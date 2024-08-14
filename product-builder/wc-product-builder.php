<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WC_Product_Type_Plugin {
            
    public function __construct() {
        add_action( 'woocommerce_loaded', array( $this, 'load_plugin') );
        add_filter( 'product_type_selector', array( $this, 'add_type' ) );
        register_activation_hook( __FILE__, array( $this, 'install' ) );
        add_action( 'woocommerce_product_options_general_product_data', function(){
            echo '<div class="options_group show_if_builder clear"></div>';
        } );
        add_action( 'admin_footer', array( $this, 'enable_js_on_wc_product' ) );
    }

    public function enable_js_on_wc_product() {
        global $post, $product_object;

        if ( ! $post ) { return; }

        if ( 'product' != $post->post_type ) :
            return;
        endif;

        $is_builder = $product_object && 'builder' === $product_object->get_type() ? true : false;

        ?>
        <script type='text/javascript'>
            jQuery(document).ready(function() {
                // for Price tab
                jQuery('#general_product_data .pricing').addClass('show_if_builder');

                <?php if ( $is_builder ) { ?>
                    jQuery('#general_product_data .pricing').show();
                <?php } ?>
            });
        </script>
        <?php
    }

    public function install() {
        if ( ! get_term_by( 'slug', 'builder', 'product_type' ) ) {
            wp_insert_term( 'builder', 'product_type' );
        }
    }

    public function add_type( $types ) {
        $types['builder'] = __( 'Product Builder', 'wcbuilder' );

        return $types;
    }

    public function load_plugin() {
        require_once dirname(__FILE__).'/class-wc-product-builder.php';
        require_once dirname(__FILE__).'/wcpb-functions.php';
    }
}

new WC_Product_Type_Plugin();


/** This will be called via woocommerce_template_single_add_to_cart when it does
*   do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
*   adding the function via add_action is required to load on page.
**/

if ( ! function_exists( 'woocommerce_builder_add_to_cart' ) ) {

	/**
	 * Output the builder product add to cart area.
	 */
	function woocommerce_builder_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/builder.php' );
	}

    add_action( 'woocommerce_builder_add_to_cart', 'woocommerce_builder_add_to_cart', 30 );

}