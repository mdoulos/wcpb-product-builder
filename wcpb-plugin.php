<?php
/**
* Plugin Name: Product Builder for WooCommerce
* Plugin URI: https://www.mdoulos.com/
* Description: A code repository for the products on the site that require advanced options.
* Version: 1.0
* Author: MDoulos
* Author URI: http://www.mdoulos.com/
**/

/** **/

if ( ! defined ('ABSPATH') ) {
    return;
}

require_once dirname( __FILE__ ) . '/product-builder/wc-product-builder.php';

add_filter( 'woocommerce_locate_template', 'intercept_wc_template', 10, 3 );
/**
 * Filter the cart template path to use cart.php in this plugin instead of the one in WooCommerce.
 *
 * @param string $template      Default template file path.
 * @param string $template_name Template file slug.
 * @param string $template_path Template file name.
 *
 * @return string The new Template file path.
 */
function intercept_wc_template( $template, $template_name, $template_path ) {

	$template_directory = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/';
	$path = $template_directory . $template_name;

	return file_exists( $path ) ? $path : $template;

}

add_action( 'admin_enqueue_scripts', 'enqueue_wcpb_custom_admin_styles' );
function enqueue_wcpb_custom_admin_styles() {
    wp_enqueue_style( 'wcpb-admin', plugin_dir_url( __FILE__ ) . 'css/wcpb-admin-styles.css' );
}

add_action( 'wp_enqueue_scripts', 'enqueue_wcpb_custom_styles' );
function enqueue_wcpb_custom_styles() {
	wp_enqueue_style( 'wcpb-styles', plugin_dir_url( __FILE__ ) . 'css/wcpb-styles.css' );
}