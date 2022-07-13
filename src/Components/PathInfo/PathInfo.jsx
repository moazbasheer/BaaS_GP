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
    <div className='d-flex justify-content-between bg-primary text-white p-3 fs-5'>
      <div><span className="fw-bold">Estimated Time:</span> {props.time ? getTimeString(props.time) : '--'}</div>
      <div><span className="fw-bold">Distance:</span> {props.distance ? `${(props.distance / 1000).toFixed(2)} km.` : '--'}</div>
      {props.price && <div><span className="fw-bold">Price:</span> {`${props.price} EGP.`}</div>}
    </div>
  );
}

export default PathInfo;