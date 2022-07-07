import React from 'react'
import style from "./AddEmpLayer.module.css";


function AddEmpLayer({ setShowAddEmp, showAddEmp }) {
  return (
    <div className={`${style.popUpLayer} ${showAddEmp ? 'd-flex' : 'd-none'} align-items-center justify-content-center `}>

      <div className=' container  text-black'>
        <div className='row h-75'>
          <div className="col-lg-8 col-11 p-md-4 p-2 m-auto rounded-1 bg-light ">
            <form>
              <div class="mb-3">
                <label for="name" class="form-label">Full name</label>
                <input type="text" class="form-control" name="name" />
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email" />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" />
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Phone number</label>
                <input type="number" class="form-control" name="phone" />
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" />
              </div>
              <button type='button' className='btn btn-success m-2'>Add Employee</button>
              <button type='button' onClick={() => setShowAddEmp(false)} className='btn btn-danger m-2'>Cancel</button>
            </form>
          </div>
        </div>
      </div>

    </div>

  )
}

export default AddEmpLayer