import axios from 'axios'

const baseUrl = `http://localhost:3002/paths`

const getAll = async () => {
  const response = await axios.get(baseUrl)
  return response.data
}

const get = async id => {
  const response = await axios.get(`${baseUrl}/${id}`)
  return response.data
}

const create = async route => {
  const response = await axios.post(baseUrl, route)
  return response
}

export default { create, getAll, get }