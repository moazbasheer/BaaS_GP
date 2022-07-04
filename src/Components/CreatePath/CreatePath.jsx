import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import routeService from '../../Services/routes'
import { getCoordinates } from "../../Utility/points";
import CreatePathMap from "../CreatePathMap/CreatePathMap"
import Notification from "../Notification/Notification"
import pathService from '../../Services/paths'

function CreatePath() {
  const routeId = useParams().id

  const [name, setName] = useState()
  const [route, setRoute] = useState()
  const [message, setMessage] = useState()
  const [stops, setStops] = useState([])
  const [navigationResult, setNavigationResult] = useState();

  const createPath = async () => {
    const stopsCoords = stops.map(s => getCoordinates(s))
    const path = navigationResult.paths[0]

    const request = {
      routeId,
      name,
      stops: stopsCoords,
      path: path.points,
      time: path.time,
      distance: path.distance
    }

    const response = await pathService.create(request)
    if (response.status === 201) {
      setMessage('Path created successfully.')
    }
  }

  const handleNameChange = event => {
    setName(event.target.value)
  }

  useEffect(() => {
    routeService.get(routeId).then(res => setRoute(res))
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