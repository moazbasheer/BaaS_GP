import { Link } from "react-router-dom"
import { DateTime } from 'luxon'

function TripItem({ trip, deleteTrip }) {

  return (
    <div>
      <div>Trip for path: {trip.path_name}</div>
      {/* <div>{DateTime.fromISO(trip.date)}</div> */}
      <Link to={`${trip.id}`}>
        <button>View</button>
      </Link>
      <button onClick={deleteTrip}>Delete</button>
    </div>
  )
}

export default TripItem