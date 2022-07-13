import { useState } from 'react';
import CreateRouteMap from '../CreateRouteMap/CreateRouteMap'
import routeService from '../../Services/routes'
import { pointToCoordinates } from '../../Utility/map';
import PageTitle from '../PageTitle/PageTitle';
import { Alert } from '@mui/material';

function CreateRoute() {
  const [name, setName] = useState('')
  const [message, setMessage] = useState()
  const [endpoints, setEndpoints] = useState({})

  const createRoute = async () => {
    if (!endpoints.origin || !endpoints.destination) {
      setMessage(
        {
          content: 'Please specify an origin AND a destination.',
          type: 'error'
        }
      )
      return
    }
    if (!name) {
      setMessage(
        {
          content: 'Please enter a name for the route.',
          type: 'error'
        }
      )
      return
    }

    const originCoords = pointToCoordinates(endpoints.origin)
    const destinationCoords = pointToCoordinates(endpoints.destination)
    const route = {
      name,
      source_name: endpoints.origin.name,
      source_latitude: originCoords[0],
      source_longitude: originCoords[1],
      destination_name: endpoints.destination.name,
      destination_latitude: destinationCoords[0],
      destination_longitude: destinationCoords[1]
    }
    console.log(route)

    const response = await routeService.create(route)
    if (response.status === 200) {
      setMessage(
        {
          content: 'Route created successfully.',
          type: 'success'
        }
      )
    }
  }

  const handleNameChange = event => {
    setName(event.target.value)
  }

  return (
    <>
      <PageTitle title={'Create a New Route'} />
      <div className='my-1'>
        {message && <Alert severity={message.type}>{message.content}</Alert>}
      </div>
      <div className='d-flex justify-content-between align-items-end mb-3'>
        <div className='d-flex gap-1 align-items-center'>
          <div><label className='form-label fw-bold'>Route Name:</label></div>
          <div><input type="text" placeholder='Route Name' onChange={handleNameChange} className='form-control' /></div>
        </div>
        <div><button onClick={createRoute} className='btn btn-primary'>Create Route</button></div>
      </div>
      <CreateRouteMap setEndpoints={setEndpoints} />
    </>
  );
}

export default CreateRoute;