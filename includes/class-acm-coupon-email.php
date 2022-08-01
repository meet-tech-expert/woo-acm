<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Welcome Email class used to send out welcome emails to customers purchasing a course
 *
 * @package WooCommerce\Emails
 */
define('COUPON_NAME','SDC');
class ACM_Coupon_Email extends WC_Email {
	
	/**
	 * Set email defaults
	 */
	public function __construct() {
		// Unique ID for custom email
		$this->id = 'acm_coupon_email';
		// Is a customer email
		$this->customer_email = true;
		
		// Title field in WooCommerce Email settings
		$this->title = __( 'Coupon Email', 'woocommerce' );

		// Description field in WooCommerce email settings
		$this->description = __( 'Coupon email is sent when order is completed.', 'woocommerce' );

		// Default heading and subject lines in WooCommerce email settings
		$this->subject = apply_filters( 'acm_coupon_email_default_subject', __( 'Coupon Discount', 'woocommerce' ) );
		$this->heading = apply_filters( 'acm_coupon_email_default_heading', __( 'Welcome to Site', 'woocommerce' ) );
		       
		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->placeholders   = array(
				'{site_title}'   => $this->get_blogname(),
				'{order_date}'   => '',
				'{order_number}' => '',
				'{coupon_code1}' => '',
			);  
         $override_path = get_theme_file_path(). '/woocommerce/acm-emails/emails/acm-coupon-email.php';
        if (file_exists($override_path)) {
        	$this->template_base  = get_theme_file_path() . '/woocommerce/';	// Fix the template base lookup for use on admin screen template path display
        }else{
			$this->template_base  = plugin_dir_path( dirname( __FILE__ ) ) .  'admin/';	// Fix the template base lookup for use on admin screen template path display
		}
		
		$this->template_html  = 'acm-emails/emails/acm-coupon-email.php';
		$this->template_plain = 'acm-emails/emails/plain/acm-coupon-email.php';

		// Trigger email when payment is complete
		//add_action( 'woocommerce_order_status_changed', array( $this, 'trigger' ),10,1 ); 
		
		add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'trigger' ),10,1 );
		add_action( 'woocommerce_order_status_failed_to_completed', array( $this, 'trigger' ),10,1 );
		add_action( 'woocommerce_order_status_processing_to_completed', array( $this, 'trigger' ),10,1 );
		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();

	}


	/**
	 * Prepares email content and triggers the email
	 *
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {
		// Bail if no order ID is present
		if ( ! $order_id )
			return;
		
		if( self::acm_coupon_status()!='1' ){
				return;
		}
		// Send coupon email only once and not on every order status change		
		if ( ! get_post_meta( $order_id, '_acm_coupon_email_sent', true ) ) {
			// setup order object
			$this->object = new WC_Order( $order_id );
			    
     		// get order items as array
			$order_items = $this->object->get_items();

			/* Proceed with sending email */  
			
			$this->recipient = $this->object->billing_email;

			// replace variables in the subject/headings
			$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
			$this->placeholders['{order_number}'] = $this->object->get_order_number();
			

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}
			
			$coupon =  $this->acmGetCoupon($order_id);
			if($coupon && !empty($coupon)){
				
				$customer_id = get_post_meta($order_id, '_customer_user', true);
				update_user_meta($customer_id,'_acm_coupon',$coupon['name']);
				
				$this->coupon_codes =  $coupon['name'];
				
			}else{
				return;
			}  
			
			// All well, send the email
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			// add order note about the same
			$this->object->add_order_note( sprintf( __( '%s email sent to the customer.', 'woocommerce' ), $this->title ) );
			// Set order meta to indicate that the welcome email was sent
			update_post_meta( $this->object->id, '_acm_coupon_email_sent', 1 );
		}   
		
	}
	
	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'					=> $this->object,
			'email_heading'			=> $this->get_heading(),
			'sent_to_admin'			=> false,
			'plain_text'			=> false,
			'email'					=> $this,
			'coupon_codes'			=> $this->coupon_codes
		),'acm-emails/',$this->template_base );
	}


	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'					=> $this->object,
			'email_heading'			=> $this->get_heading(),
			'sent_to_admin'			=> false,
			'plain_text'			=> true,
			'email'					=> $this,
			'coupon_codes'		    => $this->coupon_codes
		),'acm-emails/',$this->template_base );
	}


	/**
	 * Initialize settings form fields
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'no'
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'     => array(
					'plain'	    => __( 'Plain text', 'woocommerce' ),
					'html' 	    => __( 'HTML', 'woocommerce' ),
					//'multipart' => __( 'Multipart', 'woocommerce' ),
				)
			)
		);
	}
	public static function acm_coupon_status(){
			$enable        =  get_option('wc_enable_acm');
			return ($enable && $enable=='1')? TRUE:FALSE;
	}
	public function acmGetCoupon($order_id){
		 $order = wc_get_order( $order_id );
		 $total = $order->get_total();
		 $settings =  get_option('wc_acm_settings');
		 $data = array();
		 $coupon_name = '';
		 $coupon_id = '';
		 $info = '';
		 if($settings){
			$data = maybe_unserialize($settings);
			if(!empty($data)){
				foreach($data as $row){
					 $info[$row['coupon']] = $row['sales'];	
				}
				$coupon_amount = $this->filterCoupon($total, $info);
				if($coupon_amount){			
					 $coupon_name = COUPON_NAME.$coupon_amount.'OFF';
					 $coupon_id = $this->createCoupon($coupon_amount,$coupon_name,$order); 
					 return array('name' => $coupon_name,'id' => $coupon_id);
					
				}
				return false; 
			}
			return false;
		 }
		 return false;
		
	}
	public function filterCoupon($value,$info){
	$min =  min($info);

	$max = max($info);
	if($value < $min){
		return 'false';	
	}elseif($value == $min){
		return $key = array_search($min, $info);
	}elseif($value >= $max){
		return $key = array_search($max, $info);
	}else{
		   foreach ($info as $coup => $amount) {
		      if($amount > $value ){
			  	unset($info[$coup]);
			  }elseif($amount == $value){
			  	return $coup;
			  }
		   }
		$diff_array = array();
		foreach ($info as $coup => $amount) {
			$diff = abs($amount-$value);
			$diff_array[$coup] = $diff;
		}
		return array_search(min($diff_array), $diff_array);	
	}
	
  }
	public function createCoupon($coupon_amount,$coupon_name,$order){
		global $wpdb;
	    $sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1;", $coupon_name );
	    $coupon_id = $wpdb->get_var( $sql );
    if ( empty( $coupon_id ) ) {
		// Create a coupon with the properties you need
        $data = array(
            'discount_type'              => 'fixed_cart',
            'coupon_amount'              => $coupon_amount, // value
            'individual_use'             => 'no',
            'product_ids'                => '',
            'exclude_product_ids'        => '',
            'usage_limit'                => '',
            'usage_limit_per_user'       => '',
            'limit_usage_to_x_items'     => '',
            'usage_count'                => '',
            'expiry_date'                => '', // YYYY-MM-DD
            'free_shipping'              => 'no',
            'product_categories'         => array(),
            'exclude_product_categories' => array(),
            'exclude_sale_items'         => 'no',
            'minimum_amount'             => '',
            'maximum_amount'             => '',
            'customer_email'             => array(),
            'coupon_type'                => 'acm'
        );
        // Save the coupon in the database
        $coupon = array(
            'post_title' => $coupon_name,
            'post_content' => 'The coupon generated for specific user.',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'shop_coupon'
        );
        $new_coupon_id = wp_insert_post( $coupon );
        // Write the $data values into postmeta table
        foreach ($data as $key => $value) {
            update_post_meta( $new_coupon_id, $key, $value );
        }
        
        return $new_coupon_id;
      }
      
       return $coupon_id;
		
	}
		
}