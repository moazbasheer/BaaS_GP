import 'ol/ol.css'
import './CreateRouteMap.css'
import { useEffect } from "react"
import { Map, View } from 'ol'
import { Draw } from 'ol/interaction'
import { Tile as TileLayer, Vector as VectorLayer } from 'ol/layer'
import { OSM, Vector as VectorSource } from 'ol/source'
import { fromLonLat } from 'ol/proj'
import { setPointStyle, getCoordinates } from '../../Utility/points'

let pointType, origin, destination
const mapLayer = new TileLayer({
  source: new OSM(),
})

const pointsVecSource = new VectorSource();
const pointsVecLayer = new VectorLayer({ source: pointsVecSource })

const drawAction = new Draw({
  source: pointsVecSource,
  type: 'Point'
})

const map = new Map({
  layers: [mapLayer, pointsVecLayer],
  view: new View({
    center: fromLonLat([31.210705736453235, 30.03138010028067]),
    zoom: 16,
  }),
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
    map.setTarget('map')

    drawAction.on('drawstart', handlePointDraw)
    changePointType('none')

    map.addInteraction(drawAction)
    return () => map.setTarget(undefined)
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
      <div id='map' className='map'></div>
    </>
  )
}

export default CreateRouteMap