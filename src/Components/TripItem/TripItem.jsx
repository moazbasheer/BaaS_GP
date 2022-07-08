import { Link } from "react-router-dom"

function TripItem({ trip, deleteTrip }) {
  return (
    <div>
      {trip.name}
      <Link to={`${trip.id}`}>
        <button>View</button>
      </Link>
      <button onClick={deleteTrip}>Delete</button>
    </div>
  )
}

export default TripItem