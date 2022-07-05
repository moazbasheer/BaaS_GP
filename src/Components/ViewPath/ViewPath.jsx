import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import MapComponent from "../MapComponent/MapComponent"
import pathService from '../../Services/paths'
import routeService from '../../Services/routes'
import { coordinatesToPoint, setPointStyle, stringToPolyline } from "../../Utility/map"
import VectorSource from "ol/source/Vector"
import VectorLayer from "ol/layer/Vector"
import { Stroke, Style } from "ol/style"

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

function ViewPath() {
  const pathId = parseInt(useParams().id)
  const [path, setPath] = useState()
  const [route, setRoute] = useState()

  useEffect(() => {
    pathService.get(pathId).then(result => {
      setPath(result)
      routeService.get(result.routeId).then(
        r => setRoute(r)
      )
    })
  }, [])

  useEffect(() => {
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
  }, [route])

  useEffect(() => {
    if (!path) {
      return
    }

    stops = path.stops.map(s => {
      const p = coordinatesToPoint(s)
      p.type = 'stop'
      return p
    })
    stops.forEach((s, i) => {
      s.index = i + 1
      setPointStyle(s)
    })
    stopsVecSource.addFeatures(stops)

    const pathFeature = stringToPolyline(path.path)
    pathVecSource.addFeature(pathFeature)
  }, [path]);

  return (
    <MapComponent layers={[pathVecLayer, stopsVecLayer, endpointsVecLayer]} />
  )
}

export default ViewPath;