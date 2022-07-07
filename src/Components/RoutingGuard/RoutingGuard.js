import React from 'react'
import { Navigate} from 'react-router-dom'

const RoutingGuard= ({ Component , ...rest})=> {
    console.log("in routing Gurad" , rest ) ;
    // console.log(component);
    if (localStorage.getItem("organization"))// check if the user exist in the local storage
        return <Component {...rest} /> // note that user added to the local storage after he Log in.
    else 
    return  <Navigate replace={true} to="/login"/> 
}

export default RoutingGuard
