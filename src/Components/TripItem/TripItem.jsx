import { Link } from "react-router-dom"
import { DateTime } from 'luxon'

function TripItem({ trip, deleteTrip }) {
  const [date, time] = trip.datetime.split(' ')

  return (
    <div>
      <div>{DateTime.fromISO(date).toLocaleString(DateTime.DATE_FULL)}</div>
      <div>{DateTime.fromISO(time).toLocaleString(DateTime.TIME_SIMPLE)}</div>
      <div>Capacity: {trip.num_seats}</div>
      <div>Price: {trip.price} EGP</div>
      <div>{trip.public ? 'Public' : 'Private'}</div>
      <Link to={`../paths/${trip.path_id}`}>
        <button>View Path</button>
      </Link>
      <button onClick={deleteTrip}>Delete</button>
    </div>
  )
}

export default TripItem