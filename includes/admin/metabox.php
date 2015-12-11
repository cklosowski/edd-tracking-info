<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function edd_ti_metabox( $payment_id ) {
	$tracking_id = edd_ti_get_payment_tracking_id( $payment_id );
	$was_sent    = edd_get_payment_meta( $payment_id, 'edd_tracking_info_sent', true );
	?>
	<div id="edd-payment-tracking" class="postbox">
		<h3 class="hndle"><span><?php _e( 'Tracking Info', 'edd-tracking-info' ); ?></span></h3>
		<div class="inside">
			<strong class="order-data-tracking-id"><?php _e( 'Tracking ID:', 'edd-tracking-info' ); ?></strong><br/>
			<input type="text" name="edd_payment_tracking_id" value="<?php echo $tracking_id; ?>" class="regular-text" />
			<?php if ( ! empty( $tracking_id ) ) : ?>
			<?php wp_nonce_field( 'edd-ti-send-tracking', 'edd-ti-send-tracking', false, true ); ?>
			<?php $notify_button_text = empty( $was_sent ) ? __( 'Send Tracking Info', 'edd-tracking-info' ) : __( 'Resend Tracking Info', 'edd-tracking-info' ); ?>
			<span class="button-secondary" id="edd-tracking-info-notify-customer" data-payment="<?php echo $payment_id; ?>"><?php echo $notify_button_text; ?></span>
			<span class="edd-tracking-info-email-message"></span>
			<span class="spinner"></span>
			<p>
				<?php _e( 'Track shipment', 'edd-tracking-info' ); ?>:&nbsp;<a href="<?php echo edd_ti_get_payment_tracking_link( $payment_id ); ?>" target="_blank"><?php echo $tracking_id; ?></a>
			</p>
			<?php endif; ?>
			<div class="clear"></div>
		</div><!-- /.inside -->
	</div><!-- /#edd-payment-notes -->
	<?php
}
add_action( 'edd_view_order_details_billing_after', 'edd_ti_metabox' );

function edd_ti_save_edited_payment( $payment_id ) {
	$tracking_id = ! empty( $_POST['edd_payment_tracking_id'] ) ? sanitize_text_field( $_POST['edd_payment_tracking_id'] ) : false;

	if ( false === $tracking_id ) {
		delete_post_meta( $payment_id, '_edd_payment_tracking_id' );
	} else {
		edd_update_payment_meta( $payment_id, '_edd_payment_tracking_id', $tracking_id );
	}
}
add_action( 'edd_updated_edited_purchase', 'edd_ti_save_edited_payment', 10, 1 );

function edd_ti_send_tracking( $post ) {
	$nonce = ! empty( $post['nonce'] ) ? $post['nonce'] : false;
	if ( ! wp_verify_nonce( $nonce, 'edd-ti-send-tracking' ) ) { wp_die(); }

	$tracking_id = edd_ti_get_payment_tracking_id( $post['payment_id'] );
	if ( empty( $tracking_id ) ) {
		return;
	}

	$from_name    = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$from_email   = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
	$to_email     = edd_get_payment_user_email( $post['payment_id'] );

	$subject      = 'Your order has shipped';
	$heading      = 'Your order has shipped!';

	$message  = '<p>Your recent order ' . $post['payment_id']. ' has been shipped.</p>';
	$message .= '<p>Tracking ID: <a href="' . edd_ti_get_payment_tracking_link( $post['payment_id'] ) . '">' . $tracking_id . '</a></p>';

	$message .= '<p>Thank you!</p>';
	$message .= '<p>The ' . $from_name . ' team</p>';

	$headers  = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
	$headers .= "Reply-To: ". $from_email . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	$attachments = array();

	$emails = EDD()->emails;

	$emails->__set( 'from_name', $from_name );
	$emails->__set( 'from_email', $from_email );
	$emails->__set( 'heading', $heading );
	$emails->__set( 'headers', $headers );

	$result = $emails->send( $to_email, $subject, $message, $attachments );

	$response = array( 'success' => $result );
	$response['message'] = $result ? __( 'Email sent.', 'edd-tracking-info' ) : __( 'Error sending email. Try again later.', 'edd-tracking-info' );

	if ( $result ) {
		edd_update_payment_meta( $post['payment_id'], 'edd_tracking_info_sent', true );
		edd_insert_payment_note( $post['payment_id'], sprintf( __( 'Tracking information sent to %s.', 'edd-tracking-info' ), $to_email ) );
	}

	echo json_encode( $response );
	die();
}
add_action( 'edd_send-tracking', 'edd_ti_send_tracking', 10, 1 );
