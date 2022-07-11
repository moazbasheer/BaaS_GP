import axios from 'axios';

const baseUrl = `https://graphhopper.com/api/1/route?key=bd47e377-3534-45d4-95a1-e35b7a1ac81d`;

const getPath = async (points) => {
  let url = baseUrl;
  points.forEach((point) => url = url.concat(`&point=${point.join(',')}`));
  console.log(url)
  const result = await axios.get(url);
  return result.data;
};

export default { getPath };
