<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/meet-tech-expert
 * @since             1.0.0
 * @package           Woo_Acm
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Auto Coupon Mail
 * Plugin URI:        https://github.com/meet-tech-expert/woo-acm
 * Description:       This plugin does send email to users if auto-coupon applied.
 * Version:           1.0.0
 * Author:            meet-tech-expert
 * Author URI:        https://github.com/meet-tech-expert
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-acm
 * Domain Path:       /languages
 * WC requires at least: 2.6.0
 * WC tested up to: 3.4.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOO_ACM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-acm-activator.php
 */
function activate_woo_acm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-acm-activator.php';
	Woo_Acm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-acm-deactivator.php
 */
function deactivate_woo_acm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-acm-deactivator.php';
	Woo_Acm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_acm' );
register_deactivation_hook( __FILE__, 'deactivate_woo_acm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-acm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_acm() {

	$plugin = new Woo_Acm();
	$plugin->run();

}
run_woo_acm();