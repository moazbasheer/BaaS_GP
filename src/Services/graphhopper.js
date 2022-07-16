import axios from 'axios';

const routingEndpoint = `https://graphhopper.com/api/1/route?key=${process.env.REACT_APP_GRAPHHOPPER_KEY}`;
const geocodingEndpoint = `https://graphhopper.com/api/1/geocode?key=${process.env.REACT_APP_GRAPHHOPPER_KEY}`;

const getPath = async (coordinates) => {
  let url = routingEndpoint;
  coordinates.forEach((coordinate) => {
    url = url.concat(`&point=${coordinate.join(',')}`);
  });
  const result = await axios.get(url);
  return result.data;
};

// given an array of lat long returns the result of reverse geocoding this coordinates
const getReverseGeocode = async (coordinates) => {
  let url = geocodingEndpoint;
  url = url.concat('&reverse=true');
  url = url.concat(`&point=${coordinates.join(',')}`);
  const result = await axios.get(url);
  return result.data;
};

export default { getPath, getReverseGeocode };
