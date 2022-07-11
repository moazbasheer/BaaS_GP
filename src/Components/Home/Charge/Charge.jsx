import React, {  useEffect, useState } from 'react'
import {  privateRequst } from '../../axiosRequest';
import style from "./Charge.module.css";


function Charge({ setShowlayer, showLayer, openAlert ,wallet,setWallet }) {
    const [card, setCard] = useState({ card_number: "", exp_month: "", exp_year: "", CVC: "", amount: "" });
    /**
     * 4242424242424242
     * 12/2034
     * 123
     */
    const [operationErrors, setOperationErrors] = useState([]);



    // useEffect( () => {
    //     var balance= 0;
        
    //      try {
    //        privateRequst.post('organization/wallet/charge',card).then( (response)=>{
    //         setWallet(card.amount);
    //          // balance = data.message.balance;
    //        } 
    //        )
    //      } catch (error) {
    //        console.log("error in checking balance\n" + error);
    //      }
      
    //    setWallet(balance);
    //    console.log(balance);
    //  }, [wallet]);

    function handleFormValues(e) {
        const temp = { ...card };
        temp[e.target.name] = e.target.value;
        setCard(temp);

    }


  function chargeBalance(e) {
        e.preventDefault();
        
             privateRequst.post('organization/wallet/charge', card).then(({data})=>{
                if(data.status==true){
                    setWallet( (prev)=> prev + parseInt( card.amount));    
                    setShowlayer(false);
                    openAlert();
                    setOperationErrors([]);
                }else{
                    console.log("inside else ");
                    setOperationErrors(data.message);    
                }
            } ).catch((err)=>{                
                console.log("error in Charging Wallet : " + err);
            });


    }

    return (
        <div className={`${style.popUpLayer} ${showLayer ? 'd-flex' : 'd-none'} align-items-center justify-content-center `}>

            <div className=' container  text-black'>
                <div className='row h-75'>
                    <div className="col-lg-8 col-11 p-md-4 p-2 m-auto rounded-1 bg-light ">
                        <form>
                            <div className="mb-3">
                                <label htmlFor="card_number" className="form-label">Card Number</label>
                                <input type="text" className="form-control" onChange={handleFormValues} name="card_number" />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="exp_month" className="form-label">Exporation Month</label>
                                <input type="number" className="form-control" onChange={handleFormValues} name="exp_month" />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="exp_year" className="form-label">Expiration Year</label>
                                <input type="number" className="form-control" onChange={handleFormValues} name="exp_year" />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="CVC" className="form-label">CVC</label>
                                <input type="number" className="form-control" onChange={handleFormValues} name="CVC" />
                            </div>
                            <div className="mb-3">
                                <label htmlFor="amount" className="form-label">Amount</label>
                                <input type="text" className="form-control" onChange={handleFormValues} name="amount" />
                            </div>
                            <button type='button' onClick={chargeBalance} className={ `btn  m-2 fw-bold ${style.chargeBtn}`} >Charge $</button>
                            <button type='button' onClick={() => setShowlayer(false)} className='btn btn-danger m-2'>Cancel</button>
                        { operationErrors.map( (err,index)=> <div key={index} className="alert-danger text-center rounded-1 my-1 py-1">{err}   </div> ) }
                        </form>
                          </div>
                </div>
            </div>

        </div>

    )
}

export default Charge;

