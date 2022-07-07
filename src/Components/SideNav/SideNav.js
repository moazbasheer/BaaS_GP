import React from 'react';
import { NavLink } from 'react-router-dom';
import style from './SideNav.module.css';

function SideNav() {
  return (
    <nav className={`d-flex flex-column text-white ${style.sideNav}`}>
      <NavLink to="statistics" className={({ isActive }) => (isActive ? `${style.activeLink}` : '')}> Statistics </NavLink>
      <NavLink to="passengers" className={({ isActive }) => (isActive ? style.activeLink : '')}> Passenger </NavLink>
      <NavLink to="routes" className={({ isActive }) => (isActive ? style.activeLink : '')}> Routes </NavLink>
    </nav>
  );
}

export default SideNav;
