import { useState } from 'react';
import MapContainer from '../MapContainer/MapContainer';

function CreateRoute() {
  const [pointType, setPointType] = useState('');

  const changePointType = event => {
    const type = event.target.getAttribute('point-mode')
    setPointType(Number(type))
  }

  return (
    <div>
      <div>
        <button point-mode='1' onClick={changePointType}>Source</button>
        <button point-mode='2' onClick={changePointType}>Stop</button>
        <button point-mode='3' onClick={changePointType}>Destination</button>
        <button point-mode='0' onClick={changePointType}>None</button>
      </div>
      <MapContainer pointType={pointType} />
    </div>
  );
}

export default CreateRoute;