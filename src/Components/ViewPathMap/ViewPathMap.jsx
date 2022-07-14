import { useEffect, useState } from 'react';
import VectorSource from 'ol/source/Vector';
import VectorLayer from 'ol/layer/Vector';
import { Stroke, Style } from 'ol/style';
import MapComponent from '../MapComponent/MapComponent';
import {
  setPathStyle,
  coordinatesToPoint, pointToCoordinates, setPointStyle, stringToPolyline,
} from '../../Utility/map';
import pathAPIService from '../../Services/graphhopper';
import PathInfo from '../PathInfo/PathInfo';

let stops
let origin; let
  destination

// layer for origin and destination
const endpointsVecSource = new VectorSource();
const endpointsVecLayer = new VectorLayer({
  source: endpointsVecSource,
  style: setPointStyle
});

const stopsVecSource = new VectorSource();
const stopsVecLayer = new VectorLayer({
    source: stopsVecSource,
    style: setPointStyle
  });

const pathVecSource = new VectorSource();
const pathVecLayer = new VectorLayer({
  source: pathVecSource,
  style: setPathStyle
})

function ViewPathMap({ path }) {
  const [time, setTime] = useState()
  const [distance, setDistance] = useState()
  const [price, setPrice] = useState()
  const [pathStops, setPathStops] = useState()
  const [focusExtent, setFocusExtent] = useState();

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
    endpointsVecSource.addFeatures(features);
  }, [path]);

  useEffect(() => {
    if (!path) {
      return;
    }

    setPrice(path.price)
    setPathStops(path.stops)

    // add stops other than origin and destination
    stopsVecSource.clear()
    
    // exclude origin and destination
    stops = path.stops.slice(1, path.stops.length - 1).map((stop) => {
      const point = coordinatesToPoint([stop.latitude, stop.longitude]);
      point.type = 'stop';
      point.name = stop.name;
      return point;
    })
    stopsVecSource.addFeatures(stops);

    // draw path polyline
    pathVecSource.clear()

    console.log(path)
    const pointsCoords = path.stops.map(stop => [stop.latitude, stop.longitude])
    pathAPIService.getPath(pointsCoords).then((result) => {
      const pathFeature = stringToPolyline(result.paths[0].points)
      pathVecSource.addFeature(pathFeature)

      setTime(result.paths[0].time)
      setDistance(result.paths[0].distance)
      setFocusExtent(pathVecSource.getExtent())
    });
  }, [path]);

  return (
    <>
      <PathInfo time={time} distance={distance} price={price} stops={pathStops} />
      <MapComponent layers={[pathVecLayer, stopsVecLayer, endpointsVecLayer]} focusExtent={focusExtent} />
    </>
  );
}

export default ViewPathMap;
