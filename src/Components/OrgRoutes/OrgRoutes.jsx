import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import routeService from '../../Services/routes';
import pathService from '../../Services/paths';
import RouteItem from '../RouteItem/RouteItem';
import PathItem from '../PathItem/PathItem';
import PageTitle from '../PageTitle/PageTitle';


function OrgRoutes() {
  const [routes, setRoutes] = useState([]);
  const [paths, setPaths] = useState([]);

  useEffect(() => {
    routeService.getAll().then((result) => setRoutes(result.message));
    pathService.getAll().then((result) => setPaths(result.message));
  }, []);

  const deletePath = (id) => {
    pathService.deletePath(id).then(() => setPaths(paths.filter((path) => path.id !== id)));
  };

  const deleteRoute = (id) => {
    routeService.deleteRoute(id).then(() => {
      setRoutes(routes.filter((route) => route.id !== id));
      setPaths(paths.filter((path) => path.routeId !== id));
    });
  };

  const getPathItem = (path) => (
    <li key={path.id} className='list-group-item list-group-item-action'>
      <PathItem key={path.id} path={path} deletePath={() => deletePath(path.id)} />
    </li>
  );

  const getRouteItem = (route) => (
    <li key={route.id} className='list-group-item list-group-item-action'>
      <RouteItem key={route.id} route={route} deleteRoute={() => deleteRoute(route.id)} />
      <ul className='list-group'>
        {paths.filter((path) => parseInt(path.route_id) === route.id).map((path) => getPathItem(path))}
      </ul>
    </li>
  )

  console.log(paths)

  return (
    <>
      <PageTitle title={'Routes'} />
      <div>
        <Link to="create">
          <button className='btn btn-outline-primary'>
            Create a New Route
          </button>
        </Link>
      </div>
      <ul className='list-group'>
        {routes.map((route) => getRouteItem(route))}
      </ul>
    </>
  );
}

export default OrgRoutes;
