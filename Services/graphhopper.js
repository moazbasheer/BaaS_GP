import axios from 'axios'

const baseUrl = `https://graphhopper.com/api/1/route?key=${process.env.REACT_APP_GRAPHHOPPER_KEY}`

const getPath = async points => {
  let url = baseUrl
  points.forEach(point => url = url.concat(`&point=${point.join(',')}`))
  const result = await axios.get(url)
  return result.data
}

export default { getPath }