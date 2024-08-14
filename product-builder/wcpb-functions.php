<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

add_action( 'add_meta_boxes', 'custom_builder_postboxes' );
function custom_builder_postboxes() {
    global $post;
    if ( !is_object( $post ) ) {
        return;
    }
    $product = wc_get_product( $post->ID );
    if ( !$product || !is_a( $product, 'WC_Product' ) ) {
        return;
    }
    
    if ('builder' === $product->get_type()) {
        add_meta_box(
            'postbox_for_wcpb',
            'Product Builder Options',
            'render_postbox_inputs_for_wcpb',
            'product',
            'advanced',
            'high'
        );
    }
}

// Code for the Edit Boxes on the Product Edit Page.
function render_postbox_inputs_for_wcpb( $post ) {
    require_once('postbox-inputs.php'); // Outputs the inputs on the product edit page which in turn defines what shows up on the front end.
}

add_action('save_post', 'save_custom_builder_postboxes');
function save_custom_builder_postboxes($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if(get_post_type($post_id) !== 'product'){
        return;
    }
    $product = wc_get_product($post_id);
    if(!$product || $product->get_type() !== 'builder') {
        return;
    }

    $category_count = filter_input(INPUT_POST, 'wcpb-category-count', FILTER_SANITIZE_NUMBER_INT);
    $category_count = intval($category_count);
    update_post_meta($post_id, 'wcpb-category-count', $category_count);

    $modify_base = filter_input(INPUT_POST, 'wcpb-modify-base-price', FILTER_VALIDATE_FLOAT);
    update_post_meta($post_id, "wcpb-modify-base-price", $modify_base);

    for ($category = 1; $category <= $category_count; $category++) {
        $category_name = sanitize_text_field($_POST["wcpb-category-option-$category"]);
        update_post_meta($post_id, "wcpb-category-option-$category", $category_name);
        $category_type = sanitize_text_field($_POST["wcpb-category-option-type-$category"]);
        update_post_meta($post_id, "wcpb-category-option-type-$category", $category_type);
        $radio_none_enabled = sanitize_text_field($_POST["wcpb-category-radio-none-$category"]);
        update_post_meta($post_id, "wcpb-category-radio-none-$category", $radio_none_enabled);
        $radio_none_popup = sanitize_text_field($_POST["wcpb-category-radio-none-popup-$category"]);
        update_post_meta($post_id, "wcpb-category-radio-none-popup-$category", $radio_none_popup);

        $category_option_count = filter_input(INPUT_POST, "wcpb-count-option-$category", FILTER_SANITIZE_NUMBER_INT);
        $category_option_count = intval($category_option_count);
        update_post_meta($post_id, "wcpb-count-option-$category", $category_option_count);

        $category_disablers = sanitize_text_field($_POST["wcpb-category-disablers-$category"]);
        update_post_meta($post_id, "wcpb-category-disablers-$category", $category_disablers);

        for ($option_count = 1; $option_count <= $category_option_count; $option_count++) {
            $option_name = htmlspecialchars($_POST["wcpb-name-option-$category-$option_count"]);
            update_post_meta($post_id, "wcpb-name-option-$category-$option_count", $option_name);
            $option_description = sanitize_text_field($_POST["wcpb-description-option-$category-$option_count"]);
            update_post_meta($post_id, "wcpb-description-option-$category-$option_count", $option_description);
            $option_image = sanitize_text_field($_POST["wcpb-image-option-$category-$option_count"]);
            update_post_meta($post_id, "wcpb-image-option-$category-$option_count", $option_image);
            $option_sku = sanitize_text_field($_POST["wcpb-sku-option-$category-$option_count"]);
            update_post_meta($post_id, "wcpb-sku-option-$category-$option_count", $option_sku);
            $option_hexcode = sanitize_text_field($_POST["wcpb-hexcode-option-$category-$option_count"]);
            update_post_meta($post_id, "wcpb-hexcode-option-$category-$option_count", $option_hexcode);
            $option_price = sanitize_text_field($_POST["wcpb-price-option-$category-$option_count"]);
            update_post_meta($post_id, "wcpb-price-option-$category-$option_count", $option_price);
        }
    }
}

