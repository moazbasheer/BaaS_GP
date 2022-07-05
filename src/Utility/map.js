import { Feature } from "ol"
import { Polyline } from "ol/format"
import { Point } from "ol/geom"
import { fromLonLat, toLonLat } from "ol/proj"
import { Circle, Fill, Stroke, Style, Text } from "ol/style"

export const setPointStyle = point => {
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
  else if (point.type === 'destination') {
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
  }

  point.setStyle(style)
}

// get coordinates of a feature (point) in the form of lat long
export const getCoordinates = feature => {
  const coords = toLonLat(feature.getGeometry().getCoordinates())

  // we want latitude first so we reverse the array
  return coords.reverse()
}

// convert coordinates of form lat long to a point on map
export const coordinatesToPoint = coordinates => (
  new Feature(
    new Point(
      fromLonLat(coordinates.reverse())
    )
  )
)

// compressed string to polyline feature
export const stringToPolyline = string => (
  new Feature(
    new Polyline({}).readGeometry(string, {
      dataProjection: 'EPSG:4326',
      featureProjection: 'EPSG:3857',
    })
  )
)