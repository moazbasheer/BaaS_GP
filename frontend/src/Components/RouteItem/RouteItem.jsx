import { Link } from "react-router-dom"
import PathItem from "../PathItem/PathItem"

function RouteItem({ route, deleteRoute }) {
  return (
    <div>
      {route.name}
      <Link to={`../create-path/${route.id}`}>
        <button>Create Path</button>
      </Link>
      <Link to={`../routes/${route.id}`}>
        <button>View</button>
      </Link>
      <button onClick={deleteRoute}>Delete</button>
    </div>
  )
}

export default RouteItem