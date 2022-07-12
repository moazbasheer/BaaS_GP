import { useEffect, useState } from "react"
import { Link } from "react-router-dom"
import { getCoordinatesAddress } from "../../Utility/misc"

function RouteItem({ route, deleteRoute }) {
  const [origin, setOrigin] = useState()
  const [destination, setDestination] = useState()

  useEffect(() => {
    const setAddresses = async () => {
      setOrigin(await getCoordinatesAddress([route.source_latitude, route.source_longitude]))
      setDestination(await getCoordinatesAddress([route.destination_latitude, route.destination_longitude]))
    }

    setAddresses()
  }, []);

  return (
    <div>
      <div>{route.name}</div>
      <div>Origin: {origin}</div>
      <div>Destination: {destination}</div>
      <Link to={`../paths/create/${route.id}`}>
        <button>Create Path</button>
      </Link>
      <Link to={`${route.id}`}>
        <button>View</button>
      </Link>
      <button onClick={deleteRoute}>Delete</button>
    </div>
  )
}

export default RouteItem