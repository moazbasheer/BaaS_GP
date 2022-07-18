import { Navigate } from 'react-router-dom';

function RoutingGuard({ Component, ...rest }) {
  console.log('in routing Gurad', rest);
  // console.log(component);

  // check if the user exist in the local storage
  if (localStorage.getItem('organization')) {
    return <Component {...rest} />;
  } // note that user added to the local storage after he Log in.
  return <Navigate replace to="/login" />;
}

export default RoutingGuard;
