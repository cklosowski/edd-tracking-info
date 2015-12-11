<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function edd_ti_get_payment_tracking_id( $payment_id = 0 ) {
	$tracking_id = edd_get_payment_meta( $payment_id, '_edd_payment_tracking_id', true );

	return $tracking_id;
}

function edd_ti_get_payment_tracking_link( $payment_id = 0 ) {
	$tracking_id = edd_ti_get_payment_tracking_id( $payment_id );
	$link        = false;

	if ( ! empty( $tracking_id ) ) {
		$link = 'https://track.aftership.com/' . $tracking_id;
	}

	return $link;
}

function edd_ti_order_details_header() {
	?>
	<th class="edd_purchase_tracking"><?php _e( 'Tracking', 'edd-tracking-info' ); ?></th>
	<?
}
add_action( 'edd_purchase_history_header_after', 'edd_ti_order_details_header', 10, 1 );

function edd_ti_order_details_row( $payment_id, $purchase_data ) {
	$link = edd_ti_get_payment_tracking_link( $payment_id );
	?>
	<td>
		<?php if ( $link ) : ?>
			<a href="<?php echo $link; ?>" target="_blank"><?php echo edd_ti_get_payment_tracking_id( $payment_id ); ?></a>
		<?php else : ?>
			<em><?php _e( 'No Tracking', 'edd-tracking-info' ); ?></em>
		<?php endif; ?>
	</td>
	<?php
}
add_action( 'edd_purchase_history_row_end', 'edd_ti_order_details_row', 10, 2 );
