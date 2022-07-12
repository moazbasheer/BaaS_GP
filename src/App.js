import './App.css';
import Login from './Components/Login/login';
import Register from './Components/Register/Register';
import { Navigate, Route, Routes, useNavigate } from 'react-router-dom';
import NotFound from './Components/Not Found/NotFound';
import { useEffect, useState } from 'react';
import MainInterface from './Components/MainInterface/MainInterface';
import RoutingGuard from './Components/RoutingGuard/RoutingGuard';
import Employees from './Components/Employees/Employees';
import OrgRoutes from './Components/OrgRoutes/OrgRoutes';
import CreateRoute from './Components/CreateRoute/CreateRoute';
import CreatePath from './Components/CreatePath/CreatePath';
import ViewRoute from './Components/ViewRoute/ViewRoute';
import ViewPath from './Components/ViewPath/ViewPath';
import CreateTrip from './Components/CreateTrip/CreateTrip';
import Trips from './Components/Trips/Trips';
import Home from './Components/Home/Home';

function App() {
  const [loggedOrg, setLoggedOrg] = useState({});
  const navigate = useNavigate();

  function logout() {
    localStorage.removeItem('organization');
    setLoggedOrg(null);
    navigate('/login');
  }

  useEffect(() => {
    setLoggedOrg(JSON.parse(localStorage.getItem('organization'))?.info);
  }, []);

  return (
    <>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />

        <Route path="/" element={(<RoutingGuard logOut={logout} Component={MainInterface} />)}>
          <Route path="home" element={<Home />} />
          <Route path="passengers" element={<Employees />} />
          <Route path="routes/create" element={<CreateRoute />} />
          <Route path="routes/:id" element={<ViewRoute />} />
          <Route path="routes" element={<OrgRoutes />} />
          <Route path="paths/create/:id" element={<CreatePath />} />
          <Route path="paths/:id" element={<ViewPath />} />
          <Route path="trips/create" element={<CreateTrip />} />
          <Route path="trips" element={<Trips />} />
        </Route>
        <Route path="*" element={<NotFound />} />
      </Routes>
      <div className="App bg-success">

        {/* <Register></Register> */}
        {/* </Login> */}
      </div>
    </>
  );
}

export default App;