add_filter( 'woocommerce_add_cart_item_data', 'wcpb_item_data', 10, 3 );
function wcpb_item_data( $cart_item_data, $product_id, $variation_id ) {
    $product = wc_get_product( $product_id );

	if( $product->is_type( 'builder' ) ) {
        $builder_option_count = get_post_meta($product_id, "builder-option-count", true); /** Saved on the builder product page. */
        $cart_item_data['builder_option_count'] = $builder_option_count;
        $modify_base = get_post_meta($product_id, "wcpb-modify-base-price", true);
        $basePrice = $product->get_price();
		$price_adjustment = 0;

        $percent = $modify_base / 100;
        $modify_base = $basePrice * $percent;
          
        $modify_base = (int) $modify_base;
        $price_adjustment += $modify_base;

		for ($i = 1; $i <= $builder_option_count; $i++) {
            if (isset($_POST["builder-option-$i"])) { /** If the option was selected. */
                $chosenOption = $_POST["builder-option-$i"]; /** Grabs the value of each builder option, will return the option handle such as option-3-2 for option 2 in category 3. For radio buttons, only the selected option's value will return. For checkboxes, if the checkbox is checked, the value will return, otherwise it will be blank. */
                if ($chosenOption != "none-option") {
                    $numbers = explode("-", $chosenOption);
                    $optionCategory = $numbers[1];

                    $option_name = get_post_meta($product_id, "wcpb-name-$chosenOption", true);
                    $option_price = get_post_meta($product_id, "wcpb-price-$chosenOption", true);
                    $category_name = get_post_meta($product_id, "wcpb-category-option-$optionCategory", true);

                    $price_adjustment += $option_price;

                    $cart_item_data['selected_option_name_' . $i] = $option_name;
                    $cart_item_data['selected_option_category_name_' . $i] = $category_name;
                }
            }
		}

        $cart_item_data['new_price'] = $product->get_price() + $price_adjustment;
	}
    

	return $cart_item_data;
}

add_filter( 'woocommerce_get_item_data', 'display_builder_options_dc200', 10, 2 );
function display_builder_options_dc200( $item_data, $cart_item ) {
    $item_id = $cart_item['product_id'];
    $builder_option_count = $cart_item['builder_option_count'];

    for ($i = 1; $i <= $builder_option_count; $i++) {
        if ( isset( $cart_item['selected_option_name_' . $i] ) ) {
            $key = $cart_item['selected_option_category_name_' . $i];
            $value = $cart_item['selected_option_name_' . $i];

            //** Adds the item data to the cart and checkout. */
            $item_data[] = array(
                'key'     => $key,
                'value'   => $value,
            );
        }
    }
    
    return $item_data;
}

add_action( 'woocommerce_add_order_item_meta', 'add_custom_item_meta_data_to_order', 10, 3 );
function add_custom_item_meta_data_to_order( $item_id, $values, $cart_item_key ) {
    $builder_option_count = $values['builder_option_count'];
    
    for ($i = 1; $i <= $builder_option_count; $i++) {
        if ( isset( $values['selected_option_name_' . $i] ) ) {
            $key = $values['selected_option_category_name_' . $i];
            $value = $values['selected_option_name_' . $i];

            //** Adds the item data to the thank you page, order page, and emails. */
            wc_add_order_item_meta( $item_id, $key, $value );
        }
    }
}

add_action( 'woocommerce_before_calculate_totals', 'wcpb_calculate_totals', 10, 1 );
function wcpb_calculate_totals( $cart_obj ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	// Iterate through each cart item
	foreach( $cart_obj->get_cart() as $key=>$value ) {
		if( isset( $value['new_price'] ) ) {
			$price = $value['new_price'];
			$value['data']->set_price( ( $price ) );
		}
	}
}

add_filter( 'woocommerce_cart_item_subtotal', 'wcpb_cart_item_subtotal', 10, 3 );
function wcpb_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
    // Check if the cart item has new_price data
    if( isset( $cart_item['new_price'] ) ) {
        $subtotal = wc_price( $cart_item['new_price'] * $cart_item['quantity'] );
    }
    return $subtotal;
}