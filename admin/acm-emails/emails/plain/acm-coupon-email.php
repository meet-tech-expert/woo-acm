<?php
/**
 * Coupon Email content template(plain text)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";

echo _e( 'Thank you for your purchase. Your order has been successfully completed.', 'woocommerce' ). "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo _e( 'Use the following coupon for next time purchase:', 'woocommerce' ). "\n\n";

echo "= " . $coupon_codes . " =\n\n";

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
