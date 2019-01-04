<?php
  namespace Metatavu\POI;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\POI\PoiMapsMetaboxes' ) ) {
    
    /**
     * Class for creating meta boxes for post type process table
     */
    class PoiMapsMetaboxes {

      /**
       * Constructor
       */
      public function __construct() {
        add_action('admin_init', function() {
          wp_enqueue_style( 'admin-styles', plugins_url( 'styles/css/admin.min.css' , __FILE__ ));
          wp_enqueue_style( 'leafletcss', 'https://cdn.metatavu.io/libs/leaflet/1.3.4/leaflet.css');
          wp_enqueue_style( 'esri-geocode', 'https://cdn.metatavu.io/libs/esri-leaflet/2.2.13/esri-leaflet-geocoder.css');
          wp_enqueue_script( 'stamen', 'https://cdn.metatavu.io/libs/leaflet-tiles/stamen.js', array(), '', false );
          wp_enqueue_script( 'leafletjs', 'https://cdn.metatavu.io/libs/leaflet/1.3.4/leaflet.js', array(), '', false );
          wp_enqueue_script( 'esri', 'https://cdn.metatavu.io/libs/esri-leaflet/2.2.13/esri-leaflet.js', array(), '', false );
          wp_enqueue_script( 'esri-geocode', 'https://cdn.metatavu.io/libs/esri-leaflet/2.2.13/esri-leaflet-geocoder.js', array(), '', false );

          add_meta_box( 'mapMetaBox', __( 'Address', 'miksei' ), array($this, 'buildMapMetabox'), 'point_of_interest', 'advanced', 'high' );

          wp_register_script( 'map-script', plugins_url( 'js/bundle/map-script.min.js' , __FILE__ ), array('jquery'), '1.0.0', true );
          error_log(admin_url( 'admin-ajax.php' ));
        });

        add_action( 'save_post_point_of_interest', array($this, 'saveMapMetabox'), 11, 2 );
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
       * Get lat
       */
      public function getLat ($postId) {
        $postMeta = get_post_meta($postId, 'poiLat', true);

        if ($postMeta) {
          return $postMeta;
        }
        return '';
      }

      /**
       * Get lng
       */
      public function getLng ($postId) {
        $postMeta = get_post_meta($postId, 'poiLng', true);

        if ($postMeta) {
          return $postMeta;
        }
        return '';
      }

      /**
       * Build metabox for map
       */
      public function buildMapMetabox () {
        global $post;
        wp_nonce_field( plugin_basename( __FILE__ ), 'mapMetaBox' );

        $icon = get_the_post_thumbnail_url($currentPost->ID);
        wp_localize_script('map-script', 'poiMap', [ 
          'mapIcon' => $icon,
          'ajaxurl' => admin_url( 'admin-ajax.php' )
        ]);
        wp_enqueue_script('map-script');

        echo '<div id="addressInputContainer">';

        echo sprintf('<input id="addressInput" name="poi_address" type="text" value="%s" placeholder="Address"/>', $this->getAddress($post->ID));
        echo sprintf('<button id="searchAddressButton" type="button">%s</button>', __('Search', 'miksei'));

        echo sprintf('<input type="hidden" value="%s" name="poi_lat"/>', $this->getLat($post->ID));
        echo sprintf('<input type="hidden" value="%s" name="poi_lng"/>', $this->getLng($post->ID));

        echo '</div>';

        echo '<div id="leafletMap" style="height: 400px;"></div>';
      }

      /**
       * Save map data when publish/update is clicked
       * 
       * @param $postId Id of WP_Post
       */
      public function saveMapMetabox ($postId) {
        if ( !wp_verify_nonce( $_POST['mapMetaBox'], plugin_basename(__FILE__) )) {
          return;
        }

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
          return;
        }

        if ( 'page' == $_POST['point_of_interest'] ||  'post' == $_POST['point_of_interest']) {
          if ( !current_user_can( 'edit_page', $postId ) || !current_user_can( 'edit_post', $postId )) {
            return;
          }
        }

        $address = $_POST['poi_address'];
        $lat = $_POST['poi_lat'];
        $lng = $_POST['poi_lng'];
        update_post_meta($postId, 'poiAddress', $address);
        update_post_meta($postId, 'poiLat', $lat);
        update_post_meta($postId, 'poiLng', $lng);
      }

      /**
       * Save icon when publish/update is clicked
       * 
       * @param $postId Id of WP_Post
       */
      public function saveIconMetabox ($postId) {
        if ( !wp_verify_nonce( $_POST['iconMetaBox'], plugin_basename(__FILE__) )) {
          return;
        }

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
          return;
        }

        if ( 'page' == $_POST['point_of_interest'] ||  'post' == $_POST['point_of_interest']) {
          if ( !current_user_can( 'edit_page', $postId ) || !current_user_can( 'edit_post', $postId )) {
            return;
          }
        }

        $icon = $_POST['poi-selected-icon'];
        update_post_meta($postId, 'poiMapIcon', $icon);
      }
    }

    add_action('init', function () {
      new PoiMapsMetaboxes();
    });
  }

?>