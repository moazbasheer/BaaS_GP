import Joi from 'joi';
import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom';

import background from "../../imges/backImg.jpg"
import { publicRequst } from '../axiosRequest';
import style from "./Register.module.css"

import { Alert, AlertTitle } from '@mui/material';


function Register(props) {
  const [orgainzation, setOrgainzation] = useState({
    name: "", email: "", password: "", phone_number: "", address: "",
    postal_code: "", role_name: "organization"
  });
  const [formValidationErrors, setformValidationErrors] = useState([]);
  const [isFetching, setIsFetching] = useState(false);
  const [isSuccess, setIsSuccess] = useState(false);
  const navigate = useNavigate();

  function handleOrg(event) {// function takes the input and set it to the organization object (state).
    let org = { ...orgainzation };
    org[event.target.name] = event.target.value;
    setOrgainzation(org);
    // console.log(orgainzation);
  }
  // .message("name is required")  .message("email is required")  .message("phone number must be graeter than 10 numbers.")  
  // .message("address is required") .message("postal code is required and must be less than 5 numbers")
  function formValidation() {
    let schema = Joi.object({
      name: Joi.string().required(),
      email: Joi.string().email({ tlds: { allow: false } }).required(),
      password: Joi.string().required().min(8).pattern(new RegExp('^[a-zA-Z0-9]{5,20}$')).message("password must start with a character and at least 8 length long."),
      phone_number: Joi.string().min(10).pattern(/^[0-9]+$/).required(),
      address: Joi.string().required(),
      postal_code: Joi.string().max(5).required(),
      role_name: Joi.optional()
    })
    return schema.validate(orgainzation, { abortEarly: false });
  }

  async function submitForm(e) {
    e.preventDefault();
    setIsFetching(true);
    setIsSuccess(true);
    let formvalidationResult = formValidation();
    if (formvalidationResult.error) {
      console.log(formValidationErrors);
      setformValidationErrors(formvalidationResult.error.details);
      setIsFetching(false);
    } else {
      setformValidationErrors([]);
      try {
        let {data} = await publicRequst.post("register", orgainzation);
        console.log(data);
        if(data.status){
          setIsFetching(false);
          console.log(isSuccess);
          navigate("/login")
        }else{
          setformValidationErrors(data.message);
          console.log("error in register\n"+data.message[0]);
          setIsFetching(false);
        }
        

        

        
      } catch (error) {
        console.log("error\n", error);
        setIsFetching(false);
      }
    }
  }



  return (
    <div style={{
      backgroundImage: `url(${background})`, backgroundSize: "cover", backgroundPosition: "center",
      backgroundOrigin: "content-box" ,backgroundAttachment:"fixed"
    }} className={`  position-absolute top-0 bottom-0 start-0 end-0`} >
      <div className="container vh-100 vw-100">
        <div className='row  w-100 h-100  m-auto  d-flex justify-content-between align-items-center '>

          <div className='col-md-5 text-center p-4 text-white bg-primary bg-opacity-75' >
            <h1 className={`${style.leftHeader} my-3 `}>BaaS  </h1>
            <h3 className='fw-bold my-5 '>Smarter transportation for your people </h3>
            <h4>  Replace the inefficiencies plaguing your transportation. </h4>
          </div>
          <div className={`col-md-5 text-start ${style.registerForm} bg-white  bg-opacity-75 fw-bold border border-primary border-5`}  >
            <h2 className='text-md-center fw-bold'> Create An Account </h2>
            <form className='m-lg-4 m-md-2 m-0'>
              <div className="form-group text-start  ">
                <label htmlFor="name" className=' ' >Name</label>
                <input onChange={handleOrg} type="text" name='name' className="form-control" placeholder="Organization Name" />
              </div>
              <div className="form-group text-start ">
                <label htmlFor="email" className=' ' >Email address</label>
                <input onChange={handleOrg} type="email" name='email' className="form-control" id="exampleFormControlInput1" placeholder="name@example.com" />
              </div>
              <div className="form-group text-start ">
                <label htmlFor="password" className=' ' >Password</label>
                <input onChange={handleOrg} type="password" name='password' className="form-control" placeholder="Password" />
              </div>
              <div className="form-group text-start ">
                <label htmlFor="phone_number" className=' ' >Phone Number</label>
                <input onChange={handleOrg} type="text" name='phone_number' className="form-control" placeholder="+02 01102488789" />
              </div>
              <div className="form-group text-start ">
                <label htmlFor="postal_code" className=' ' >Postal Code</label>
                <input onChange={handleOrg} type="text" name='postal_code' className="form-control" placeholder="ex. 12511" />
              </div>
              <div className="form-group text-start ">
                <label htmlFor="address" className=' ' >Address</label>
                <input onChange={handleOrg} type="text" name='address' className="form-control" placeholder="Address" />
              </div>
              <div className="validationErrors  ">
                {formValidationErrors.map((err, index) => <Alert key={index} severity='error' className='my-2 p-2'> {err.message||err} </Alert>)}
              </div>
              <button type='submit' disabled={isFetching} onClick={submitForm} className='btn btn-primary  mt-3 fs-3 fw-bolder text-white'>
                {
                  isFetching ?
                    <div className="spinner-border text-warning" role="status">
                      <span className="visually-hidden">Loading...</span>
                    </div>
                    :
                    "Register"
                }
              </button>

              {/* <button onClick={showAlert}>  test alert </button> */}
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Register;