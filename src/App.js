import './App.css';
import Login from './Components/Login/login';
import Register from './Components/Register/Register';
import Home from './Components/Home/Home';
import { Navigate, Route, Routes, useNavigate } from 'react-router-dom';
import NotFound from './Components/Not Found/NotFound';
import { useEffect, useState } from 'react';
import RoutingGuard from './Components/RoutingGuard/RoutingGuard';
import Statistics from './Components/Statistics/Statistics';
import Employees from './Components/Employees/Employees';
import OrgRoutes from './Components/OrgRoutes/OrgRoutes';
import CreateRoute from './Components/CreateRoute/CreateRoute';
import CreatePath from './Components/CreatePath/CreatePath';
import ViewRoute from './Components/ViewRoute/ViewRoute';
import ViewPath from './Components/ViewPath/ViewPath';
import CreateTrip from './Components/CreateTrip/CreateTrip';

function App() {
  const [loggedOrg, setLoggedOrg] = useState({});
  const navigate = useNavigate();

  function logout() {
    localStorage.removeItem("organization");
    setLoggedOrg(null);
    navigate("/login");

  }

  useEffect(() => {
    setLoggedOrg(JSON.parse(localStorage.getItem("organization"))?.info);
  }, [])

  return (
    <>

      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />

        <Route path="/home" element={
          <RoutingGuard logOut={logout} Component={Home}>
          </RoutingGuard>}>
          <Route path='statistics' element={<Statistics />} />
          <Route path='passengers' element={<Employees />} />
          <Route path='routes' element={<OrgRoutes />}/>
          <Route path='routes/:id' element={<ViewRoute />}/>
          <Route path='paths/:id' element={<ViewPath />}/>
          <Route path='create-route' element={<CreateRoute />} />
          <Route path='create-path/:id' element={<CreatePath />} />
          <Route path='create-trip/:id' element={<CreateTrip />} />
        </Route>
        <Route path='/' element={<Navigate to="/home" replace />} />
        <Route path="*" element={<NotFound />} />
      </Routes>
      <div className="App bg-success">



        {/* <Register></Register> */}
        {/*</Login> */}
      </div>
    </>
  );
}

export default App;
