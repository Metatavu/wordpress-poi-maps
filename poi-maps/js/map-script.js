
jQuery(document).ready(function ($) {
  var map = new L.Map("leafletMap", {
    center: new L.LatLng(61.6914766, 27.259305),
    zoom: 12
  });

  var Stamen_Toner = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}.{ext}', {
    attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    subdomains: 'abcd',
    minZoom: 0,
    maxZoom: 20,
    ext: 'png'
  });
  map.addLayer(Stamen_Toner);

  var district_boundary = new L.geoJson();
  district_boundary.addTo(map);

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

  map.on('click', function (e) {
    addMarker(e.latlng);
  });
  
  let mapMarker = {};
  if ($('input[name="poi_lat"]').val() && $('input[name="poi_lng"]').val()) {
    var latLng = {
      lat: $('input[name="poi_lat"]').val(),
      lng:  $('input[name="poi_lng"]').val()
    };
    addMarker(latLng);
  }

  function addMarker(latLng) {
    if (!latLng) {
      noResults();
      return;
    }

    if (mapMarker != undefined) {
      map.removeLayer(mapMarker);
    }

    if (poiMap.mapIcon) {
      var icon = L.icon({iconUrl: poiMap.mapIcon, iconSize: [50, 50]});
      mapMarker = L.marker([latLng.lat, latLng.lng], {icon: icon});
    } else {
      mapMarker = L.marker([latLng.lat, latLng.lng]);
    }
    
    mapMarker.addTo(map);
    updateInputs(latLng);
  }

  function processLocationData(data) {
    if (data.results && data.results[0].locations && data.results[0].locations[0] && data.results[0].locations[0].latLng) {
      if (data.results[0].locations[0].adminArea1.toLowerCase() != "fi") {
        alert("Added marker to country: " + data.results[0].locations[0].adminArea1 + ". If this isn't right you can add marker manually by clicking the map and then you can write the addres to the address input.");
      }

      const latLng = data.results[0].locations[0].latLng;
      addMarker(latLng);
      updateInputs(latLng);
    } else {
      noResults();
    }
  }

  function updateInputs (latLng) {
    $('input[name="poi_lat"]').val(latLng.lat);
    $('input[name="poi_lng"]').val(latLng.lng);
  }

  function noResults () {
    alert("No results found. You can add your location manually by clicking the map to add marker and writing the address to text input.");
  }

  $(document).on("click", "#searchAddressButton", () => {
    const address = $("#addressInput").val();

    if (!address) {
      return;
    }

    $.post(miksei.ajaxurl, {
      'action': 'geocode',
      'data': { location: address }
    }, (data) => {
      if (!data) {
        noResults();
        return;
      }

      processLocationData(JSON.parse(data));
    }).fail(function (response) {
      console.log(response.responseText || response.statusText || 'Unknown error occurred');
    });
  });
});