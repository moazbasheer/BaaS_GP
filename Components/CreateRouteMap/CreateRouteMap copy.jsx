import 'ol/ol.css'
import './CreateRouteMap.css'
import { useEffect, useState } from "react"
import { Map, View } from 'ol'
import { Draw, Snap } from 'ol/interaction'
import { Tile as TileLayer, Vector as VectorLayer } from 'ol/layer'
import { OSM, Vector as VectorSource } from 'ol/source'
import { fromLonLat, toLonLat } from 'ol/proj'
import { Circle, Fill, Stroke, Style, Text } from 'ol/style'
import {Polyline} from 'ol/format'
import routeService from '../../Services/routes'
import { Feature } from 'ol'

let pointType, origin, destination
let stops = []
const setPointStyle = point => {
  const baseStyle = {
    radius: 7,
    strokeColor: '#000',
    strokeWidth: 3,
    textOffsetY: 16,
    textStyle: 'bold 16px sans-serif'
  }

  let style
  if (point.type === 'origin') {
    style = new Style({
      image: new Circle({
        radius: baseStyle.radius,
        fill: new Fill({
          color: '#0f0',
        }),
        stroke: new Stroke({
          color: baseStyle.strokeColor,
          width: baseStyle.strokeWidth,
        })
      }),
      text: new Text({
        text: 'Origin',
        offsetY: baseStyle.textOffsetY,
        font: baseStyle.textStyle
      })
    })
  }
  else if (point.type === 'stop') {
    style = new Style({
      image: new Circle({
        radius: baseStyle.radius,
        fill: new Fill({
          color: '#00f',
        }),
        stroke: new Stroke({
          color: baseStyle.strokeColor,
          width: baseStyle.strokeWidth,
        })
      }),
      text: new Text({
        text: `Stop #${point.index}`,
        offsetY: baseStyle.textOffsetY,
        font: baseStyle.textStyle
      })
    })
  }
  else if (point.type === 'destination')
    style = new Style({
      image: new Circle({
        radius: baseStyle.radius,
        fill: new Fill({
          color: '#f00',
        }),
        stroke: new Stroke({
          color: baseStyle.strokeColor,
          width: baseStyle.strokeWidth,
        })
      }),
      text: new Text({
        text: 'Destination',
        offsetY: baseStyle.textOffsetY,
        font: baseStyle.textStyle
      })
    })
  else {
    style = baseStyle
  }

  point.setStyle(style)
}

const mapLayer = new TileLayer({
  source: new OSM(),
})

const pointsVecSource = new VectorSource();
const pointsVecLayer = new VectorLayer({ source: pointsVecSource })

const drawAction = new Draw({
  source: pointsVecSource,
  type: 'Point'
})

const snap = new Snap({
  source: pointsVecSource
})

const pathVecSource = new VectorSource()
const pathVecLayer = new VectorLayer({
  source: pathVecSource,
  style: new Style({
    stroke: new Stroke({
      color: '#00688bCC',
      width: 7
    })
  })
})

const map = new Map({
  layers: [mapLayer, pathVecLayer, pointsVecLayer],
  view: new View({
    center: fromLonLat([31.210705736453235, 30.03138010028067]),
    zoom: 16,
  }),
})

const CreateRouteMap = () => {
  const [time, setTime] = useState()
  const [distance, setDistance] = useState()

  const clearPath = () => {
    setTime(null)
    setDistance(null)
    pathVecSource.clear()
  }

  const clearPoints = () => {
    origin = null
    stops = []
    destination = null
    pointsVecSource.clear()
  }

  const clearEverything = () => {
    clearPoints()
    clearPath()
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
    else if (pointType === 'stop') {
      stops.push(point)
      stops.forEach((stop, i) => stop.index = i + 1)
    }

    setPointStyle(point)
    clearPath()
  }

  const changePointType = type => {
    pointType = type

    // enabling/disabling drawing
    drawAction.setActive(pointType !== 'none')
  }

  // get coordinates of a feature (point)
  const getCoordinates = feature => {
    const coords = toLonLat(feature.getGeometry().getCoordinates())

    // we want latitude first so we reverse the array
    return coords.reverse()
  }

  const computePath = async () => {
    let points = []

    points.push(getCoordinates(origin))
    stops.forEach(stop => points.push(getCoordinates(stop)))
    points.push(getCoordinates(destination))

    const result = await routeService.getRoute(points)
    console.log(result)

    const path = new Polyline({}).readGeometry(result.paths[0].points, {
      dataProjection: 'EPSG:4326',
      featureProjection: 'EPSG:3857',
    })

    const pathFeature = new Feature({
      geometry: path
    })

    // delete previous path if any
    clearPath()
    pathVecSource.addFeature(pathFeature)
    setTime(result.paths[0].time)
    setDistance(result.paths[0].distance)
  }

  const getDistanceString = () => {
    return (distance / 1000).toFixed(2) + ' km'
  }

  const getTimeString = () => {
    const seconds = time / 1000
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds - hours * 3600) / 60)

    const list = []
    if (hours > 0) {
      list.push(`${hours} hour(s)`)
    }

    if(minutes > 0) {
      list.push(`${minutes} minute(s)`)
    }

    if (list.length > 0) {
      return list.join(', ')
    }
    else {
      return '<1 minute'
    }
  }

  useEffect(() => {
    map.setTarget('map')

    drawAction.on('drawstart', handlePointDraw)
    changePointType('none')

    map.addInteraction(drawAction)
    map.addInteraction(snap)
  }, [])

  return (
    <>
      <div>
        <button onClick={() => changePointType('origin')}>Origin</button>
        <button onClick={() => changePointType('stop')}>Stop</button>
        <button onClick={() => changePointType('destination')}>Destination</button>
        <button onClick={() => changePointType('none')}>None</button>
      </div>
      <div>
        <button onClick={clearEverything}>Clear Everything</button>
        <button onClick={computePath}>Compute Path</button>
      </div>
      <div>
        <p>Estimated Time: {time ? getTimeString() : '--'}</p>
        <p>Distance: {distance ? getDistanceString() : '--'}</p>
      </div>
      <div id='map' className='map'></div>
    </>
  )
}

export default CreateRouteMap