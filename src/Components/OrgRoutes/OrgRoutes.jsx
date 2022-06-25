import React from 'react'
import { Link } from 'react-router-dom'
import { Outlet } from 'react-router-dom'

function OrgRoutes() {


  return (
    <div>
      <Link to='../create-route'>
        <button className='btn btn-success mx-2'>
          Create New Route
        </button>
      </Link>
    </div>
  )
}

export default OrgRoutes