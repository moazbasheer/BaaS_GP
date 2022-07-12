import { Feature, Map, View } from 'ol';
import { Point } from 'ol/geom';
import { Draw, Snap } from 'ol/interaction';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import { fromLonLat } from 'ol/proj';
import { OSM } from 'ol/source';
import VectorSource from 'ol/source/Vector';
import { Stroke, Style } from 'ol/style';
import { useEffect, useState } from 'react';
import { Polyline } from 'ol/format';
import {
  setPointStyle, pointToCoordinates, coordinatesToPoint, stringToPolyline, setPathStyle,
} from '../../Utility/map';
import pathAPIService from '../../Services/graphhopper';
import MapComponent from '../MapComponent/MapComponent';
import PathInfo from '../PathInfo/PathInfo';

let stops = [];
let origin; let destination; let
  pointType;

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
  style: setPathStyle,
});

const drawAction = new Draw({
  source: stopsVecSource,
  type: 'Point',
});

const snapAction = new Snap({
  source: stopsVecSource,
});

function CreatePathMap({ route, setNavigationResult, setStops }) {
  const [time, setTime] = useState();
  const [distance, setDistance] = useState();

  const clearStops = () => {
    stops = [];
    stopsVecSource.clear();
    drawPath();
    setStops(stops);
  };

  const clearPath = () => {
    setTime(null);
    setDistance(null);
    pathVecSource.clear();
  };

  const drawPath = async () => {
    const points = [];

    points.push(pointToCoordinates(origin));
    stops.forEach((stop) => points.push(pointToCoordinates(stop)));
    points.push(pointToCoordinates(destination));

    const result = await pathAPIService.getPath(points)
    const pathFeature = stringToPolyline(result.paths[0].points)

    // delete previous path if any
    clearPath();
    pathVecSource.addFeature(pathFeature);
    setTime(result.paths[0].time);
    setDistance(result.paths[0].distance);

    setNavigationResult(result);
  };

  const changePointType = (type) => {
    pointType = type;

    // enabling/disabling drawing
    drawAction.setActive(pointType !== 'none');
  };

  const handleDrawAction = (event) => {
    const point = event.feature;
    point.type = pointType;

    const input = window.prompt('Enter a name for the point');
    point.name = input || pointType;

    stops.push(point);
    setStops(stops);

    drawPath();
  };

  useEffect(() => {
    drawAction.on('drawstart', handleDrawAction);
    changePointType('none');
  }, []);

  useEffect(() => {
    // add origin and destination
    if (!route) {
      return;
    }

    origin = coordinatesToPoint([route.source_latitude, route.source_longitude]);
    origin.name = route.source;
    origin.type = 'origin';

    destination = coordinatesToPoint([route.destination_latitude, route.destination_longitude]);
    destination.name = route.destination;
    destination.type = 'destination';

    const features = [origin, destination];
    features.forEach((p) => setPointStyle(p));
    endpointsVecSource.addFeatures(features);

    drawPath();
  }, [route]);

  return (
    <>
      <div>
        <button onClick={() => changePointType('stop')}>Add Stops</button>
        <button onClick={() => changePointType('none')}>None</button>
      </div>
      <div>
        <button onClick={clearStops}>Clear All Stops</button>
      </div>
      <PathInfo time={time} distance={distance} />
      <MapComponent
        layers={[pathVecLayer, stopsVecLayer, endpointsVecLayer]}
        interactions={[drawAction, snapAction]}
      />
    </>
  );
}

export default CreatePathMap;
