import { Link } from "react-router-dom"
import { DateTime } from 'luxon'
import DeleteOutlineOutlinedIcon from '@mui/icons-material/DeleteOutlineOutlined';
import CalendarTodayIcon from '@mui/icons-material/CalendarToday';
import AccessTimeIcon from '@mui/icons-material/AccessTime';
import PublicIcon from '@mui/icons-material/Public';
import PublicOffIcon from '@mui/icons-material/PublicOff';

function TripItem({ trip, deleteTrip }) {
  const [date, time] = trip.datetime.split(' ')

  return (
    <div className="d-flex justify-content-between align-items-center">
      <div>
        <div><CalendarTodayIcon/> {DateTime.fromISO(date).toLocaleString(DateTime.DATE_FULL)}</div>
        <div><AccessTimeIcon/> {DateTime.fromISO(time).toLocaleString(DateTime.TIME_SIMPLE)}</div>
      </div>
      <div>
        <div><span className="fw-bold">Total Price:</span> {trip.price} EGP</div>
        <div>{trip.num_seats} Passengers</div>
      </div>
      <div>{trip.public ? <PublicIcon /> : <PublicOffIcon />} {trip.public ? 'Public' : 'Private'}</div>
      <div>
        <Link className="text-center fst-italic mx-2" to={`../paths/${trip.path_id}`}>
          View Path
        </Link>
        <button className="btn btn-danger" onClick={deleteTrip}><DeleteOutlineOutlinedIcon /> Delete</button>
      </div>
    </div>
  )
}

export default TripItem