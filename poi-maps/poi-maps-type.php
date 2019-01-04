<?php
  namespace Metatavu\Miksei\Type;
  
  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

 
  if (!class_exists( '\Metatavu\Miksei\Type' ) ) {
  
    /**
     * Custom post type for exports 
     */
    class Type {
      
      /**
       * Constructor
       */
      public function __construct() {
        $this->registerPOI();
        add_action( 'admin_menu', array($this, 'processAdminMenuItems'));

      }

      /**
       * Show and hide in admin menu
       */
      public function processAdminMenuItems() {
        add_menu_page(__('POI', 'miksei'), __('POI', 'miksei'), 'manage_options', 'edit.php?post_type=point_of_interest', '', 'dashicons-location-alt', 50);
        add_submenu_page('edit.php?post_type=point_of_interest', __('Add new POI', 'miksei'), __('Add new POI', 'miksei'), 'manage_options', 'post-new.php?post_type=point_of_interest', '');
      }

      /**
       * Registers a custom post type
       */
      public function registerPOI() {
        register_post_type('point_of_interest', [
          'labels' => [
              'name'               => __( 'Point of interest', 'miksei' ),
              'singular_name'      => __( 'Point of interest', 'miksei' ),
              'add_new'            => __( 'Add Point of interest', 'miksei' ),
              'add_new_item'       => __( 'Add New Point of interest', 'miksei' ),
              'edit_item'          => __( 'Edit Point of interest', 'miksei' ),
              'new_item'           => __( 'New Point of interest', 'miksei' ),
              'view_item'          => __( 'View Point of interest', 'miksei' ),
              'search_items'       => __( 'Search Point of interest', 'miksei' ),
              'not_found'          => __( 'No Point of interests found', 'miksei' ),
              'not_found_in_trash' => __( 'No Point of interests in trash', 'miksei' ),
              'all_items'          => __( 'Point of interests', 'miksei' )
          ],
          'menu_icon' => 'dashicons-location-alt',
          'show_in_menu' => false,
          'public' => true,
          'taxonomies' => array('poiCategories'),
          'has_archive' => true,
          'show_in_rest' => true,
          'hierarchical' => true,
          'supports' => array(
            'editor',
            'title',
            'thumbnail',
            'page-attributes'
          ),
        ]);
      }
    }
  }
  add_action ('init', function () {
    new Type();
  });
?>