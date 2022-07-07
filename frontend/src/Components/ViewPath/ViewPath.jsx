import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import VectorSource from 'ol/source/Vector';
import VectorLayer from 'ol/layer/Vector';
import { Stroke, Style } from 'ol/style';
import MapComponent from '../MapComponent/MapComponent';
import pathService from '../../Services/paths';
import routeService from '../../Services/routes';
import {
  coordinatesToPoint, pointToCoordinates, setPointStyle, stringToPolyline,
} from '../../Utility/map';
import pathAPIService from '../../Services/graphhopper';
import PathInfo from '../PathInfo/PathInfo';

let stops = [];
let origin; let
  destination;

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

function ViewPath() {
  const pathId = parseInt(useParams().id);
  const [path, setPath] = useState();
  const [time, setTime] = useState();
  const [distance, setDistance] = useState();

  useEffect(() => {
    pathService.get(pathId).then((result) => {
      setPath(result.message);
      console.log(result.message);
    });
  }, []);

  useEffect(() => {
    if (!path) {
      return;
    }

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

    stops = path.stops.map((stop) => {
      const point = coordinatesToPoint([stop.latitude, stop.longitude]);
      point.type = 'stop';
      point.name = stop.name;
      return point;
    });
    stops.forEach((stop) => setPointStyle(stop));
    stopsVecSource.addFeatures(stops);

    const pointsCoords = stops.map((stop) => pointToCoordinates(stop));
    pathAPIService.getPath(pointsCoords).then((result) => {
      console.log(result);
      const pathFeature = stringToPolyline(result.paths[0].points);
      pathVecSource.addFeature(pathFeature);
      setTime(result.paths[0].time);
      setDistance(result.paths[0].distance);
    });
  }, [path]);

  return (
    <>
      <PathInfo time={time} distance={distance} />
      <MapComponent layers={[pathVecLayer, stopsVecLayer, endpointsVecLayer]} />
    </>
  );
}

export default ViewPath;
