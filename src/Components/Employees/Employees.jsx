import { Alert, Snackbar } from '@mui/material';
import React, { useEffect, useState } from 'react'
import { privateRequst } from '../axiosRequest';
import AddBulkLayer from './AddEmplyeeLayer/AddBulkLayer';
import AddEmpLayer from './AddEmplyeeLayer/AddEmpLayer';
import style from "./Employees.module.css";


function Passengers() {
  const [successAlert, setSuccessAlert] = useState(false)
  const [passengers, setPassengers] = useState([ // this data should be feteched from the database 
    { name: 'Ahmed Ali', email: "ahmed.aly@gmail.com", phone: "+02 01100110011", address: "maryotia, king faysl giza street" },
    { name: 'Aly Aly', email: "ali.ali@gmail.com", phone: "+02 01122110011", address: "maryotia, Haram street" },
    { name: 'Mohamed Mohy', email: "mohamed.mohy@gmail.com", phone: "+02 01133113311", address: "elwfaa, king faysl giza street" },
    { name: 'Mahmoud Ashraf', email: "Mahmoud.ashraf@gmail.com", phone: "+02 01144114411", address: "elomrania, king faysl giza street" },
    { name: 'Ahmed Megahd', email: "ahmed.megahd@gmail.com", phone: "+02 01188118811", address: "zaid, king faysl giza street" },
    { name: 'Ahmed AboTrika', email: "ahmed.trika@gmail.com", phone: "+02 01166116611", address: "october king faysl giza street" },
  ]);
  const [trickReload, setTrickReload] = useState(false);// to ensure that the data updated after adiition of any employer.
  const [showAddEmp, setShowAddEmp] = useState(false);
  const [showBulkLayer, setBulkLayer] = useState(false);

  useEffect(() => {
    
    try {
      privateRequst.get('organization/passengers').then((response)=>{
        const employess=response.data.message
        // console.log(response.data.message);
        setPassengers(employess);
      }).catch((error)=>{
        console.log("error in retriving employes from server: "+error); 
      })  ;
    } catch (error) {
     console.log("error in retriving employes from server: "+error); 
    }
  }, [trickReload]);
  

  const openAlert = () => {
    setSuccessAlert(true);
  }
  const handleClose = (event, reason) => {
    // console.log( "reason is "+ reason);
    if (reason === 'clickaway') {// to keep the alert untill its duration ends or the user clicks on the close icon on the alert
      return; // if the user clicked on any other place on the screen the alert will not disappear.
    }

    setSuccessAlert(false);
  }


  return (
    <>
      <div className='d-flex align-items-center justify-content-between'>
        <h1 className='' >Passengers</h1  >
        <div className={`  `}>
          <button className={`btn btn-success mx-2   `} onClick={() => setShowAddEmp(true)} >Add Employee</button>
          <button className='btn btn-light mx-2' onClick={() => setBulkLayer(true)}>Add Bulk</button>
        </div>
      </div>
      <table className={`table  table-striped table-responsive  table-dark table-hover border border-1 m-auto p-0   ${style.table}`}>

        <thead className={` m-auto  `}>
          <tr className='border border-1 border-white'>
            <th className='' scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Phone</th>
            <th scope="col">Address</th>
          </tr>
        </thead>
        <tbody className=' '>
          {passengers.map((passenger, index) => {
            return <tr key={passenger.id}>
              <th> {index + 1} </th>
              <td>{passenger.name}  </td>
              <td> {passenger.email} </td>
              <td> {passenger.phone} </td>
              <td> {passenger.address} </td>
            </tr>
          })}
        </tbody>

      </table>
          {/* to confirm for the org that the employer is added */}
      <Snackbar open={successAlert} autoHideDuration={6000} onClose={handleClose}>
        <Alert onClose={handleClose} severity="success">
          Empoyer Added  Auccessfully!
        </Alert>
      </Snackbar>

      <AddEmpLayer showAddEmp={showAddEmp} openAlert={openAlert} setShowAddEmp={setShowAddEmp} setTrickReload={setTrickReload} ></AddEmpLayer>
      <AddBulkLayer showBulkLayer={showBulkLayer} setBulkLayer={setBulkLayer} openAlert={openAlert} setTrickReload={setTrickReload} ></AddBulkLayer>


    </>
  )
}

export default Passengers