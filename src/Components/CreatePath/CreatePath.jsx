import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import routeService from '../../Services/routes'
import { pointToCoordinates } from "../../Utility/map";
import CreatePathMap from "../CreatePathMap/CreatePathMap"
import pathService from '../../Services/paths'
import PageTitle from "../PageTitle/PageTitle";
import { Alert } from "@mui/material";

function CreatePath() {
  const routeId = parseInt(useParams().id)

  const [name, setName] = useState()
  const [route, setRoute] = useState()
  const [message, setMessage] = useState()
  const [stops, setStops] = useState([])
  const [navigationResult, setNavigationResult] = useState()

  const createPath = async () => {
    if (!navigationResult) {
      setMessage({content: 'Please construct a path first.', type: 'error'})
      return
    }
    if(!name) {
      setMessage({content: 'Please enter a name for the path.', type: 'error'})
      return
    }

    let requestStops = stops.map(s => {
      const coords = pointToCoordinates(s)
      return {
        name: s.name,
        latitude: coords[0],
        longitude: coords[1]
      }
    })

    // add origin and destination to stops
    console.log(route)
    const origin = {
      name: route.source,
      latitude: route.source_latitude,
      longitude: route.source_longitude
    }
    const destination = {
      name: route.destination,
      latitude: route.destination_latitude,
      longitude: route.destination_longitude
    }

    requestStops = [origin, ...requestStops, destination]

    const request = {
      route_id: routeId,
      path: {
        path_name: name,
        stops: requestStops
      }
    }
    console.log(request)

    const response = await pathService.create(request)
    if (response.status === 200) {
      setMessage('Path created successfully.')
      console.log(response)
    }
  }

  const handleNameChange = event => {
    setName(event.target.value)
  }

  useEffect(() => {
    routeService.get(routeId).then(result => setRoute(result.message[0]))
  }, []);

  return (
    <>
      {route && <PageTitle title={`Create a Path for '${route.name}' Route`}/>}
      { message && <Alert severity={message.type}>{message.content}</Alert>}
      <div className='d-flex justify-content-between align-items-end mb-3'>
        <div className='d-flex gap-1 align-items-center'>
          <div><label className='form-label fw-bold'>Path Name:</label></div>
          <div><input type="text" placeholder='Path Name' onChange={handleNameChange} className='form-control' /></div>
        </div>
        <div><button onClick={createPath} className='btn btn-primary'>Create Path</button></div>
      </div>
      <CreatePathMap route={route} setNavigationResult={setNavigationResult} setStops={setStops} />
    </>
  );
}

export default CreatePath;