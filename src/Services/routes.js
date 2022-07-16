import { privateRequest } from '../Components/axiosRequest';

const baseUrl = '/organization/routes';

const getAll = async () => {
  const response = await privateRequest.get(baseUrl);
  return response.data;
};

const get = async (id) => {
  const response = await privateRequest.get(`${baseUrl}/${id}`);
  return response.data;
};

const create = async (route) => {
  const response = await privateRequest.post(baseUrl, route);
  return response;
};

const deleteRoute = async (id) => {
  const response = await privateRequest.delete(`${baseUrl}/${id}`);
  return response;
};

export default {
  create, getAll, get, deleteRoute,
};
