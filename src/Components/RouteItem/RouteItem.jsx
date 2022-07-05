import { Link } from "react-router-dom"

function RouteItem({ route, paths }) {
  return (
    <li>
      {route.name}
      <Link to={`../create-path/${route.id}`}>
        <button>Create Path</button>
      </Link>
      <Link to={`../routes/${route.id}`}>
        <button>View</button>
      </Link>
      <ul>
        {paths.map(p =>
          <li key={p.id}>
            {p.name}
            <Link to={`../paths/${p.id}`}>
              <button>View</button>
            </Link>
          </li>)
        }
      </ul>
    </li>
  )
}

export default RouteItem