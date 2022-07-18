import { useState } from 'react';
import { privateRequest } from '../../axiosRequest';
import style from './AddEmpLayer.module.css';

function AddEmpLayer({
  setShowAddEmp, showAddEmp, openAlert, setTrickReload,
}) {
  const [employee, setEmployee] = useState({
    name: '', email: '', phone: '', password: '', address: '',
  });
  const [addEmployerErrors, setAddEmployerErrors] = useState([]);

  function handleEmployee(e) {
    const temp = { ...employee };
    temp[e.target.name] = e.target.value;
    setEmployee(temp);
  }

  async function addEmployee(e) {
    e.preventDefault();
    try {
      const response = await privateRequest.post('organization/passengers', employee);
      // console.log(response);
      const { data } = response;
      if (data.status) {
        setShowAddEmp(false);
        openAlert();
        setTrickReload((oldState) => !oldState);// flip the state to retrive the new Employess the
        setAddEmployerErrors([]);
        // console.log("inside if");
      } else {
        console.log('inside else ');
        console.log(data.message);
        setAddEmployerErrors(data.message);
      }
      console.log(data);
    } catch (error) {
      console.log(`error in adding employer${error}`);
    }
    console.log(employee);
  }

  return (
    <div className={`${style.popUpLayer} ${showAddEmp ? 'd-flex' : 'd-none'} align-items-center justify-content-center `}>

      <div className=" container  text-black">
        <div className="row h-75">
          <div className="col-lg-8 col-11 p-md-4 p-2 m-auto rounded-1 bg-light ">
            <form>
              <div className="mb-3">
                <label htmlFor="name" className="form-label">Full name</label>
                <input type="text" className="form-control" onChange={handleEmployee} name="name" />
              </div>
              <div className="mb-3">
                <label htmlFor="email" className="form-label">Email address</label>
                <input type="email" className="form-control" onChange={handleEmployee} name="email" />
              </div>
              <div className="mb-3">
                <label htmlFor="password" className="form-label">Password</label>
                <input type="password" className="form-control" onChange={handleEmployee} name="password" />
              </div>
              <div className="mb-3">
                <label htmlFor="phone" className="form-label">Phone number</label>
                <input type="number" className="form-control" onChange={handleEmployee} name="phone" />
              </div>
              <div className="mb-3">
                <label htmlFor="address" className="form-label">Address</label>
                <input type="text" className="form-control" onChange={handleEmployee} name="address" />
              </div>
              <button type="button" onClick={addEmployee} className="btn btn-primary m-2">Add Employee</button>
              <button type="button" onClick={() => setShowAddEmp(false)} className="btn btn-danger m-2">Cancel</button>
              {addEmployerErrors.map((err, index) => (
                <div key={index} className="alert-danger text-center rounded-1 my-1 py-1">
                  {err}
                  {' '}
                </div>
              ))}
            </form>
          </div>
        </div>
      </div>

    </div>

  );
}

export default AddEmpLayer;
