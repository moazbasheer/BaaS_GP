import React from 'react'
import { NavLink } from 'react-router-dom'

function NotFound() {
 
 
  return (
    <> <h1 className='text-center mt-lg-5'>sorry, this page is no longer exist</h1>
  <h2 className='text-center mt-lg-5'> return to <NavLink to="/home" style={{color:"green", fontSize:"30px", textDecoration:"none" }}>Home </NavLink> </h2>
</>
  )
}

export default NotFound