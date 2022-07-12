import 'ol/ol.css'
import './CreateRouteMap.css'
import { useEffect } from "react"
import { Map, View } from 'ol'
import { Draw, Select, Snap } from 'ol/interaction'
import { Tile as TileLayer, Vector as VectorLayer } from 'ol/layer'
import { OSM, Vector as VectorSource } from 'ol/source'
import { setPointStyle } from '../../Utility/map'
import MapComponent from '../MapComponent/MapComponent'

let pointType
let endpoints = {}
const pointsVecSource = new VectorSource();
const pointsVecLayer = new VectorLayer({
  source: pointsVecSource,
  style: setPointStyle
})

const drawAction = new Draw({
  source: pointsVecSource,
  type: 'Point'
})

const snapAction = new Snap({
  source: pointsVecSource
})

const deleteAction = new Select({
  layers: [pointsVecLayer]
})

const CreateRouteMap = ({ setEndpoints }) => {
  const setEndpointsState = () => {
    setEndpoints(endpoints)
  }

  const clearPoints = () => {
    endpoints = {}
    pointsVecSource.clear()
    setEndpointsState()
  }

  const handleDrawAction = event => {
    const point = event.feature
    point.type = pointType
    
    // remove previous instance of the origin/destination
    pointsVecSource.removeFeature(endpoints[pointType])
    endpoints[pointType] = point
    
    const input = window.prompt('Enter a name for the point')
    point.name = input ? input : pointType

    setEndpointsState()
  }

  const changePointType = type => {
    pointType = type

    drawAction.setActive(false)
    deleteAction.setActive(false)

    if (pointType === 'delete') {
      deleteAction.setActive(true)
    }
    else {
      deleteAction.getFeatures().clear()
    }

    if (pointType === 'origin' || pointType === 'destination') {
      drawAction.setActive(true)
    }
  }

  const handleDeleteAction = event => {
    const feature = event.selected[0]

    pointsVecSource.removeFeature(feature)
    endpoints[feature.type] = null
  }

  useEffect(() => {
    drawAction.on('drawstart', handleDrawAction)
    deleteAction.on('select', handleDeleteAction)
    changePointType('none')
  }, [])

  return (
    <>
      <div>
        <button onClick={() => changePointType('origin')}>Origin</button>
        <button onClick={() => changePointType('destination')}>Destination</button>
        <button onClick={() => changePointType('delete')}>Delete Point</button>
        <button onClick={() => changePointType('none')}>None</button>
      </div>
      <div>
        <button onClick={clearPoints}>Clear All</button>
      </div>
      <MapComponent layers={[pointsVecLayer]} interactions={[drawAction, deleteAction, snapAction]} />
    </>
  )
}

export default CreateRouteMap