import pathAPIService from '../Services/graphhopper';

// returns the textual address of given coordinates
export const getCoordinatesAddress = async (coordinates) => {
  const result = await pathAPIService.getReverseGeocode(coordinates)
  return `${result.hits[0].street}, ${result.hits[0].city}`
}