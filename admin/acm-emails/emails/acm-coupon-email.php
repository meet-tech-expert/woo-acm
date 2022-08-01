<?php
/**
 *
 * Coupon Email content template
 *
 * The file is prone to modifications after plugin upgrade or alike; customizations are advised via hooks/filters
 *
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// $email variable fix
$email = isset($email) ? $email : null;

?>

<?php do_action('woocommerce_email_header', $email_heading, $email); ?>

<p><?php _e( 'Thank you for your purchase. Your order has been successfully completed.', 'woocommerce' ); ?></p>

<p><?php _e( 'Use the following coupon for next time purchase:', 'woocommerce' ); ?></p>

<p><strong><?php echo $coupon_codes; ?></strong></p>  

<?php do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email); ?>

<?php do_action('woocommerce_email_footer', $email);?>