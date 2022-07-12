import React, { useEffect, useState } from 'react';
import { Alert, Snackbar } from '@mui/material';
import style from "./Home.module.css";
import { Outlet } from "react-router-dom";
import SideNav from '../SideNav/SideNav';
import { privateRequst } from '../axiosRequest';
// import { useNavigate } from 'react-router-dom'
import LogoutIcon from '@mui/icons-material/Logout';
import Charge from './Charge/Charge';
// email test@test.com password : testtest

function MainInterface({ logOut }) {
  const [wallet, setWallet] = useState(1);
  const [showLayer, setShowLayer] = useState(false);
  const [successAlert, setSuccessAlert] = useState(false)
  useEffect( () => {
    var balance= 0;
    
     try {
       privateRequst.get('organization/wallet').then( (response)=>{
         balance= response.data.message.balance;
         console.log("balance is "+balance);
         setWallet(balance);
         // balance = data.message.balance;
       } 
       )
     } catch (error) {
       console.log("error in checking balance\n" + error);
     }
  
  //  setWallet(balance);
   console.log(balance);
 }, []);

  // check use reduce to handle wallet 
  const openAlert = () => {
    setSuccessAlert(true);
  }
  const handleClose = (event, reason) => {
    console.log( "reason is "+ reason);
    if (reason === 'clickaway') {// to keep the alert untill its duration ends or the user clicks on the close icon on the alert
      return; // if the user clicked on any other place on the screen the alert will not disappear.
    }
    setSuccessAlert(false);
  }
  // let navigate = useNavigate();
  // console.log(props);
  return (
    <>
      <div className={`container d-flex flex-column bg-dark text-white ${style.mainHome}`}>
        <div className='d-flex justify-content-between my-2'>
          <h1 className='text-center'>BaaS</h1>
          <button className={`btn text-end text-white ${style.logoutBtn}`} onClick={logOut}><LogoutIcon className='text-white'/> Log out </button>
        </div>
        <div className={`row ${style.outlet}  `}>
          <div className="col-md-3 bg-info">
            <SideNav />
          </div>
          <div className={`col-md-9`}>
            <div className='d-flex justify-content-between border border-1 border-secondry my-2 align-items-center'>
            <h2></h2>
            <h1 className=''>Home</h1>
              <div>
                Balance :  <span className={`fw-bolder`}>{wallet} </span>
              <button className='btn btn-primary' onClick={ ()=>{  setShowLayer(true) }}>Charge</button>

              </div>
            </div>
            <Outlet context={[wallet, setWallet]} />
          </div>
        </div>
      </div>
      {/* // setShowlayer, showLayer, openAlert ,setTrickReload } */}
    <Charge setShowlayer={setShowLayer}   showLayer={showLayer} openAlert={openAlert} wallet={wallet} setWallet={setWallet} />

    <Snackbar open={successAlert} autoHideDuration={6000} onClose={handleClose}>
        <Alert onClose={handleClose} severity="success">
          Wallet Charged Successfully!
        </Alert>
      </Snackbar>
    </>
  )
}

export default MainInterface;