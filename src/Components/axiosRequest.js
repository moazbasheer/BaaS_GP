import axios from 'axios';

const baseUrl = 'http://baas-gp.herokuapp.com/api/';

// get the token from the local storage.
const org = JSON.parse(localStorage.getItem('organization'));

// the question mark to avoid getting an error when the user is not loggedin
export const token = org?.token;
console.log(`token is ${token}`);
const config = {
  Authorization: `Bearer ${token}`,
};

export const publicRequest = axios.create({ // for requests without tokens -Loggin Register-.
  baseURL: baseUrl,
});
export const privateRequest = axios.create({
  baseURL: baseUrl,
  headers: config,
});
