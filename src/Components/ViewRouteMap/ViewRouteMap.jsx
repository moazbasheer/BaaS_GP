import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import MapComponent from "../MapComponent/MapComponent"
import routeService from '../../Services/routes'
import { coordinatesToPoint, setPointStyle } from "../../Utility/map"
import VectorSource from "ol/source/Vector"
import VectorLayer from "ol/layer/Vector"

let origin, destination, extent
const pointsVecSource = new VectorSource()
const pointsVecLayer = new VectorLayer({
  source: pointsVecSource,
  style: setPointStyle
})

function ViewRouteMap({ route }) {
  const [focusExtent, setFocusExtent] = useState()

  useEffect(() => {
    if (!route) {
      return
    }

    pointsVecSource.clear()
    origin = coordinatesToPoint([route.source_latitude, route.source_longitude])
    origin.name = route.source
    origin.type = 'origin'

    destination = coordinatesToPoint([route.destination_latitude, route.destination_longitude])
    destination.name = route.destination
    destination.type = 'destination'

    const features = [origin, destination]
    pointsVecSource.addFeatures(features)

    setFocusExtent(pointsVecSource.getExtent())
  }, [route]);

  return (
    <MapComponent layers={[pointsVecLayer]} focusExtent={focusExtent} />
  )
}

export default ViewRouteMap;