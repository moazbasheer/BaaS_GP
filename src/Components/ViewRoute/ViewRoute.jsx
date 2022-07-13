import { useEffect, useState } from "react"
import { useParams } from "react-router-dom"
import routeService from '../../Services/routes'
import { getCoordinatesAddress } from "../../Utility/misc"
import PageTitle from "../PageTitle/PageTitle"
import ViewRouteMap from "../ViewRouteMap/ViewRouteMap"

function ViewRoute() {
  const routeId = parseInt(useParams().id)

  const [origin, setOrigin] = useState()
  const [destination, setDestination] = useState()
  const [route, setRoute] = useState()

  useEffect(() => {
    if (!route) {
      return
    }

    const setAddresses = async () => {
      setOrigin(await getCoordinatesAddress([route.source_latitude, route.source_longitude]))
      setDestination(await getCoordinatesAddress([route.destination_latitude, route.destination_longitude]))
    }

    setAddresses()
  }, [route]);

  useEffect(() => {
    routeService.get(routeId).then(result => setRoute(result.message[0]))
  }, [])

  return (
    <>
      {route && <PageTitle title={`Viewing '${route.name}' Map.`} />}
      <div className="bg-primary text-white p-3 fs-5">
        <div><span className="fw-bold">From:</span> {origin}</div>
        <div><span className="fw-bold">To:</span> {destination}</div>
      </div>
      <ViewRouteMap route={route} />
    </>
  )
}

export default ViewRoute;