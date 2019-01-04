<?php
  namespace Metatavu\POI\Settings;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( '\Metatavu\POI\Settings\SettingsUI' ) ) {

    /**
     * UI for settings
     */
    class SettingsUI {

      /**
       * Constructor
       */
      public function __construct() {
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_menu', array($this, 'adminMenu'));
      }

      /**
       * Admin menu action. Adds admin menu page
       */
      public function adminMenu() {
        add_options_page (__( "POI Settings", 'miksei' ), __( "POI Settings", 'miksei' ), 'manage_options', 'poimaps', [$this, 'settingsPage']);
      }

      /**
       * Admin init action. Registers settings
       */
      public function adminInit() {
        register_setting('poimaps', 'poimaps');
        add_settings_section('api', __( "Geo server", 'miksei' ), null, 'poimaps');
        $this->addOption('api', 'key', 'api-key', __( "API Key", 'poimaps'));
        $this->addOption('api', 'url', 'api-url', __( "API URL", 'poimaps'));
      }

      /**
       * Adds new option
       * 
       * @param string $group option group
       * @param string $type option type
       * @param string $name option name
       * @param string $title option title
       */
      private function addOption($group, $type, $name, $title) {
        add_settings_field($name, $title, array($this, 'createFieldUI'), 'poimaps', $group, [
          'name' => $name, 
          'type' => $type
        ]);
      }

      /**
       * Prints field UI
       * 
       * @param array $opts options
       */
      public function createFieldUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $value = Settings::getValue($name);
        echo "<input id='$name' name='" . 'poimaps' . "[$name]' size='42' type='$type' value='$value' />";
      }

      /**
       * Prints settings page
       */
      public function settingsPage() {
        if (!current_user_can('manage_options')) {
          wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        echo '<div class="wrap">';
        echo "<h2>" . __( "POI Maps management", 'miksei') . "</h2>";
        echo '<form action="options.php" method="POST">';
        settings_fields('poimaps');
        do_settings_sections('poimaps');
        submit_button();
        echo "</form>";
        echo "</div>";
      }
    }

  }
  
  if (is_admin()) {
    $settingsUI = new SettingsUI();
  }

?>