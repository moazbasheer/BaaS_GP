import { useEffect, useState } from "react"
import { Link } from "react-router-dom"
import TripItem from "../TripItem/TripItem"
import tripService from '../../Services/trips'

function Trips() {
  const [trips, setTrips] = useState([])

  useEffect(() => {
    tripService.getAll().then((result) => setTrips(result.message));
  }, []);

  const deleteTrip = id => {
    tripService.deleteTrip(id).then(() => setTrips(trips.filter(trip => trip.id !== id)));
  };

  const getTripItem = trip => (
    <li key={trip.id}>
      <TripItem key={trip.id} deleteTrip={() => deleteTrip(trip.id)} />
    </li>
  )

  return (
    <>
      <div>
        <Link to="create">
          <button>
            Create New Trip
          </button>
        </Link>
      </div>
      <h2>Trips</h2>
      <ul>
        {trips.map(trip => getTripItem(trip))}
      </ul>
    </>
  )
}

export default Trips