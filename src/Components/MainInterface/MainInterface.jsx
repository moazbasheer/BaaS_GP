import React, { useEffect, useState } from 'react';
import { Alert, Snackbar } from '@mui/material';
import style from "./MainInterface.module.css";
import { Outlet } from "react-router-dom";
import SideNav from '../SideNav/SideNav';
import { privateRequst } from '../axiosRequest';
// import { useNavigate } from 'react-router-dom'
import LogoutIcon from '@mui/icons-material/Logout';
import Charge from './Charge/Charge';
// email test@test.com password : testtest

function MainInterface({ logOut }) {
  const [wallet, setWallet] = useState(0);
  const [showLayer, setShowLayer] = useState(false);
  const [successAlert, setSuccessAlert] = useState(false)
  const [orgName, setOrgName] = useState(
    JSON.parse(
      localStorage.getItem('organization')
    ).info.name
  )
  useEffect(() => {
    var balance = 0;

    try {
      privateRequst.get('organization/wallet').then((response) => {
        balance = response.data.message.balance;
        console.log("balance is " + balance);
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
    console.log("reason is " + reason);
    if (reason === 'clickaway') {// to keep the alert untill its duration ends or the user clicks on the close icon on the alert
      return; // if the user clicked on any other place on the screen the alert will not disappear.
    }
    setSuccessAlert(false);
  }
  // let navigate = useNavigate();
  // console.log(props);
  return (
    <>
      <div className={`d-flex flex-column ${style.mainHome}`}>
        <div className='d-flex justify-content-between bg-primary border border-1 border-dark'>
          <h1 className='fw-bold text-white ms-5'>BaaS</h1>
          <button className={`btn btn-primary text-end text-white ${style.logoutBtn}`} onClick={logOut}><LogoutIcon className='text-white' /> Log out</button>
        </div>
        <div className='d-flex justify-content-between align-items-center px-1 p-1'>
          <div className='fs-5'>Hello, <span className='fw-bold text-black'>{orgName}</span>!</div>
          <div>
            <div><span className="fw-bold">Balance: </span>{wallet.toLocaleString()} EGP</div>
            <button className='btn btn-success fw-bold w-100' onClick={() => { setShowLayer(true) }}>Recharge</button>
          </div>
        </div>
        <div className="container-fluid">
          <div className={`row ${style.outlet}  `}>
            <div className="col-md-2 bg-primary min-vh-100 border border-1 border-dark rounded-1 px-0">
              <SideNav />
            </div>
            <div className={`col-md-10`}>
              <Outlet context={[wallet, setWallet]} />
            </div>
          </div>
        </div>
      </div>
      <Charge setShowlayer={setShowLayer} showLayer={showLayer} openAlert={openAlert} wallet={wallet} setWallet={setWallet} />
      <Snackbar open={successAlert} autoHideDuration={6000} onClose={handleClose}>
        <Alert onClose={handleClose} severity="success">
          Wallet Charged Successfully!
        </Alert>
      </Snackbar>
    </>
  )
}

export default MainInterface;