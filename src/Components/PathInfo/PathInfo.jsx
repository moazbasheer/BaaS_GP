import { Duration } from 'luxon'

function PathInfo(props) {
  const getTimeString = time => {
    if (time < 60000) {
      return '<1 minute.'
    }
    else {
      const d = Duration.fromMillis(time).shiftTo('hours', 'minutes', 'seconds').toObject()

      const hourPart = d.hours === 0 ? '' : `${d.hours} hour(s), `
      const minutePart = `${d.minutes} minute(s).`

      return hourPart + minutePart
    }
  }

  return (
    <div>
      <div>Estimated Time: {props.time ? getTimeString(props.time) : '--'}</div>
      <div>Distance: {props.distance ? `${(props.distance / 1000).toFixed(2)} km` : '--'}</div>
      <div>Price: {props.distance ? `${props.price} EGP` : '--'}</div>
      <div>
        <div>Stops</div>
        {props.stops?.map((stop, i) => (
          <div key={i}>{stop.name}</div>
        ))}
      </div>
    </div>
  );
}

export default PathInfo;