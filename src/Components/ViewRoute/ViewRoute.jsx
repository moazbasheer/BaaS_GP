import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import MapComponent from "../MapComponent/MapComponent"
import routeService from '../../Services/routes'
import { coordinatesToPoint, setPointStyle } from "../../Utility/map"
import VectorSource from "ol/source/Vector"
import VectorLayer from "ol/layer/Vector"

let origin, destination
const pointsVecSource = new VectorSource()
const pointsVecLayer = new VectorLayer({ source: pointsVecSource })

function ViewRoute() {
  const routeId = parseInt(useParams().id)
  const [route, setRoute] = useState()

  useEffect(() => {
    routeService.get(routeId).then(result => setRoute(result.message[0]))
  }, [])

  useEffect(() => {
    if (!route) {
      return
    }

    origin = coordinatesToPoint([route.source_latitude, route.source_longitude])
    origin.name = route.source
    origin.type = 'origin'

    destination = coordinatesToPoint([route.destination_latitude, route.destination_longitude])
    destination.name = route.destination
    destination.type = 'destination'

    const features = [origin, destination]
    features.forEach(p => setPointStyle(p))
    pointsVecSource.addFeatures(features)
  }, [route]);

  return (
    <MapComponent layers={[pointsVecLayer]} />
  )
}

export default ViewRoute;