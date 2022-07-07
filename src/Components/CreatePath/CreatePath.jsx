import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import routeService from '../../Services/routes'
import { pointToCoordinates } from "../../Utility/map";
import CreatePathMap from "../CreatePathMap/CreatePathMap"
import Notification from "../Notification/Notification"
import pathService from '../../Services/paths'

function CreatePath() {
  const routeId = parseInt(useParams().id)

  const [name, setName] = useState()
  const [route, setRoute] = useState()
  const [message, setMessage] = useState()
  const [stops, setStops] = useState([])
  const [navigationResult, setNavigationResult] = useState()

  const createPath = async () => {
    if (!navigationResult) {
      setMessage('Please construct a path first.')
      return
    }
    if(!name) {
      setMessage('Please enter a name for the path.')
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
      <Notification message={message} setMessage={setMessage} />
      <div>
        <label>Path Name:</label> <input type="text" onChange={handleNameChange} />
      </div>
      <button onClick={createPath}>Create Path</button>
      <CreatePathMap route={route} setNavigationResult={setNavigationResult} setStops={setStops} />
    </>
  );
}

export default CreatePath;