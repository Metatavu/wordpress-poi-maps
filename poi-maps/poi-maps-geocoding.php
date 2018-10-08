<?php
  namespace Metatavu\Miksei;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\POIMapsGeoCoding' ) ) {
    
    class POIMapsGeoCoding {
      
      /**
       * Constructor
       */
      public function __construct() {
        add_action('wp_ajax_geocode', [$this, 'geocode']);
      }

      /**
       * Calls geocoding api and sends results as json
       */
      function geocode() {
        $location = $_POST['data']['location'];
        $location = urlencode($location);
        $boundingBox = '20.6455928891,59.846373196,31.5160921567,70.1641930203';
        $url = sprintf('%s?key=%s&inFormat=kvp&outFormat=json&location=%s&thumbMaps=false&boundingBox=%s', Config::$geocodingUrl, Config::$geoodingSecret, $location, $boundingBox);
        $results = file_get_contents($url);
        wp_send_json($results);
      }

    }
  }
  
  add_action('init', function () {
    new POIMapsGeoCoding();
  });
  
?>
