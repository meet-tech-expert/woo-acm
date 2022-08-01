<?php

/**
* The admin-specific functionality of the plugin.
*
* @link       https://github.com/meet-tech-expert
* @since      1.0.0
*
* @package    Woo_Acm
* @subpackage Woo_Acm/admin
*/

/**
* The admin-specific functionality of the plugin.
*
* Defines the plugin name, version, and two examples hooks for how to
* enqueue the admin-specific stylesheet and JavaScript.
*
* @package    Woo_Acm
* @subpackage Woo_Acm/admin
* @author     Rinkesh Gupta <gupta.rinkesh1990@gmail.com>
*/
class Woo_Acm_Admin{

	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;

	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;

	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public function __construct( $plugin_name, $version ){

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles(){

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Woo_Acm_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Woo_Acm_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-acm-admin.css', array(), $this->version, 'all' );

	}

	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts(){

		/**
		* This function is provided for demonstration purposes only.
		*
		* An instance of this class should be passed to the run() function
		* defined in Woo_Acm_Loader as all of the hooks are defined
		* in that particular class.
		*
		* The Woo_Acm_Loader will then create the relationship
		* between the defined hooks and the functions defined in this
		* class.
		*/

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-acm-admin.js', array( 'jquery' ), '', false );

	}
	/**
	* Add a new settings tab to the WooCommerce settings tabs array.
	*
	* @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
	* @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
	*/
	public function acm_add_settings_tab( $settings_tabs ){
		$settings_tabs['acm'] = __( 'Auto Coupon Mail', $this->plugin_name );
		return $settings_tabs;
	}
	/**
	* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	*
	* @uses woocommerce_admin_fields()
	* @uses self::get_settings()
	*/
	public function acm_settings_tab(){
		include_once "partials/woo-acm-admin-display.php";
	}
	/**
	* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	*
	* @uses woocommerce_update_options()
	* @uses self::get_settings()
	*/
	public static function acm_update_settings(){
    	
		$sales  = $_POST['wc_acm_sales_amount'];
		$coupon = $_POST['wc_acm_coupon_amount'];
		if( count($sales) == count($coupon) ){
    		
			for($i=0;$i<count($sales);$i++){
				$data[] = array('sales' => $sales[$i],'coupon' => $coupon[$i]);
			}
			$enable_acm = (array_key_exists('wc_enable_acm',$_POST))?$_POST['wc_enable_acm']:'0';
			update_option('wc_enable_acm',$enable_acm);	
			update_option('wc_acm_settings',maybe_serialize($data) );	
		}
    	
	}
    
	public function acm_coupon_woocommerce_emails($email_classes){
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acm-coupon-email.php';
		$email_classes['ACM_Coupon_Email'] = new ACM_Coupon_Email(); // add to the list of email classes that WooCommerce loads
		return $email_classes;
	}
	public function acm_cart_calculate_fees(){
		global $woocommerce;
		global $wpdb;
		$user_id = get_current_user_id();
	     
		if(!$user_id)return;
		 
		$enable        =  get_option('wc_enable_acm');
		if($enable && $enable!='1')return;
		 
		$coupon_code =  get_user_meta($user_id,'_acm_coupon',TRUE);
		//var_dump($coupon_code);
		//if(!$coupon_code)return;
		 
		$postTable = $wpdb->prefix.'posts';
		$postMetaTable = $wpdb->prefix.'postmeta';
		 
		$sql = "SELECT t1.post_title FROM $postTable t1 JOIN $postMetaTable t2 ON t1.ID = t2.post_id  WHERE t1.post_type = 'shop_coupon' AND t2.meta_key = 'coupon_type' AND t2.meta_value = 'acm'";
		 
		$res = $wpdb->get_results($sql,ARRAY_A);
		if($wpdb->num_rows >0){
			//print_r($res);
			foreach($res as $cup){
		 		
				if( $woocommerce->cart->has_discount( $cup['post_title'] ) ){
					if(!$coupon_code || $coupon_code!= $cup['post_title']){
						wc_clear_notices();
						$coupon = new WC_Coupon( $cup['post_title'] );
						WC()->cart->remove_coupon( $cup['post_title'] );
						$coupon->add_coupon_message( WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED );
						return;
					}
				}
			} 
		 	
		}
		return;
	}
	public function acm_create_order($post_id, $post, $update){
		
		if($post->post_type == 'shop_order'){
			$order = wc_get_order( $post_id );
			$user_id = get_current_user_id();
			$coupon_code =  get_user_meta($user_id,'_acm_coupon',TRUE);
			
			if( $order->get_used_coupons() ){
				
				foreach( $order->get_used_coupons() as $coupon){
					if($coupon_code && strtolower($coupon_code) == $coupon){
						delete_user_meta( $user_id, '_acm_coupon' );	
					}
				}
			}
		}
		
	}
}
