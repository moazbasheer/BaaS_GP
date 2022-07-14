import { useEffect, useState } from "react"
import pathService from '../../Services/paths'
import ViewPathMap from "../ViewPathMap/ViewPathMap"
import Joi from 'joi'
import tripService from "../../Services/trips"
import { useOutletContext } from "react-router-dom"
import PageTitle from "../PageTitle/PageTitle"
import { Alert } from "@mui/material"

function CreateTrip() {
  const [form, setForm] = useState({
    pathId: '',
    date: '',
    time: '',
    capacity: '',
    public: true
  })
  const [paths, setPaths] = useState([])
  const [currentPath, setCurrentPath] = useState()
  const [messages, setMessages] = useState([])
  const [wallet, setWallet] = useOutletContext();
  console.log("wallet in create trips is " + wallet);
  useEffect(() => {
    pathService.getAll().then((result) => setPaths(result.message))
  }, [])

  useEffect(() => {
    if (form.pathId) {
      pathService.get(form.pathId).then((result) => {
        setCurrentPath(result.message);
      });
    }
  }, [form.pathId]);

  const validateForm = tripForm => {
    const schema = Joi.object({
      pathId: Joi.number().messages({
        'number.base': 'You must select a path.'
      }),
      date: Joi.string().messages({
        'string.empty': 'You must select a date for the trip.'
      }),
      time: Joi.string().min(1).messages({
        'string.empty': 'You must specify time for the trip.'
      }),
      capacity: Joi.number().min(10).max(100).messages({
        'number.base': 'You must specify a capacity for the trip',
        'number.min': 'Capacity cannot be smaller than 10.',
        'number.max': 'Capacity cannot be greater than 100.',
      }),
      public: Joi.boolean().messages({
        'boolean.base': 'You must select whether the trip is public or not.'
      })
    })

    return schema.validate(tripForm, { abortEarly: false })
  }

  const handleSubmit = event => {
    event.preventDefault()
    console.log(form)

    const validation = validateForm({
      ...form,
      pathId: parseInt(form.pathId),
      capacity: parseInt(form.capacity)
    })

    if (validation.error) {
      setMessages(validation.error.details.map(d => ({
        content: d.message,
        type: 'error'
      })))
    } else {
      if (currentPath.price > wallet) {
        setMessages([{
          content: 'Your balance is not enough for creating more trips, charge your wallet and try again',
          type: 'error'
        }]);
      } else {
        const data = {
          path_id: form.pathId,
          repitition: 'one-time',
          date: form.date,
          time: form.time,
          num_seats: form.capacity,
          public: +form.public
        }
        console.log(data)
        tripService.create(data).then(
          response => {
            console.log("create trip response is : " + response.data);
            if (response.status === 200) {
              const { id } = response.data.trip;
              tripService.payTrip(id).then((res) => {
                console.log("response for payment : " + res);
                if (res.data.status == true) {
                  setMessages([{content: 'Trip created successfully.', type: 'success'}]);
                  setWallet((prev) => prev - currentPath.price);
                }
                else setMessages([{content: 'Error in trip payment.', type: 'error'}]);
              }).catch((err) => console.log("error in paying for trip with id " + id + "\n" + err));
              console.log(id);

            }
          }
        )

      }
    }
  }

  const handleChange = event => {
    setForm({ ...form, [event.target.name]: event.target.value })
  }

  const handleCheckbox = event => {
    setForm({ ...form, [event.target.name]: event.target.checked })
  }

  // return today date in format yyyy-mm-dd as a string
  const getTodayDate = () => {
    let date = new Date()
    date.setDate(date.getDate() + 2)
    const timezoneOffset = date.getTimezoneOffset()
    date = new Date(date.getTime() - (timezoneOffset * 60 * 1000))

    return date.toISOString().split('T')[0]
  }

  return (
    <>
      <PageTitle title={'Create a New Trip'}/>
      <form className="" onSubmit={handleSubmit}>
        <div>
          <select className="form-select" name="pathId" id="path" value={form.pathId} onChange={handleChange}>
            <option value="" disabled>Select your path</option>
            {paths.map(
              path => <option key={path.id} value={path.id}>{path.path_name}</option>
            )}
          </select>
          {currentPath && <div className="my-3"><ViewPathMap path={currentPath} /></div>}
        </div>
        {messages.map((message, i) => <Alert key={i} severity={message.type}>{message.content}</Alert>)}
        <div>
          <label htmlFor="date" className='form-label'>Date </label>
          <input className="form-control" type="date" name="date" id="date" value={form.date} onChange={handleChange} min={getTodayDate()} />
        </div>
        <div>
          <label htmlFor="time" className='form-label'>Time </label>
          <input className="form-control" type="time" name="time" id="time" value={form.time} onChange={handleChange} />
        </div>
        <div>
          <label htmlFor="capacity" className='form-label'>Capacity </label>
          <input className="form-control" type="number" name="capacity" id="capacity" placeholder="Must be between 10 - 100" value={form.capacity} onChange={handleChange} />
        </div>
        <div>
          <label htmlFor="public" className='form-check-label me-2'>Public </label>
          <input className="form-check-input" type="checkbox" name="public" id="public" checked={form.public} onChange={handleCheckbox} />
        </div>
        <button className="btn btn-primary w-100" type="submit">Create Trip</button>
      </form>
    </>
  )
}

export default CreateTrip