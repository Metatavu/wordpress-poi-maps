
jQuery(document).ready(function ($) {
  var terrain = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}.{ext}', {
    attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    id: 'leafletMap',
    ext: 'png'
  });

  var map = new L.Map("leafletMap", {
    center: new L.LatLng(61.6914766, 27.259305),
    zoom: 11,
    layers: [terrain]
  });

  var district_boundary = new L.geoJson();
  district_boundary.addTo(map);

  getMarkers();

  function getMarkers () {
    const categories = getCategories();

    $.post(miksei.ajaxurl, {
      'action': 'markers',
      'data': { categories: categories }
    }, (data) => {
      const markers = JSON.parse(data);
      addMarkers(markers);
    }).fail((response) => {
      console.log(response.responseText || response.statusText || 'Unknown error occurred');
    });
  }

  function getCategories () {
    const categoryAttribute = $('#mapCategories').attr('data-categories');
    
    if (categoryAttribute) {
      const categories = JSON.parse(categoryAttribute);
      if (categories && categories.length > 0) {
        return categories;
      }
    }
    
    return null;
  }

  function addMarkers (markers) {
    var baseMaps = {};
    var overlayMaps = {};
    const markerLabels = Object.keys(markers);

    baseMaps['Terrain'] = terrain;

    markerLabels.forEach(markerLabel => {
      let markerArray = [];
      
      markers[markerLabel].forEach(markerObject => {
        let icon = null;
        if (markerObject.icon) {
          icon = L.icon({
            iconUrl: markerObject.icon,
            iconSize:     [40, 40]
          });
        }

        markerArray.push(L.marker([markerObject.latLng.lat, markerObject.latLng.lng], {icon: icon}).bindPopup('<h4>'+markerObject.title+'</h4><small>'+markerObject.address+'</small>' + '<p>'+markerObject.content+'</p>'));
      });
      overlayMaps[markerLabel] = L.layerGroup(markerArray).addTo(map);
    });

    L.control.layers(baseMaps, overlayMaps).addTo(map);
  }

  $.ajax({
  dataType: "json",
  url: "https://gist.githubusercontent.com/VilleKoivukangas/77b78e6925b336744790e74c4578309c/raw/2d46daec1a99b88f8d2f2e11d27fc824968e05c4/powerline.json",
  success: function(data) {
    $(data.features).each(function(key, data) {
      district_boundary.addData(data);
    });
  }
  }).error(function() {
    console.error("Failed to get powerlines geoJSON");
  });

});