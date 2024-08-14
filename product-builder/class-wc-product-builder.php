<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Product Builder Product Type
 */
class WC_Product_Builder extends WC_Product_Simple {

    public function get_type() {
        return 'builder';
    }
    
}