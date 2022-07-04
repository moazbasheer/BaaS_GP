import { useState } from 'react';
import CreateRouteMap from '../CreateRouteMap/CreateRouteMap'
import routeService from '../../Services/routes'
import Notification from '../Notification/Notification'
import { useRef } from 'react';
import { getCoordinates } from '../../Utility/points';

function CreateRoute() {
  const [name, setName] = useState('')
  const [message, setMessage] = useState('')
  const [origin, setOrigin] = useState()
  const [destination, setDestination] = useState();

  const createRoute = async () => {
    const originCoords = getCoordinates(origin)
    const destinationCoords = getCoordinates(destination)

    const route = {
      name,
      origin: originCoords,
      destination: destinationCoords
    }
    const response = await routeService.create(route)
    if (response.status === 201) {
      setMessage('Route created successfully.')
    }
  }

  const handleNameChange = event => {
    setName(event.target.value)
  }

  return (
    <>
      <Notification message={message} setMessage={setMessage} />
      <div>
        <label>Route Name:</label> <input type="text" onChange={handleNameChange} />
      </div>
      <button onClick={createRoute}>Create Route</button>
      <CreateRouteMap setOrigin={setOrigin} setDestination={setDestination} />
    </>
  );
}

export default CreateRoute;