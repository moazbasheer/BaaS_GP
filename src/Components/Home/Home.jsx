import React from 'react';
import style from "./Home.module.css";
import { NavLink, Outlet } from "react-router-dom";
import SideNav from '../SideNav/SideNav';
// import { useNavigate } from 'react-router-dom'

// email test@test.com password : testtest
function Home({ logOut }) {
  // let navigate = useNavigate();
  // console.log(props);
  return (
    <>
      <div className={`container d-flex flex-column ${style.mainHome}`}>
        <div className='d-flex justify-content-between '> 
      <h1 className='text-center'>BaaS</h1>
      <button  className='btn btn-info text-end' onClick={logOut}> Logout </button>
      </div>
     
        <div className={`row ${style.outlet} bg-dark `}>
          
          <div className="col-md-3 bg-info">
           <SideNav/>
{/* make the side nav here then copy it to the side nav folder */}

          </div>
          <div className={`col-md-9   ` }>
            <h1 className=' bg-danger'>Home</h1>
         
            <Outlet /> 
          </div>

        </div>
    
      </div>
    </>
  )
}

export default Home