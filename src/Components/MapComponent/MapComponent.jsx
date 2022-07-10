import { View } from "ol";
import { Map } from "ol";
import TileLayer from "ol/layer/Tile";
import { fromLonLat } from "ol/proj";
import { OSM } from "ol/source";
import { useEffect } from "react";

const mapLayer = new TileLayer({
  source: new OSM(),
})

let map = new Map({
  layers: [mapLayer],
  view: new View({
    center: fromLonLat([31.210705736453235, 30.03138010028067]),
    zoom: 16,
  }),
})

function MapComponent({layers, interactions, focusGeometry}) {
  useEffect(() => {
    layers?.forEach(l => map.addLayer(l))
    interactions?.forEach(i => map.addInteraction(i))

    map.setTarget('map')

    // deconstructor
    return () => {
      layers?.forEach(l => {
        l.getSource().clear()
        map.removeLayer(l)
      })
      interactions?.forEach(i => map.removeInteraction(i))
      map.setTarget(undefined)
    }
  }, [])

  useEffect(() => {
    if (focusGeometry) {
      map.getView().fit(focusGeometry)
    }
  }, [focusGeometry])

  return (
    <div id='map' className='map'></div>
  );
}

export default MapComponent