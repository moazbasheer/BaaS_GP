import pathAPIService from '../Services/graphhopper';

// returns the textual address of given coordinates
export const getCoordinatesAddress = async (coordinates) => {
  const result = await pathAPIService.getReverseGeocode(coordinates)

  const street = result.hits[0].street
  const city = result.hits[0].city

  let address = [street, city]
  address = address.filter(element => element)

  return address.join(', ')
}