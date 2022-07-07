import { privateRequst } from '../Components/axiosRequest'

const baseUrl = `/organization/paths`

const get = async id => {
  const response = await privateRequst.get(`${baseUrl}/id/${id}`)
  return response.data
}

const getAll = async () => {
  const response = await privateRequst.get(baseUrl)
  return response.data
}

const getByRouteId = async id => {
  const response = await privateRequst.get(`${baseUrl}/${id}`)
  return response.data
}

const create = async path => {
  const response = await privateRequst.post(baseUrl, path)
  return response
}

const deletePath = async id => {
  const response = await privateRequst.delete(`${baseUrl}/${id}`)
  return response
}

export default { create, get, getAll, getByRouteId, deletePath }