<?php
/**
 * Plugin Name: Transgression
 * Plugin URI: https://transgression.party
 * Description: Customizations for Transgression Events
 * Version: 1.0.0
 * Author: Echo <ChaosExAnima@users.noreply.github.com >
 * Author URI: https://echonyc.name
 * License: MIT
 * Text Domain: transgression
 *
 * @package Transgression
 */

namespace Transgression;

define( 'TRANSGRESSION_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRANSGRESSION_TEMPLATES', TRANSGRESSION_PATH . '/templates' );

class Core {
	/**
	 * Sets up filter hooks.
	 *
	 * @return void
	 */
	public function setup() {
		// WP Passwordless plugin.
		add_filter( 'wpa_email_subject', [ $this, 'filter_wpa_login_subject' ] );
		add_filter( 'wpa_email_message', [ $this, 'filter_wpa_login_message' ], 10, 2 );
		add_filter( 'wpa_change_form_label', [ $this, 'filter_wpa_login_label' ] );

		// WooCommerce plugin.
		add_filter( 'wc_get_template', [ $this, 'filter_woo_template' ], 10, 2 );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'filter_woo_account_menu' ] );
		add_filter( 'woocommerce_save_account_details_required_fields', [ $this, 'filter_woo_account_required' ] );
		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'filter_woo_cart_validate' ], 10, 2 );

		// Core WP.
		add_filter( 'insert_user_meta', [ $this, 'filter_wp_save_pronouns' ] );
	}

	/**
	 * Filters the password login email subject.
	 *
	 * @return string
	 */
	public function filter_wpa_login_subject() : string {
		return __( 'Transgression: Log in here', 'transgression' );
	}

	/**
	 * Filters the password login email body.
	 *
	 * @param string $old_msg    Old message.
	 * @param string $unique_url The unique login URL.
	 * @return string
	 */
	public function filter_wpa_login_message( string $old_msg, string $unique_url ) : string {
		return sprintf(
			'Login to your account by <a href="%1$s" target="_blank">clicking here</a> or going to the URL below:<br><br>%1$s',
			$unique_url
		);
	}

	/**
	 * Filters the login label.
	 *
	 * @return string
	 */
	public function filter_wpa_login_label() : string {
		return __( 'Login with your vetted email', 'transgression' );
	}

	/**
	 * Filters the WooCommerce template.
	 *
	 * @param string $template      The template path.
	 * @param string $template_name The template name.
	 * @return string
	 */
	public function filter_woo_template( string $template, string $template_name ) : string {
		if ( 'myaccount/form-login.php' === $template_name ) {
			$template = '';
		} elseif ( 'myaccount/dashboard.php' === $template_name ) {
			$template = TRANSGRESSION_TEMPLATES . '/dashboard.php';
		} elseif ( 'myaccount/form-edit-account.php' === $template_name ) {
			$template = TRANSGRESSION_TEMPLATES . '/edit-account.php';
		} elseif ( 'cart/cart-empty.php' === $template_name ) {
			$template = TRANSGRESSION_TEMPLATES . '/cart-empty.php';
		}

		return $template;
	}

	/**
	 * Changes the name of the WC account menu navigation.
	 *
	 * @param array $menu Array of menu items.
	 * @return array
	 */
	public function filter_woo_account_menu( array $menu ) : array {
		$menu['edit-account'] = __( 'Edit Info', 'transgression' );
		return $menu;
	}

	/**
	 * Removes first and last name as required fields.
	 *
	 * @param array $fields Array of user account fields.
	 * @return array
	 */
	public function filter_woo_account_required( array $fields ) : array {
		unset( $fields['account_first_name'] );
		unset( $fields['account_last_name'] );
		return $fields;
	}

	/**
	 * Adds logic around validating cart options.
	 *
	 * @param boolean $valid      True if adding to cart is allowed.
	 * @param integer $product_id The product ID being added.
	 * @return boolean
	 */
	public function filter_woo_cart_validate( bool $valid, int $product_id ) : bool {
		if ( ! $valid ) {
			return $valid;
		}

		// Ensure the product is not already in the cart.
		$cart_contents = WC()->cart->get_cart_contents();
		foreach ( $cart_contents as $cart_product ) {
			if ( $cart_product['product_id'] === $product_id ) {
				wc_add_notice( 'You already have this event in your cart.', 'error' );
				return false;
			}
		}

		// If the user is logged in, verify whether they've previously bought a ticket.
		if ( is_user_logged_in() ) {
			if ( wc_customer_bought_product( '', get_current_user_id(), $product_id ) ) {
				wc_add_notice( 'You have already purchased a ticket to this event.', 'error' );
				return false;
			}
		}

		return $valid;
	}

	/**
	 * Adds way to save pronouns into user meta.
	 *
	 * @param array $meta Array of meta fields.
	 * @return array
	 */
	public function filter_wp_save_pronouns( array $meta ) : array {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! empty( $_POST['account_pronouns'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$meta['pronouns'] = sanitize_text_field( wp_unslash( $_POST['account_pronouns'] ) );
		} else {
			$meta['pronouns'] = '';
		}
		return $meta;
	}
}

add_action( 'init', function() {
	$core = new Core();
	$core->setup();
} );
