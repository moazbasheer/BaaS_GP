import React from 'react'
// import { useNavigate } from 'react-router-dom'

// email test@test.com password : testtest
function Home({ logOut }) {
  // let navigate = useNavigate();
  // console.log(props);
  return (
    <>
      <h1 className='w-100 h-100 bg-danger'>Home</h1>
      <button className='btn btn-info ' onClick={logOut}> logOut </button>
    </>
  )
}

export default Home