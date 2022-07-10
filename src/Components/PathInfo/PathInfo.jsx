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
      <p>Estimated Time: {props.time ? getTimeString(props.time) : '--'}</p>
      <p>Distance: {props.distance ? `${(props.distance / 1000).toFixed(2)} km` : '--'}</p>
    </div>
  );
}

export default PathInfo;