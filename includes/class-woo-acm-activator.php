<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/meet-tech-expert
 * @since      1.0.0
 *
 * @package    Woo_Acm
 * @subpackage Woo_Acm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woo_Acm
 * @subpackage Woo_Acm/includes
 * @author     Rinkesh Gupta <gupta.rinkesh1990@gmail.com>
 */
class Woo_Acm_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
			/**
			* Check if WooCommerce is active
			**/
			if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )  ) {
				
				// Deactivate the plugin
				deactivate_plugins(__FILE__);
				
				// Throw an error in the wordpress admin console
				$error_message = __('This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugins to be active!', 'woocommerce');
				die($error_message);
				
			}
			

	}

}
