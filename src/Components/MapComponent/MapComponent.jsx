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

function MapComponent({layers, interactions, focusExtent}) {
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
    if (focusExtent) {
      map.getView().fit(focusExtent, map.getSize())

      const currentZoom = map.getView().getZoom()
      map.getView().setZoom(currentZoom * 0.99) // zoom out a little bit
    }
  }, [focusExtent])

  return (
    <div id='map' className='map border border-2 border-primary'></div>
  );
}

export default MapComponent