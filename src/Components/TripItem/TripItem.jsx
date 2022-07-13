import { Link } from "react-router-dom"
import { DateTime } from 'luxon'

function TripItem({ trip, deleteTrip }) {
  const [date, time] = trip.datetime.split(' ')

  return (
    <div className="d-flex justify-content-between align-items-center">
      <div>
        <div>{DateTime.fromISO(date).toLocaleString(DateTime.DATE_FULL)}</div>
        <div>{DateTime.fromISO(time).toLocaleString(DateTime.TIME_SIMPLE)}</div>
      </div>
      <div>
        <div><span className="fw-bold">Total Price:</span> {trip.price} EGP</div>
        <div>{trip.num_seats} Passengers</div>
      </div>
      <div>{trip.public ? 'Public' : 'Private'}</div>
      <div>
        <Link className="text-center fst-italic mx-2" to={`../paths/${trip.path_id}`}>
          View Path
        </Link>
        <button className="btn btn-danger" onClick={deleteTrip}>Delete</button>
      </div>
    </div>
  )
}

export default TripItem