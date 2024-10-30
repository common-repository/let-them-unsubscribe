<?php
/*
Plugin Name: Let Them Unsubscribe
Plugin URI: https://wordpress.org/plugins/let-them-unsubscribe/
Description: Let the users delete their accounts 
Version: 1.2.2
Author: igmoweb
Author URI: http://www.igmoweb.com
Text Domain: let-them-unsubscribe
Domain Path: /lang
*/


class IW_LTU {

	public $settings_menu;
	public $unsubscribe_menu;

	/**
	 * Constructor
	 */
	function __construct() {

		$this->set_globals();
		$this->includes();
		
		// load plugin text domain
		add_action( 'plugins_loaded', array( &$this, 'load_text_domain' ) );

		// Adds an options menu
		add_action( 'init', array ( $this, 'init_plugin' ) );

		register_activation_hook( __FILE__, array( &$this, 'activate' ) );

	} // end constructor

	/**
	 * Set constants needed for the plugin
	 */
	private function set_globals() {
		// Basename
		define( 'IW_LTU_PLUGIN_BASENAME', plugin_dir_path( __FILE__ ) );
		
		// Includes dir
		define( 'IW_LTU_INCLUDES_DIR', IW_LTU_PLUGIN_BASENAME . 'inc/' );

		// Admin dir
		define( 'IW_LTU_ADMIN_DIR', IW_LTU_PLUGIN_BASENAME . 'admin/' );

		define( 'IW_LTU_VERSION', '1.2.2' );
	}

	/**
	 * Include basic files for the plugin
	 */
	private function includes() {

		include_once( IW_LTU_INCLUDES_DIR . 'helpers.php' );

		if ( is_admin() ) {
			include_once( IW_LTU_INCLUDES_DIR . 'admin-page.php' );	
			include_once( IW_LTU_ADMIN_DIR . 'settings-menu.php' );
			include_once( IW_LTU_ADMIN_DIR . 'user-profile.php' );
		}

		include_once( IW_LTU_INCLUDES_DIR . '/widget.php' );
	}

	public function activate() {
		$settings = iw_ltu_get_settings();
		update_option( 'lt_unsubscribe_options', $settings );
		update_option( 'iw_ltu_version', IW_LTU_VERSION );
	}
	
	
	/**
	 * Load the plugin text domain and MO files
	 * 
	 * These can be uploaded to the main WP Languages folder
	 * or the plugin one
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'let-them-unsubscribe', false, '/let-them-unsubscribe/lang/' );
	}

	/**
	 * Initializes the plugin
	 */
	public function init_plugin() {

		if ( is_admin() ) {
			$args = array(
				'parent' => 'options-general.php',
				'menu_title' => __( 'Let Them Unsubscribe', 'let-them-unsubscribe' ),
				'page_title' => __( 'Let Them Unsubscribe Settings', 'let-them-unsubscribe' ),
				'forbidden_message' => __( 'You do not have enough permissions to access to this page', 'let-them-unsubscribe' ),
			);
			$this->settings_menu = new IW_LTU_Settings_Menu( 'ltu_settings', 'manage_options', $args );

			
			if ( iw_ltu_user_can_unsubscribe() ) {
				include_once( IW_LTU_ADMIN_DIR . 'unsubscribe-menu.php' );
				$args = array(
					'parent' => 'users.php',
					'menu_title' => __( 'Delete your account', 'let-them-unsubscribe' ),
					'page_title' => __( 'Delete your account', 'let-them-unsubscribe' ),
					'forbidden_message' => __( 'You do not have enough permissions to access to this page', 'let-them-unsubscribe' ),
				);
				$this->unsubscribe_menu = new IW_LTU_Unsubscribe_Menu( 'ltu_unsubscribe', 'read', $args );
			} 
		}

		$this->maybe_upgrade();

	}

	private function maybe_upgrade() {
		$current_version = get_option( 'iw_ltu_version' );

		if ( IW_LTU_VERSION === $current_version )
			return;

		if ( $current_version === false ) {
			$this->activate();
			return;
		}

		// Upgrade the plugin
		include_once( IW_LTU_INCLUDES_DIR . 'upgrade.php' );

		update_option( 'iw_ltu_version', IW_LTU_VERSION );
	}

  
} // end class

global $iw_ltu;
$iw_ltu = new IW_LTU();