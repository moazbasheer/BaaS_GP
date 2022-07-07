import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import routeService from '../../Services/routes';
import pathService from '../../Services/paths';
import RouteItem from '../RouteItem/RouteItem';
import PathItem from '../PathItem/PathItem';

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
    <li key={path.id}>
      <PathItem key={path.id} path={path} deletePath={() => deletePath(path.id)} />
    </li>
  );

  const getRouteItem = (route) => (
    <li key={route.id}>
      <RouteItem key={route.id} route={route} deleteRoute={() => deleteRoute(route.id)} />
      <ul>
        {paths.filter((path) => parseInt(path.route_id) === route.id).map((path) => getPathItem(path))}
      </ul>
    </li>
  );

  return (
    <>
      <div>
        <Link to="../create-route">
          <button className="btn btn-success mx-2">
            Create New Route
          </button>
        </Link>
      </div>
      <h2>Routes</h2>
      <ul>
        {routes.map((route) => getRouteItem(route))}
      </ul>
    </>
  );
}

export default OrgRoutes;
