<?php
  namespace Metatavu\Miksei;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\POIMapsShortcodes' ) ) {
    
    class POIMapsShortcodes {
      
      /**
       * Constructor
       */
      public function __construct() {
        wp_enqueue_style( 'leafletcss', 'https://cdn.metatavu.io/libs/leaflet/1.3.4/leaflet.css');
        wp_enqueue_script( 'stamen', 'https://cdn.metatavu.io/libs/leaflet-tiles/stamen.js', array(), '', false );
        wp_enqueue_script( 'leafletjs', 'https://cdn.metatavu.io/libs/leaflet/1.3.4/leaflet.js', array(), '', false );

        add_shortcode('point_of_interest', [$this, 'pointOfInterestShortCode']);
        add_action('wp_ajax_markers', [$this, 'getMarkers']);
        add_action('wp_ajax_nopriv_markers', [$this, 'getMarkers']);
      }

      /**
       * Get address
       */
      public function getAddress ($postId) {
        $postMeta = get_post_meta($postId, 'poiAddress', true);

        if ($postMeta) {
          return $postMeta;
        }
        return '';
      }

      /**
       * Build POI shortcode
       */
      public function pointOfInterestShortCode ($tagAttrs) {
        if (is_admin()) {
          return;
        }

        wp_register_script( 'map-script-public', plugins_url( 'js/bundle/map-script-public.min.js' , __FILE__ ), array('jquery'), '1.0.0', true );
        wp_localize_script('map-script-public', 'poiMap', [ 'ajaxurl' => admin_url( 'admin-ajax.php' ) ]);
        wp_enqueue_script('map-script-public');
        
        $attrs = shortcode_atts([
          'categories' => ''
        ], $tagAttrs);

        $categories = array();
        if (strlen($attrs['categories']) > 0) {
          $categories = explode(',', str_replace(' ', '', $attrs['categories']));
        }

        if (!empty($categories)) {
          $categoryArray = array_values($categories);
          echo sprintf("<input type='hidden' id='mapCategories' data-categories='%s'>", json_encode($categoryArray));
        }
        echo '<div id="leafletMap" style="height: 400px;"></div>';
      }

      public function getMarkers() {
        global $wpdb;
        
        $categories = $_POST['data']['categories'];

        if (!$categories) {
          $categories = $categoryTaxonomies;
        }

        $posts = $wpdb->get_results("SELECT * FROM  wp_posts WHERE post_type = 'point_of_interest' and post_status = 'publish'");
        $postsByCategory = array();

        foreach ($posts as $currentPost) {
          $latLng = $this->getLatLng($currentPost->ID);
          if ($latLng) {
            $terms = get_the_terms($currentPost->ID, 'poi_categories');
            $categoryArray = array_map(function($category){
              return (string) strtolower($category);
            }, $categories);

            foreach ($terms as $term) {
              if (in_array(strtolower($term->name), $categoryArray) || count($categories) == 0) {
                $object = (object)[];
                $object->latLng = $latLng;
                $object->title = $currentPost->post_title;
                $object->content = wpautop($currentPost->post_content);
                $object->address = $this->getAddress($currentPost->ID);
                $object->id = $currentPost->ID;
                if (get_the_post_thumbnail_url($currentPost->ID)) {
                  $object->icon = get_the_post_thumbnail_url($currentPost->ID);
                } else {
                  $object->icon = "http://icons.iconarchive.com/icons/icons-land/vista-map-markers/256/Map-Marker-Marker-Outside-Chartreuse-icon.png";
                }

                if (empty($postsByCategory) || !array_key_exists($term->name, $postsByCategory)) {
                  $postsByCategory[$term->name] = array();
                }

                array_push($postsByCategory[$term->name], $object);
              }
            } 
          }
        }
        wp_send_json(json_encode($postsByCategory));
      }

      /**
       * Get latLng
       */
      public function getLatLng ($postId) {
        $latLng = array(
          'lat' => get_post_meta($postId, 'poiLat', true),
          'lng' => get_post_meta($postId, 'poiLng', true)
        );

        if (!$latLng['lat'] || !$latLng['lng']) {
          error_log("Couldn't find lat lng coordinates for post " . $postId);
          return false;
        }

        return $latLng;
      }
    }
  }
  
  add_action('init', function () {
    new POIMapsShortcodes();
  });
  
?>
