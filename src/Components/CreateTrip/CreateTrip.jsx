import { useEffect, useState } from "react"
import pathService from '../../Services/paths'
import ViewPathMap from "../ViewPathMap/ViewPathMap"
import Joi from 'joi'
import Notification from "../Notification/Notification"
import tripService from "../../Services/trips"

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
      setMessages(validation.error.details.map(d => d.message))
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
          if (response.status === 200) {
            setMessages(['Trip created successfully.'])
          }
        }
      )
    }
  }

  const handleChange = event => {
    setForm({ ...form, [event.target.name]: event.target.value })
  }

  const handleCheckbox = event => {
    setForm({ ...form, [event.target.name]: event.target.checked })
  }

  const handleRepetition = event => {
    setForm({ ...form, repeat: { ...form.repeat, [event.target.name]: event.target.checked } })
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
      {currentPath ? <ViewPathMap path={currentPath} /> : null}
      <form onSubmit={handleSubmit}>
        <div>
          <select name="pathId" id="path" value={form.pathId} onChange={handleChange}>
            <option value="" disabled>Select your path</option>
            {paths.map(
              path => <option key={path.id} value={path.id}>{path.path_name}</option>
            )}
          </select>
        </div>
        <div>
          <label htmlFor="date">Date </label>
          <input type="date" name="date" id="date" value={form.date} onChange={handleChange} min={getTodayDate()} />
        </div>
        <div>
          <label htmlFor="time">Time </label>
          <input type="time" name="time" id="time" value={form.time} onChange={handleChange} />
        </div>
        {/* <div>
          Repetition
          <div>
            <label htmlFor="sat">Sat</label>
            <input type="checkbox" name="sat" id="sat" checked={form.repeat.sat} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="sun">Sun</label>
            <input type="checkbox" name="sun" id="sun" checked={form.repeat.sun} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="mon">Mon</label>
            <input type="checkbox" name="mon" id="mon" checked={form.repeat.mon} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="tue">Tue</label>
            <input type="checkbox" name="tue" id="tue" checked={form.repeat.tue} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="wed">Wed</label>
            <input type="checkbox" name="wed" id="wed" checked={form.repeat.wed} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="thu">Thu</label>
            <input type="checkbox" name="thu" id="thu" checked={form.repeat.thu} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="fri">Fri</label>
            <input type="checkbox" name="fri" id="fri" checked={form.repeat.fri} onChange={handleRepetition} />
          </div>
        </div> */}
        <div>
          <label htmlFor="capacity">Capacity </label>
          <input type="number" name="capacity" id="capacity" value={form.capacity} onChange={handleChange} />
        </div>
        <div>
          <label htmlFor="public">Public </label>
          <input type="checkbox" name="public" id="public" checked={form.public} onChange={handleCheckbox} />
        </div>
        <button type="submit">Create Trip</button>
      </form>
      {messages.map((message, i) => <Notification key={i}>{message}</Notification>)}
    </>
  )
}

export default CreateTrip