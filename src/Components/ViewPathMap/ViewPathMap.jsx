import { useEffect, useState } from 'react';
import VectorSource from 'ol/source/Vector';
import VectorLayer from 'ol/layer/Vector';
import { Stroke, Style } from 'ol/style';
import MapComponent from '../MapComponent/MapComponent';
import {
  coordinatesToPoint, pointToCoordinates, setPointStyle, stringToPolyline,
} from '../../Utility/map';
import pathAPIService from '../../Services/graphhopper';
import PathInfo from '../PathInfo/PathInfo';

let stops = []
let origin; let
  destination

// layer for origin and destination
const endpointsVecSource = new VectorSource();
const endpointsVecLayer = new VectorLayer({ source: endpointsVecSource });

const stopsVecSource = new VectorSource();
const stopsVecLayer = new VectorLayer({ source: stopsVecSource });

const pathVecSource = new VectorSource();
const pathVecLayer = new VectorLayer({
  source: pathVecSource,
  style: new Style({
    stroke: new Stroke({
      color: '#00688bBB',
      width: 7,
    }),
  }),
});

function ViewPathMap({ path }) {
  const [time, setTime] = useState()
  const [distance, setDistance] = useState()
  const [focusGeometry, setFocusGeometry] = useState();

  useEffect(() => {
    if (!path) {
      return;
    }

    // clear source to delete possible previously drawn features
    endpointsVecSource.clear()

    const originCoords = [path.stops[0].latitude, path.stops[0].longitude];
    origin = coordinatesToPoint(originCoords);
    origin.type = 'origin';
    origin.name = path.stops[0].name;

    const n = path.stops.length - 1;
    const destinationCoords = [path.stops[n].latitude, path.stops[n].longitude];
    destination = coordinatesToPoint(destinationCoords);
    destination.type = 'destination';
    destination.name = path.stops[n].name;

    const features = [origin, destination];
    features.forEach((p) => setPointStyle(p));
    endpointsVecSource.addFeatures(features);
  }, [path]);

  useEffect(() => {
    if (!path) {
      return;
    }

    // add stops other than origin and destination
    stopsVecSource.clear()
    
    // exclude origin and destination
    stops = path.stops.slice(1, path.stops.length - 1).map((stop) => {
      const point = coordinatesToPoint([stop.latitude, stop.longitude]);
      point.type = 'stop';
      point.name = stop.name;
      return point;
    })
    stops.forEach((stop) => setPointStyle(stop));
    stopsVecSource.addFeatures(stops);

    // draw path polyline
    pathVecSource.clear()

    const pointsCoords = path.stops.map(stop => [stop.latitude, stop.longitude])
    console.log(pointsCoords)
    pathAPIService.getPath(pointsCoords).then((result) => {
      const pathFeature = stringToPolyline(result.paths[0].points)
      pathVecSource.addFeature(pathFeature)
      setTime(result.paths[0].time)
      setDistance(result.paths[0].distance)
      setFocusGeometry(pathFeature.getGeometry())
    });
  }, [path]);

  return (
    <>
      <PathInfo time={time} distance={distance} />
      <MapComponent layers={[pathVecLayer, stopsVecLayer, endpointsVecLayer]} focusGeometry={focusGeometry} />
    </>
  );
}

export default ViewPathMap;
