import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import routeService from '../../Services/routes'
import ViewRouteMap from "../ViewRouteMap/ViewRouteMap"

function ViewRoute() {
  const routeId = parseInt(useParams().id)
  const [route, setRoute] = useState()

  useEffect(() => {
    routeService.get(routeId).then(result => setRoute(result.message[0]))
  }, [])

  return (
    <ViewRouteMap route={route} />
  )
}

export default ViewRoute;