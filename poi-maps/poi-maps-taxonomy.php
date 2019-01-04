<?php
  namespace Metatavu\Miksei\Miksei;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  add_action('init', function () {
  	register_taxonomy('poi_categories', 'point_of_interest', [
  	  'label' =>  __('Point of interest categories', 'miksei'),
  	  'rewrite' => array( 'slug' => 'categories' ),
  	  'show_ui' => true, 
      'query_var' => true,
      'hierarchical'  => false,
      'show_in_rest' => true
  	]);
  	
  });
  
?>
