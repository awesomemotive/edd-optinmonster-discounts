<?php
/*
 * Plugin Name: Easy Digital Downloads - OptinMonster Discount for Submission
 * Description: Allows you to generate a discount code and send it to the email address when a user submits their email through OptinMonster
 * Author: Pippin Williamson
 * Version: 1.0
 * License: GPLv3
 */

class EDD_OM_Discount_For_Submission {

	public function __construct() {
		add_action( 'optin_monster_after_lead_stored', array( $this, 'process_lead' ) );
	}

	public function process_lead( $lead, $class_object ) {
		
		$code = $this->generate_discount( $lead['lead_email'] );
		$this->send_discount( $lead['lead_email'], $lead['lead_name'], $code );
	}

	public function generate_discount( $email = '' ) {

		// Generate a 15 character code
		$code    = substr( md5( $email ), 0, 15 );

		$details = array(
			'name'       => $email,
			'code'       => $code,
			'max'        => 1,
			'amount'     => '15',
			'start'      => '-1 day',
			'expiration' => '+10 hours',
			'type'       => 'flat',
			'use_once'   => true,
			'excluded-products' => array( 121068, 138387 )
		);
		$discount_id = edd_store_discount( $details );

		return $code;

	}

	public function send_discount( $email = '', $name = '', $code ) {

		$subject  = sprintf( __( 'Discount code for %s', 'edd-om-discounts' ), get_bloginfo( 'name' ) );
		$message  = '';
		if( ! empty( $name ) ) {
			$messabe .= sprintf( __( 'Hello %s!', 'edd-om-discounts' ), $name );
		} else {
			$messabe .= __( 'Hello !', 'edd-om-discounts' );
		}
		$message .= sprintf( __( 'Thank you submitting your email address! As a small thank you, here is a discount code for $15 off your purchase at %s', 'edd-om-discounts' ), home_url() );
		$message .= "\n\n" . sprintf( __( 'Discount code: %s', 'edd-om-discounts' ), $code );

		$emails = new EDD_EMails;
		$emails->send( $email, $subject, $message );

	}

}

function edd_om_discount_on_submission_load() {
	new EDD_OM_Discount_For_Submission();
}
add_action( 'plugins_loaded', 'edd_om_discount_on_submission_load' );