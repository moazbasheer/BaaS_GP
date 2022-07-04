import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import routeService from '../../Services/routes'

function OrgRoutes() {
  const [routes, setRoutes] = useState([])

  useEffect(() => {
    routeService.getAll().then(result => setRoutes(result))
  }, []);

  return (
    <>
      <div>
        <Link to='../create-route'>
          <button className='btn btn-success mx-2'>
            Create New Route
          </button>
        </Link>
      </div>
      <ul>
        {routes.map(route => 
          <li key={route.id}>
            {route.name}
            <Link to={`../create-path/${route.id}`}>
              <button>Create Path</button>
            </Link>
          </li>)
        }
      </ul>
    </>
  )
}

export default OrgRoutes