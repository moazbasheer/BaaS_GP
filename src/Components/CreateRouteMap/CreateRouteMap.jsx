import 'ol/ol.css'
import './CreateRouteMap.css'
import { useEffect, useState } from "react"
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
  const [deleteMode, setDeleteMode] = useState();

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

    setDeleteMode(deleteAction.getActive())
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

    // deconstructor
    return () => {
      drawAction.un('drawstart', handleDrawAction)
      deleteAction.un('select', handleDeleteAction)
    }
  }, [])

  return (
    <>
      <MapComponent layers={[pointsVecLayer]} interactions={[drawAction, deleteAction, snapAction]} />
      <div className='d-flex justify-content-between'>
        <div>
          <div className=''>
            <button onClick={() => changePointType('origin')} className='btn btn-outline-success'>Origin</button>
            <button onClick={() => changePointType('destination')} className='btn btn-outline-danger'>Destination</button>
          </div>
          <button onClick={() => changePointType('none')} className='btn btn-outline-secondary w-100'>None</button>
        </div>
        <div>
          <button onClick={() => changePointType('delete')} className={`btn btn-danger h-100 mx-1 ${deleteMode && 'bg-white text-danger'}`}>Delete a Point</button>
          <button className='btn btn-danger h-100' onClick={clearPoints}>Clear All</button>
        </div>
      </div>
    </>
  )
}

export default CreateRouteMap