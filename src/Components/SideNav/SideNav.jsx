import './SideNav.css';
import { NavLink } from 'react-router-dom';

function SideNav() {
  const commonStyle = 'fs-4 p-3 text-white navlink';
  const inactiveStyle = `${commonStyle} inactive`;
  const activeStyle = `${commonStyle} fs-1 active`;

  return (
    <nav className="d-flex flex-column pt-3 link-white">
      <NavLink to="home" className={({ isActive }) => (isActive ? activeStyle : inactiveStyle)}>Home</NavLink>
      <NavLink to="passengers" className={({ isActive }) => (isActive ? activeStyle : inactiveStyle)}>Passenger</NavLink>
      <NavLink to="routes" className={({ isActive }) => (isActive ? activeStyle : inactiveStyle)}>Routes</NavLink>
      <NavLink to="trips" className={({ isActive }) => (isActive ? activeStyle : inactiveStyle)}>Trips</NavLink>
    </nav>
  );
}

export default SideNav;
