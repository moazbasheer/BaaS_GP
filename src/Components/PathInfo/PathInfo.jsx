import { Duration } from 'luxon';

function PathInfo({ time, distance, price }) {
  const getTimeString = () => {
    if (time < 60000) {
      return '<1 minute.';
    }

    const d = Duration.fromMillis(time).shiftTo('hours', 'minutes', 'seconds').toObject();

    const hourPart = d.hours === 0 ? '' : `${d.hours} hour(s), `;
    const minutePart = `${d.minutes} minute(s).`;

    return hourPart + minutePart;
  };

  return (
    <div className="d-flex justify-content-between bg-primary text-white p-3 fs-5">
      <div>
        <span className="fw-bold">Estimated Time:</span>
        {' '}
        {time ? getTimeString() : '--'}
      </div>
      <div>
        <span className="fw-bold">Distance:</span>
        {' '}
        {distance ? `${(distance / 1000).toFixed(2)} km.` : '--'}
      </div>
      {price && (
        <div>
          <span className="fw-bold">Price:</span>
          {' '}
          {`${price} EGP.`}
        </div>
      )}
    </div>
  );
}

export default PathInfo;
