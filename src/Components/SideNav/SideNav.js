import React from 'react';
import style from "./SideNav.module.css";
import { NavLink } from "react-router-dom";

function SideNav() {
  return (
    <nav className={ `d-flex flex-column text-white ${style.sideNav}`}>
    <NavLink to="statistics"  className={ ({isActive}) => (isActive ? `${style.activeLink}` : `` )  } > Statistics </NavLink>
    <NavLink to="passengers" className={ ({isActive}) => (isActive ? style.activeLink : `` )  }> Passenger </NavLink>
    <NavLink to="routes" className={ ({isActive}) => (isActive ? style.activeLink : "" )  }> Routes </NavLink>
    <NavLink to="trips" className={ ({isActive}) => (isActive ? style.activeLink : "" )  }> Trips </NavLink>
  </nav>
  )
}

export default SideNav
