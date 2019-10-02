<?php
/**
 * User dashboard template.
 *
 * @package Transgression
 */

?>

<p>
	<?php
	// translators: 1: user display name 2: logout url.
	printf(
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		__( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'transgression' ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
	);
	?>
</p>

<p>
	<?php
	printf(
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		__( 'From your account dashboard you can view your <a href="%1$s">recent orders</a> and manage your <a href="%2$s">account information</a>.', 'transgression' ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	?>
</p>
