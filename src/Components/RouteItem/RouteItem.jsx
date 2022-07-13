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
    <>
      <div className="d-flex justify-content-between align-items-center">
        <div className="fs-5">
          <div>{route.name}</div>
        </div>
        <div>
          <div><span className="fw-bold">From:</span> {origin}</div>
          <div><span className="fw-bold">To:</span> {destination}</div>
        </div>
        <div>
          <Link className="text-center fs-5 fst-italic" to={`${route.id}`}>
            View Route
          </Link>
          <Link to={`../paths/create/${route.id}`}>
            <button className="btn btn-primary mx-2">Create a New Path</button>
          </Link>
          <button className="btn btn-danger" onClick={deleteRoute}>Delete</button>
        </div>
      </div>
    </>
  )
}

export default RouteItem