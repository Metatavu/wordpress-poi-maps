<?php
  namespace Metatavu\Miksei;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\Config' ) ) {
    
    class Config {
      static $geocodingUrl = 'https://open.mapquestapi.com/geocoding/v1/address';
      static $geoodingSecret = 'yjRzW3AornAEEMZhljARpGo5ItGx7DGq';
    }
  }
  
  add_action('init', function () {
    new Config();
  });
  
?>