import { privateRequst } from '../Components/axiosRequest';

const baseUrl = '/organization/trips';

const getAll = async () => {
  const response = await privateRequst.get(baseUrl);
  return response.data;
};

const get = async (id) => {
  const response = await privateRequst.get(`${baseUrl}/${id}`);
  return response.data;
};

const create = async (trip) => {
  const response = await privateRequst.post(baseUrl, trip);
  return response;
};

const payTrip = async (id) => {
  const response = await privateRequst.post(`${baseUrl}/pay/${id}` ,{payment_method:"wallet"});
  return response;
};

const deleteTrip = async (id) => {
  const response = await privateRequst.delete(`${baseUrl}/${id}`);
  return response;
};

export default {
  create, getAll, get, deleteTrip,payTrip
};
