import 'ol/ol.css'
import './MapContainer.css'
import { useEffect, useState } from "react"
import { Map, View } from 'ol'
import { Draw } from 'ol/interaction'
import { Tile as TileLayer, Vector as VectorLayer } from 'ol/layer'
import { OSM, Vector as VectorSource } from 'ol/source'
import { fromLonLat } from 'ol/proj'
import { Circle, Fill, Stroke, Style } from 'ol/style'

function MapContainer({ pointType }) {
  const [drawModes, setDrawModes] = useState()
  const [drawMode, setDrawMode] = useState()
  const [map, setMap] = useState()
  const [srcFeature, setSrcFeature] = useState();
  const [stopsFeatures, setStopsFeatures] = useState([]);
  const [destFeature, setDestFeature] = useState();

  const getCircleStyle = mode => {
    const style = {
      radius: 7,
      width: 2,
      strokeColor: '#000',
      fillColor: '#fff'
    }

    switch (mode) {
      case 1:
        return { ...style, fillColor: '#0f0' }
      case 2:
        return { ...style, fillColor: '#00f' }
      case 3:
        return { ...style, fillColor: '#f00' }
      default:
        return style
    }
  }

  const getPointStyle = mode => {
    const style = getCircleStyle(mode)

    const circle = new Circle({
      radius: style.radius,
      stroke: new Stroke({
        width: style.width,
        color: style.strokeColor
      }),
      fill: new Fill({
        color: style.fillColor,
      }),
    })

    return new Style({image: circle})
  }

  useEffect(() => {
    const view = new View({
      center: fromLonLat([31.21057432407688, 30.03039587852848]),
      zoom: 16
    })

    const mapLayer = new TileLayer({
      source: new OSM()
    })

    const drawingVecSource = new VectorSource()
    const drawingVecLayer = new VectorLayer({ source: drawingVecSource })

    const src = new Draw({
      source: drawingVecSource,
      type: 'Point'
    })
    src.on('drawstart', event => {
      event.feature.setStyle(getPointStyle(1))

      drawingVecSource.removeFeature(srcFeature)
      setSrcFeature(event.feature)
    })

    const stops = new Draw({
      source: drawingVecSource,
      type: 'Point'
    })
    stops.on('drawstart', event => event.feature.setStyle(getPointStyle(2)))

    const dest = new Draw({
      source: drawingVecSource,
      type: 'Point'
    })
    dest.on('drawstart', event => event.feature.setStyle(getPointStyle(3)))

    setDrawModes({1: src, 2: stops, 3: dest})

    const initialMap = new Map({
      view: view,
      layers: [mapLayer, drawingVecLayer],
      target: 'map'
    })

    setMap(initialMap)
  }, [])

  useEffect(() => {
    map?.removeInteraction(drawMode)

    if (pointType) {
      map?.addInteraction(drawModes[pointType])
      setDrawMode(drawModes[pointType])
    }
  }, [pointType]);

  return (
    <div id='map' className='map'></div>
  )
}

export default MapContainer