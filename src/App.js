
import './App.css';
import Login from './Components/Login/login';
import Register from './Components/Register/Register';
import Home from './Components/Home/Home';
import { Navigate, Route, Routes, useNavigate } from 'react-router-dom';
import NotFound from './Components/Not Found/NotFound';
import { useEffect, useState } from 'react';
import RoutingGuard from './Components/RoutingGuard/RoutingGuard';



function App() {
  const [loggedOrg, setLoggedOrg] = useState({});
  const navigate= useNavigate();

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
          </RoutingGuard>}
        />
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
