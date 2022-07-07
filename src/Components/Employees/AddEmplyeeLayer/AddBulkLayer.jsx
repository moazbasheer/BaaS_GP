import React, { useState } from 'react'
import style from "./AddEmpLayer.module.css";
import { FileUploader } from "react-drag-drop-files";
import { privateRequst, token } from '../../axiosRequest';
import Axios from 'axios';

const fileTypes = ['CSV'];

function AddBulkLayer({ showBulkLayer, setBulkLayer ,openAlert,setTrickReload}) {

    const [file, setFile] = useState(null);
    const handleChange = (file) => {
        setFile(file);
        // console.log(file);
    };






    async function sendFile() {
        const formData = new FormData();
        formData.append("file", file);
        try {
            // const response = await privateRequst.post('organization/passengers/import', {file:file});
            const response = await Axios({
                method: "post",
                url: "http://baas-gp.herokuapp.com/api/organization/passengers/import",
                data: formData,
                headers: { "Content-Type": "multipart/form-data",
                "Authorization": `Bearer ${token}` },
            })
            console.log(response);
            setBulkLayer(false);
            openAlert();
            setTrickReload( (oldState)=> !oldState  );

        } catch (error) {
            console.log("error in sending file to the server: " + error);
        }
        console.log(file);
    }
    return (
        <div className={`${style.popUpLayer} ${showBulkLayer ? 'd-flex' : 'd-none'} align-items-center justify-content-center `}>

            <div className=' container h-75 text-black'>
                <div className='row w-100 h-100'>
                    <div className="col-lg-8 col-11 p-md-4 p-2 m-auto rounded-1 bg-light ">
                        <form  >
                            <div className='w-50 h-100 m-auto'>

                                <FileUploader className={'w-100'} handleChange={handleChange} name="file" types={fileTypes} />
                            </div>
                            <button type='button' onClick={sendFile} className='btn btn-success m-2'>Add Employee</button>
                            <button type='button' onClick={() => setBulkLayer(false)} className='btn btn-danger m-2'>Cancel</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    )
}

export default AddBulkLayer