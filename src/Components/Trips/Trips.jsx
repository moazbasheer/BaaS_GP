import { useEffect, useState } from "react"
import { Link , useOutletContext} from "react-router-dom"
import TripItem from "../TripItem/TripItem"
import tripService from '../../Services/trips'
import {  } from "react-router-dom";
function Trips() {
  const [trips, setTrips] = useState([]);
  const [wallet, setWallet] = useOutletContext();
  console.log("wallet in trips is "+wallet);
  useEffect(() => {
    tripService.getAll().then((result) => setTrips(result.message));
  }, []);

  const deleteTrip = id => {
    tripService.deleteTrip(id).then(() => setTrips(trips.filter(trip => trip.id !== id)));
  };

  const getTripItem = trip => (
    <li key={trip.id}>
      <TripItem trip={trip} deleteTrip={() => deleteTrip(trip.id)} />
    </li>
  )


  console.log(trips)
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