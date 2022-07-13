import { useEffect, useState } from "react"
import { Link , useOutletContext} from "react-router-dom"
import TripItem from "../TripItem/TripItem"
import tripService from '../../Services/trips'
import {  } from "react-router-dom";
import PageTitle from "../PageTitle/PageTitle";
function Trips() {
  const [trips, setTrips] = useState([]);

  useEffect(() => {
    tripService.getAll().then(
      (result) => {
        const sortedTrips = result.message

        // sort chronologically
        sortedTrips.sort((a, b) => (
          new Date(b.datetime) - new Date(a.datetime)
        )).reverse()

        setTrips(sortedTrips)
      }
    );
  }, []);

  const deleteTrip = id => {
    tripService.deleteTrip(id).then(() => setTrips(trips.filter(trip => trip.id !== id)));
  };

  const getTripItem = trip => (
    <li className='list-group-item list-group-item-action' key={trip.id}>
      <TripItem trip={trip} deleteTrip={() => deleteTrip(trip.id)} />
    </li>
  )

  console.log(trips)
  return (
    <>
      <PageTitle title={'Trips'} />
      <div className="mb-3">
        <Link to="create">
          <button className="btn btn-outline-primary">
            Create a New Trip
          </button>
        </Link>
      </div>
      <ul className='list-group'>
        {trips.map(trip => getTripItem(trip))}
      </ul>
    </>
  )
}

export default Trips