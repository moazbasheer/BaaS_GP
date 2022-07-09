import { useEffect, useState } from "react"
import pathService from '../../Services/paths'
import ViewPathMap from "../ViewPathMap/ViewPathMap"

function CreateTrip() {
  const [form, setForm] = useState({repeat: {}})
  const [paths, setPaths] = useState([])
  const [currentPath, setCurrentPath] = useState()

  useEffect(() => {
    pathService.getAll().then((result) => setPaths(result.message))
  }, [])

  useEffect(() => {
    if(form.pathId) {
      pathService.get(form.pathId).then((result) => {
        setCurrentPath(result.message);
      });
    }
  }, [form]);

  const handleSubmit = event => {
    event.preventDefault()
    console.log(form)
  }

  const handleChange = event => {
    setForm({ ...form, [event.target.name]: event.target.value })
  }
  
  const handleCheckbox = event => {
    setForm({ ...form, [event.target.name]: event.target.checked })
  }

  const handleRepetition = event => {
    setForm({...form, repeat: {...form.repeat, [event.target.name]: event.target.checked}})
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
      <ViewPathMap path={currentPath} />
      <form onSubmit={handleSubmit}>
        <div>
          <select name="pathId" id="path" value={form.pathId || ""} onChange={handleChange}>
            <option value="" disabled>Select your path</option>
            {paths.map(
              path => <option key={path.id} value={path.id}>{path.path_name}</option>
            )}
          </select>
        </div>
        <div>
          <label htmlFor="name">Trip Name </label>
          <input type="text" name="name" id="name" value={form.name || ''} onChange={handleChange} />
        </div>
        <div>
          <label htmlFor="date">Date </label>
          <input type="date" name="date" id="date" value={form.date || ''} onChange={handleChange} min={getTodayDate()} />
        </div>
        <div>
          <label htmlFor="time">Time </label>
          <input type="time" name="time" id="time" value={form.time || ''} onChange={handleChange} />
        </div>
        <div>
          Repetition
          <div>
            <label htmlFor="sat">Sat</label>
            <input type="checkbox" name="sat" id="sat" checked={form.repeat.sat || false} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="sun">Sun</label>
            <input type="checkbox" name="sun" id="sun" checked={form.repeat.sun || false} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="mon">Mon</label>
            <input type="checkbox" name="mon" id="mon" checked={form.repeat.mon || false} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="tue">Tue</label>
            <input type="checkbox" name="tue" id="tue" checked={form.repeat.tue || false} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="wed">Wed</label>
            <input type="checkbox" name="wed" id="wed" checked={form.repeat.wed || false} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="thu">Thu</label>
            <input type="checkbox" name="thu" id="thu" checked={form.repeat.thu || false} onChange={handleRepetition} />
          </div>
          <div>
            <label htmlFor="fri">Fri</label>
            <input type="checkbox" name="fri" id="fri" checked={form.repeat.fri || false} onChange={handleRepetition} />
          </div>
        </div>
        <div>
          <label htmlFor="capacity">Capacity </label>
          <input type="number" name="capacity" id="capacity" value={form.capacity || ''} onChange={handleChange} />
        </div>
        <div>
          <label htmlFor="public">Public </label>
          <input type="checkbox" name="public" id="public" checked={form.public || false} onChange={handleCheckbox} />
        </div>
        <button type="submit">Create Trip</button>
      </form>
      <div>
      </div>
    </>
  )
}

export default CreateTrip