import Joi from 'joi';
import React, { useState } from 'react'
import { NavLink, useNavigate } from 'react-router-dom';

import background from "../../imges/backImg.jpg"
import { publicRequst } from '../axiosRequest';
import style from "./login.module.css"
function Login() {
  const [organization, setOrganization] = useState({ emailorusername: "", password: "" });
  const [isFetching, setisFetching] = useState(false);
  const [errors, setErrors] = useState([]);
  const navigate = useNavigate();
  function handleForm(e) {// takes the input from the login form and save it in the organization object 
    let tempOrg = { ...organization };
    tempOrg[e.target.name] = e.target.value;
    setOrganization(tempOrg);
  
  }

  function formValidation() {// to validate the input before sending it to the server.
    let schema = Joi.object({
      emailorusername: Joi.string().email({ tlds: { allow: false } }).required(),
      password: Joi.string().required().min(8).pattern(new RegExp('^[a-zA-Z0-9]{5,20}$')).message("password must start with a character and at least 8 length long.")
    })
    return schema.validate(organization, { abortEarly: false }); // abort early false: to keep checking all the inputs -donot stop after the first error-
  }


  const submitForm = async (e) => {
    e.preventDefault();
    setisFetching(true);
    let isFormValid = formValidation();
    if (isFormValid.error) {// invalid input
      setErrors(isFormValid.error.details);
      setisFetching(false);
    } else {
      try {
        let response = await publicRequst.post("login", organization);
        response = response.data;
        if (response.status) {
          let storedOrg = { info: response.user, token: response.token }
          localStorage.setItem("organization", JSON.stringify(storedOrg));
          navigate("/home", { replace: true });
        } else {
          setErrors([...errors, response.message]);
        }
        console.log(response);
        setisFetching(false);
      } catch (error) {
        console.log("error in logging in\n", error);
        isFetching(false);
      }
    }


  }
  return (
    <div style={{
      backgroundImage: `url(${background})`, backgroundSize: "cover", backgroundPosition: "center",
      backgroundAttachment: "fixed", backgroundOrigin: "content-box"
    }} className={` bg-warning position-absolute top-0 bottom-0 start-0 end-0`} >
      <div className="container vh-100 vw-100">
        <div className='row w-100 h-100 justify-content-between m-auto  d-flex justify-content-center align-items-center rightContainer'>

          <div className='col-md-5 text-center busColor p-4' >
            <h1 className={`${style.leftHeader} my-3 `}>BaaS  </h1>
            <h3 className='fw-bold my-5 '>Smarter transportation for your people </h3>
            <h4>  Replace the inefficiencies plaguing your transportation. </h4>
          </div>
          <div className={`col-md-5  text-start ${style.loginForm} bg-white  bg-opacity-50 fw-bold`}  >
            <h2 className='text-md-center'> SIGN IN </h2>
            <form className='m-lg-4 m-md-2 m-0'>
              <div className="form-group text-start ">
                <label htmlFor="email" className=' ' >Email address</label>
                <input type="email" name='emailorusername' onChange={handleForm} className="form-control" id="exampleFormControlInput1" placeholder="Email" />
              </div>
              <div className="form-group text-start ">
                <label htmlFor="password" className=' ' >Password</label>
                <input type="password" name='password' onChange={handleForm} className="form-control" placeholder="Password" />
              </div>

              <div className="validationErrors  ">
                {errors.map((err, index) => <div key={index} className='alert-danger  my-2 p-2'> {err.message} </div>)}
              </div>
              <button onClick={submitForm} className='btn btn-outline-warning  mt-3 fs-3 fw-bolder text-white' disabled={isFetching}>
                {
                  isFetching ?
                    <div className="spinner-border text-warning" role="status">
                      <span className="visually-hidden">Loading...</span>
                    </div>
                    :
                    "LOGIN"
                }

              </button>
              <div className='text-end '> you can create one from   <NavLink to="/register" style={{ color: "green", textDecoration: "underline" }}>here </NavLink></div>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}

export default Login;