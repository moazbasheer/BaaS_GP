import { Feature } from 'ol';
import { Polyline } from 'ol/format';
import { LineString, Point } from 'ol/geom';
import { fromLonLat, toLonLat } from 'ol/proj';
import {
  Circle, Fill, RegularShape, Stroke, Style, Text,
} from 'ol/style';

export const setPointStyle = (point) => {
  const baseStyle = {
    radius: 7,
    strokeColor: '#000',
    strokeWidth: 3,
    textOffsetY: 16,
    textStyle: 'bold 16px sans-serif',
  };

  let style;
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
        }),
      }),
    });
  } else if (point.type === 'destination') {
    style = new Style({
      image: new Circle({
        radius: baseStyle.radius,
        fill: new Fill({
          color: '#f00',
        }),
        stroke: new Stroke({
          color: baseStyle.strokeColor,
          width: baseStyle.strokeWidth,
        }),
      }),
    });
  } else {
    style = new Style({
      image: new Circle({
        radius: baseStyle.radius,
        fill: new Fill({
          color: '#00f',
        }),
        stroke: new Stroke({
          color: baseStyle.strokeColor,
          width: baseStyle.strokeWidth,
        }),
      }),
    })
  }

  style.setText(new Text({
    text: point.name,
    offsetY: baseStyle.textOffsetY,
    font: baseStyle.textStyle,
  }));

  return style
};

// get coordinates of a feature (point) in the form of lat long
export const pointToCoordinates = (feature) => {
  const coords = toLonLat(feature.getGeometry().getCoordinates());

  // we want latitude first so we reverse the array
  return coords.reverse();
};

// convert coordinates of form lat long to a point on map
export const coordinatesToPoint = (coordinates) => (
  new Feature(
    new Point(
      fromLonLat(coordinates.reverse()),
    ),
  )
);

// compressed string to polyline feature
export const stringToPolyline = (string) => (
  new Feature(
    new Polyline({}).readGeometry(string, {
      dataProjection: 'EPSG:4326',
      featureProjection: 'EPSG:3857',
    }),
  )
);

// styling function for easier view of path adding arrow on each point on the path
export const setPathStyle = (polyline) => {
  let styles = [
    new Style({
      stroke: new Stroke({
        color: '#00688bbb',
        width: 7
      })
    })
  ]

  // arrows
  // const maxLength = 50
  // polyline.getGeometry().forEachSegment((start, end) => {
  //   let dx = end[0] - start[0]
  //   let dy = end[1] - start[1]
  //   const length = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2))
    
  //   // don't guiding arrows if segment length is longer than max length
  //   if (length > maxLength) {
  //     return
  //   }
    
  //   let rotation = Math.atan2(dy, dx)
  //   const arrowShape = new RegularShape({
  //     fill: new Fill({ color: '#002447' }),
  //     points: 3,
  //     radius: 9,
  //     rotation: -rotation,
  //     angle: Math.PI / 2
  //   })

  //   const arrowStyle = new Style({
  //     geometry: new Point(start),
  //     image: arrowShape
  //   })

  //   styles.push(arrowStyle)
  // })

  return styles
}