import { privateRequst } from '../Components/axiosRequest';

const baseUrl = '/organization/routes';

const getAll = async () => {
  const response = await privateRequst.get(baseUrl);
  return response.data;
};

const get = async (id) => {
  const response = await privateRequst.get(`${baseUrl}/${id}`);
  return response.data;
};

const create = async (route) => {
  const response = await privateRequst.post(baseUrl, route);
  return response;
};

const deleteRoute = async (id) => {
  const response = await privateRequst.delete(`${baseUrl}/${id}`);
  return response;
};

export default {
  create, getAll, get, deleteRoute,
};
