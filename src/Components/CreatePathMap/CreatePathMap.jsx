import { Feature, Map, View } from "ol";
import { Point } from "ol/geom";
import { Draw, Snap } from "ol/interaction";
import TileLayer from "ol/layer/Tile";
import VectorLayer from "ol/layer/Vector";
import { fromLonLat } from "ol/proj";
import { OSM } from "ol/source";
import VectorSource from "ol/source/Vector";
import { Stroke, Style } from "ol/style"
import { useEffect, useState } from "react"
import { setPointStyle, getCoordinates, coordinatesToPoint, stringToPolyline } from '../../Utility/map'
import pathAPIService from '../../Services/graphhopper'
import { Polyline } from "ol/format";
import MapComponent from "../MapComponent/MapComponent";

let stops = []
let origin, destination

// layer for origin and destination
const endpointsVecSource = new VectorSource()
const endpointsVecLayer = new VectorLayer({ source: endpointsVecSource })

const stopsVecSource = new VectorSource()
const stopsVecLayer = new VectorLayer({ source: stopsVecSource })

const pathVecSource = new VectorSource()
const pathVecLayer = new VectorLayer({
  source: pathVecSource,
  style: new Style({
    stroke: new Stroke({
      color: '#00688bBB',
      width: 7
    })
  })
})

const drawAction = new Draw({
  source: stopsVecSource,
  type: 'Point'
})

const snap = new Snap({
  source: stopsVecSource
})

function CreatePathMap({ route, setNavigationResult, setStops }) {
  const [time, setTime] = useState()
  const [distance, setDistance] = useState()

  const clearStops = () => {
    stops = []
    stopsVecSource.clear()
    drawPath()
    setStops(stops)
  }

  const clearPath = () => {
    setTime(null)
    setDistance(null)
    pathVecSource.clear()
  }

  const getTimeString = () => {
    const seconds = time / 1000
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds - hours * 3600) / 60)

    const list = []
    if (hours > 0) {
      list.push(`${hours} hour(s)`)
    }

    if (minutes > 0) {
      list.push(`${minutes} minute(s)`)
    }

    if (list.length > 0) {
      return list.join(', ')
    }
    else {
      return '<1 minute'
    }
  }

  const drawPath = async () => {
    let points = []

    points.push(getCoordinates(origin))
    stops.forEach(stop => points.push(getCoordinates(stop)))
    points.push(getCoordinates(destination))

    const result = await pathAPIService.getPath(points)
    const pathFeature = stringToPolyline(result.paths[0].points)

    // delete previous path if any
    clearPath()
    pathVecSource.addFeature(pathFeature)
    setTime(result.paths[0].time)
    setDistance(result.paths[0].distance)

    setNavigationResult(result)
  }

  const handleStopDraw = event => {
    const point = event.feature
    point.type = 'stop'

    stops.push(point)
    stops.forEach((stop, i) => stop.index = i + 1)
    setStops(stops)

    setPointStyle(point)
    drawPath()
  }

  const getDistanceString = () => {
    return (distance / 1000).toFixed(2) + ' km'
  }

  const setDrawing = value => {
    drawAction.setActive(value)
  }

  useEffect(() => {
    drawAction.on('drawstart', handleStopDraw)
    setDrawing(false)
  }, [])

  useEffect(() => {
    // add origin and destination
    if (!route) {
      return
    }

    origin = coordinatesToPoint(route.origin)
    origin.type = 'origin'

    destination = coordinatesToPoint(route.destination)
    destination.type = 'destination'

    const features = [origin, destination]
    features.forEach(p => setPointStyle(p))
    endpointsVecSource.addFeatures(features)

    drawPath()
  }, [route]);

  return (
    <>
      <div>
        <button onClick={() => setDrawing(true)}>Add Stops</button>
        <button onClick={() => setDrawing(false)}>None</button>
      </div>
      <div>
        <button onClick={clearStops}>Clear All Stops</button>
      </div>
      <div>
        <p>Estimated Time: {time ? getTimeString() : '--'}</p>
        <p>Distance: {distance ? getDistanceString() : '--'}</p>
      </div>
      <MapComponent
        layers={[pathVecLayer, stopsVecLayer, endpointsVecLayer]}
        interactions={[drawAction, snap]}
      />
    </>
  );
}

export default CreatePathMap