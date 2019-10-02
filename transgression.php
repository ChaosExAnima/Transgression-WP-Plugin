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

define( 'TRANSGRESSION_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRANSGRESSION_TEMPLATES', TRANSGRESSION_PATH . '/templates' );

add_action( 'init', function() {
	$core = new Transgression\Core();
	$core->setup();
} );
