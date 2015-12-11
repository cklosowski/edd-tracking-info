<?php
/*
Plugin Name: Easy Digital Downloads - Tracking Info
Description: Add shipment tracking information to payments
Plugin URI: https://easydigitaldownlaods.com
Author: Chris Klosowski
Author URI: https://easydigitaldownloads.com
Version: 1.0
License: GPL2
Text Domain: edd-tracking-info
Contributors: cklosows
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EDD_Tracking_Info' ) ) {

class EDD_Tracking_Info {

	private static $instance;

	private function __construct() {
		if ( ! class_exists( 'Easy_Digital_Downloads' ) ){
			return;
		}

		$this->constants();
		$this->includes();
		$this->hooks();
		$this->filters();
	}

	static public function instance() {

		if ( !self::$instance ) {
			self::$instance = new EDD_Tracking_Info();
		}

		return self::$instance;

	}

	private function constants() {

		// Plugin version
		if ( ! defined( 'EDD_TI_VERSION' ) ) {
			define( 'EDD_TI_VERSION', '1.0' );
		}

		// Plugin Folder Path
		if ( ! defined( 'EDD_TI_PLUGIN_DIR' ) ) {
			define( 'EDD_TI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'EDD_TI_PLUGIN_URL' ) ) {
			define( 'EDD_TI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'EDD_TI_PLUGIN_FILE' ) ) {
			define( 'EDD_TI_PLUGIN_FILE', __FILE__ );
		}

	}

	private function includes() {
		include EDD_TI_PLUGIN_DIR . 'includes/functions.php';
		if ( is_admin() ) {
			include EDD_TI_PLUGIN_DIR . 'includes/admin/metabox.php';
			include EDD_TI_PLUGIN_DIR . 'includes/admin/settings.php';
		}
	}

	private function hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	private function filters() {}

	public function register_settings() {}

	public function admin_scripts() {
		wp_register_style( 'edd-tracking-info', EDD_TI_PLUGIN_URL . 'assets/styles.css', array(), EDD_TI_VERSION );
		wp_enqueue_style( 'edd-tracking-info' );

		wp_register_script( 'edd-tracking-info', EDD_TI_PLUGIN_URL . 'assets/scripts.js', array( 'jquery' ), EDD_TI_VERSION );
		wp_enqueue_script( 'edd-tracking-info' );
	}

	function setting_section_callback() {}


} // End WP_CodeShare class

} // End Class Exists check

function edd_load_tracking_info() {
	return EDD_Tracking_Info::instance();
}
add_action( 'plugins_loaded', 'edd_load_tracking_info' );
