import React, { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import routeService from '../../Services/routes'
import pathService from '../../Services/paths'
import RouteItem from '../RouteItem/RouteItem'

function OrgRoutes() {
  const [routes, setRoutes] = useState([])
  const [paths, setPaths] = useState([])

  useEffect(() => {
    routeService.getAll().then(result => setRoutes(result))
    pathService.getAll().then(result => setPaths(result))
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
      <h2>Routes</h2>
      <ul>
        {routes.map(
          route =>
            <RouteItem key={route.id} route={route}
              paths={paths.filter(p => p.routeId === route.id)}
            />
        )}
      </ul>
    </>
  )
}

export default OrgRoutes