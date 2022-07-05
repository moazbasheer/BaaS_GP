import 'ol/ol.css'
import './CreateRouteMap.css'
import { useEffect } from "react"
import { Map, View } from 'ol'
import { Draw } from 'ol/interaction'
import { Tile as TileLayer, Vector as VectorLayer } from 'ol/layer'
import { OSM, Vector as VectorSource } from 'ol/source'
import { fromLonLat } from 'ol/proj'
import { setPointStyle, getCoordinates } from '../../Utility/map'
import MapComponent from '../MapComponent/MapComponent'

let pointType, origin, destination

const pointsVecSource = new VectorSource();
const pointsVecLayer = new VectorLayer({ source: pointsVecSource })

const drawAction = new Draw({
  source: pointsVecSource,
  type: 'Point'
})

const CreateRouteMap = ({ setOrigin, setDestination }) => {
  const setEndpointsStates = () => {
    setOrigin(origin)
    setDestination(destination)
  }

  const clearPoints = () => {
    origin = null
    destination = null
    pointsVecSource.clear()
    setEndpointsStates()
  }

  const handlePointDraw = event => {
    const point = event.feature
    point.type = pointType
    
    if (pointType === 'origin') {
      pointsVecSource.removeFeature(origin)
      origin = point
    }
    else if (pointType === 'destination') {
      pointsVecSource.removeFeature(destination)
      destination = point
    }
    
    setPointStyle(point)
    setEndpointsStates()
  }

  const changePointType = type => {
    pointType = type

    // enabling/disabling drawing
    drawAction.setActive(pointType !== 'none')
  }

  useEffect(() => {
    drawAction.on('drawstart', handlePointDraw)
    changePointType('none')
  }, [])

  return (
    <>
      <div>
        <button onClick={() => changePointType('origin')}>Origin</button>
        <button onClick={() => changePointType('destination')}>Destination</button>
        <button onClick={() => changePointType('none')}>None</button>
      </div>
      <div>
        <button onClick={clearPoints}>Clear All</button>
      </div>
      <MapComponent layers={[pointsVecLayer]} interactions={[drawAction]} />
    </>
  )
}

export default CreateRouteMap