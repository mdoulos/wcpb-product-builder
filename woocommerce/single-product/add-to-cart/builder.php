<?php
/**
 * Builder product add to cart
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

$product_id = $product->get_id();

$base_price = $product->get_price();
$modify_base = get_post_meta($product_id, "wcpb-modify-base-price", true);
$category_count = get_post_meta($product_id, "wcpb-category-count", true);
$category_count = intval($category_count);
$builder_option = 0; 
?>

<p class="wcpb-starting-price">
	<span>Starting Price: </span><span class="wcpb-starting-price-label"><?php echo '$' . number_format($base_price, 2); ?></span>
</p>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart builder-product-page-form flex-column" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
<?php

for ($category = 1; $category <= $category_count; $category++) { 
	$category_name = get_post_meta($product_id, "wcpb-category-option-$category", true);
	if(preg_match("/^[aeiouAEIOU]/", $category_name)) { $prefix = "an "; } else { $prefix = "a "; }
	$category_type = get_post_meta($product_id, "wcpb-category-option-type-$category", true);
	$radio_none_enabled = get_post_meta($product_id, "wcpb-category-radio-none-$category", true);
	$radio_none_popup = get_post_meta($product_id, "wcpb-category-radio-none-popup-$category", true);
	$category_disablers = get_post_meta($product_id, "wcpb-category-disablers-$category", true);
	$category_option_count = get_post_meta($product_id, "wcpb-count-option-$category", true);
	$category_option_count = intval($category_option_count);
	?>

	<fieldset class="builder-category <?php if (!empty($category_disablers)) { echo 'disable-if-' . $category_disablers; } ?> wcpb-category-<?php echo esc_attr($category_type); ?>">
		<legend>Choose <?php echo esc_attr($prefix . $category_name); ?>:<?php if ($category_type == "radio") { echo '<abbr class="required" title="required">*</abbr>'; } ?></legend>
		<div class="flex-column">
		<?php
		if ($category_option_count > 0) {
			if ($category_type == "radio") { $builder_option++; /** Only adds 1 to the option tally per category since they are exclusive. */
				if ($radio_none_enabled == "yes") { ?>
					<div class="builder-option">
						<input type="radio" id="none-option-<?php echo esc_attr($category); ?>" class="<?php if ($radio_none_popup != "") { echo 'delays-add-to-cart'; }?>" name="builder-option-<?php echo esc_attr($builder_option); ?>" value="none-option" data-category="<?php echo esc_attr($category); ?>">
						<label for="none-option-<?php echo esc_attr($category); ?>" onclick="wcpbRadioClicked(event)">
							<div class="builder-option-critical-info">
								<span class="builder-option-text">None</span>
								<span class="builder-option-price" data-price="0"></span>
							</div>
						</label>
					</div>

					<?php
					if ($radio_none_popup != "") { /** Popup message, if it exists. */ ?>
						<div class="radio-none-popup hidden" id="radio-none-popup-<?php echo esc_attr($category); ?>">
							<div class="radio-popup-inner">
								<span class="radio-none-popup-message"><?php echo esc_attr($radio_none_popup); ?></span>
								<div class="radio-popup-btns flex-row">
									<div class="radio-none-btn radio-none-btn-no">Go Back</div>
									<div class="radio-none-btn radio-none-btn-yes">Add to Cart</div>
								</div>
							</div>
						</div>
					<?php }
				 }
			}

        for ($option_count = 1; $option_count <= $category_option_count; $option_count++) {
			$option_name = get_post_meta($product_id, "wcpb-name-option-$category-$option_count", true);
			$option_description = get_post_meta($product_id, "wcpb-description-option-$category-$option_count", true);
			$option_image = get_post_meta($product_id, "wcpb-image-option-$category-$option_count", true);
			$option_sku = get_post_meta($product_id, "wcpb-sku-option-$category-$option_count", true);
			$option_hexcode = get_post_meta($product_id, "wcpb-hexcode-option-$category-$option_count", true);
			$option_price = get_post_meta($product_id, "wcpb-price-option-$category-$option_count", true);

			/** Adds 1 to the option tally per option since each checkbox is selectable. */
			if ($category_type == "checkbox") { $builder_option++; }

			if ($category_type == "color") { ?>
				<div class="builder-option builder-option-color">
					<input type="radio" id="option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" <?php if ($option_image != "") { echo 'data-image-id="'.$option_image.'"'; }?> name="builder-option-<?php echo esc_attr($builder_option); ?>" value="option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" data-category="<?php echo esc_attr($category); ?>">
					<label for="option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" onclick="wcpbRadioClicked(event)";>
					</label>
				</div>
			<?php } else { ?>
				<div class="builder-option">
					<input type="<?php echo esc_attr($category_type); ?>" id="option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" <?php if ($option_image != "") { echo 'data-image-id="'.$option_image.'"'; }?> name="builder-option-<?php echo esc_attr($builder_option); ?>" value="option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" <?php if ($category_type == "checkbox") { echo 'onclick="wcpbCheckboxClicked(event)"'; } ?> data-category="<?php echo esc_attr($category); ?>">
					<label for="option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" <?php if ($category_type == "radio") { echo 'onclick="wcpbRadioClicked(event)"'; } ?> >
						<div class="builder-option-critical-info">
							<span class="builder-option-text"><?php echo "PN: " . $option_sku . ", " . $option_name; ?></span>
							<span class="builder-option-price" data-price="<?php echo esc_attr($option_price); ?>"><?php if ($category_type == "checkbox") { echo '+$' . esc_attr($option_price); } ?></span>
						</div>
						<?php if ($option_description != "") { ?>
							<div class="builder-option-additional-info">
								<span class="builder-option-description"><?php echo esc_attr($option_description); ?></span>
							</div>
						<?php } ?>
					</label>
				</div>
			<?php } ?>
		<?php }} ?>
		</div>
	</fieldset>


<?php 
}

$builder_option = intval($builder_option);
update_post_meta($product_id, 'builder-option-count', $builder_option);

?>

	<div class="builder-price-totals">
		<p class="wcpb-totals-base-price">
			<span>Base Price: </span><span><?php echo '$' . number_format($base_price, 2); ?></span>
		</p>
		<p class="wcpb-totals-options">
			<span>Options: </span><span class="wcpb-totals-options-price"></span>
		</p>

	</div>

	<div class="builder-add-to-cart flex-row">

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );
		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);
		do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>

		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		<div class="single_add_to_cart_button wcpb-add-to-cart-btn"><span><?php echo esc_html( $product->single_add_to_cart_text() ); ?></span></div>
		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt hidden"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

		<a href="tel:855-851-4700" target="_blank" rel="noreferrer noopener" class="builder-phone-btn">Give Us A Call!</a>

	</div>

	<script>
		let priceTotalsContainer = document.querySelector('.builder-price-totals');
		let optionsPrice = document.querySelector('.wcpb-totals-options');
		let wcPrice = document.querySelector('.price');
		priceTotalsContainer.appendChild(wcPrice);

		const addToCartButton = document.querySelector('button[name="add-to-cart"]');
		const addToCartButtonWCPB = document.querySelector('.wcpb-add-to-cart-btn');
		let totalPriceLabel = wcPrice.querySelector('.woocommerce-Price-amount');
		let optionsPriceLabel = optionsPrice.querySelector('.wcpb-totals-options-price');
		let unmodifiedBasePrice = Number(<?php echo json_encode($base_price); ?>);
		let basePrice = unmodifiedBasePrice;
		let modifyBase = <?php echo json_encode($modify_base); ?>;

		const totalElement = document.createElement('span');
		totalElement.textContent = 'Total: ';
		totalPriceLabel.parentNode.insertBefore(totalElement, totalPriceLabel);
		
		const percent = Number(modifyBase) / 100;
		modifyBase = basePrice * percent;

		modifyBase = Number(modifyBase);
		basePrice += modifyBase;

		const optionCategories = document.querySelectorAll(".builder-category");
		const formatter = new Intl.NumberFormat('en-US', {
			style: 'currency',
			currency: 'USD',
			minimumFractionDigits: 2,
		});

		function updateTotalPrice() {
			var totalPrice = basePrice;
			var optionsPrice = 0;

			for (var a = 0; a < optionCategories.length; a++) {
				var inputElements = optionCategories[a].getElementsByTagName("input");
				var checkedInputs = [];
				for (var i = 0; i < inputElements.length; i++) {
					if (inputElements[i].checked) {
						optionPrice = Number(inputElements[i].parentNode.querySelector(".builder-option-price").getAttribute("data-price"));
						totalPrice += optionPrice;
					}
				}
			}

			optionsPrice = totalPrice - unmodifiedBasePrice;

			optionsPriceLabel.textContent = formatter.format(optionsPrice);
			totalPriceLabel.textContent = formatter.format(totalPrice);
		}

		const fieldSetsRadio = document.querySelectorAll('.wcpb-category-radio');
		fieldSetsRadio.forEach(fieldSet => {
			const firstOptionInput = fieldSet.querySelector('.builder-option:first-child label');
			if (firstOptionInput) {
				firstOptionInput.click();
			}
		});

		function clickThumbnailForImageID(input) {
			if (input && input.dataset && input.dataset.imageId) {
				var imageID = input.dataset.imageId;
				var thumbnail = document.querySelector(`.thumbnail-id-${imageID}`);
				if (thumbnail) {
					thumbnail.click();
				}
			}
		}

		function wcpbRadioClicked(e) {
			var targetedInput = document.getElementById(e.currentTarget.getAttribute("for"));

			clickCorrespondingInput(e, targetedInput);
			radioDisableWCPBElements(targetedInput);
			clickThumbnailForImageID(targetedInput);
			removeNoneClassFromAddToCart(targetedInput);
			addNoneClassToAddToCart(targetedInput);
		}
		
		function clickCorrespondingInput(e, targetedInput) {
			targetedInput.click();

			var priceLabels = e.currentTarget.closest("fieldset").getElementsByClassName("builder-option-price");
			updatePriceLabels(priceLabels, e.currentTarget.querySelector('.builder-option-price'));
		}

		function updatePriceLabels(priceLabels, selectedLabel) {
			var currentPrice = parseInt(selectedLabel.getAttribute("data-price"));

			Array.from(priceLabels).forEach(function(priceLabel) {
				let thisPrice = parseInt(priceLabel.getAttribute("data-price"));

				if (thisPrice >= currentPrice) {
					priceLabel.innerHTML = "+" + formatter.format(Math.abs(currentPrice - thisPrice));
				} else {
					priceLabel.innerHTML = "-" + formatter.format(Math.abs(currentPrice - thisPrice));
				}
				priceLabel.style.display = "block";
			});

			selectedLabel.style.display = "none";

			updateTotalPrice();
		}

		function wcpbCheckboxClicked(e) {
			togglePriceLabel(e);
			clickThumbnailForImageID(e);
		}

		function togglePriceLabel(e) {
			var associatedPriceLabel = document.querySelector(`label[for=${e.currentTarget.id}]`).querySelector('.builder-option-price');
			if (e.currentTarget.checked) {
				associatedPriceLabel.style.display = "none";
			} else {
				associatedPriceLabel.style.display = "block";
			}

			updateTotalPrice();
		}

		function radioDisableWCPBElements(selectedOption) {
			let parentFieldset = selectedOption.closest('fieldset');

			let allOptions = parentFieldset.querySelectorAll('input[type="radio"]');
			for (let sibling of allOptions) {
				if (sibling != selectedOption) {
					let fieldsets = document.querySelectorAll(`.disable-if-${sibling.value}`);
					for (let fieldset of fieldsets) {
						fieldset.classList.remove("hidden");
					}
				}
			}

			let fieldsets = document.querySelectorAll(`.disable-if-${selectedOption.value}`);
			for (let fieldset of fieldsets) {
				fieldset.classList.add("hidden");
				let inputs = fieldset.querySelectorAll('input');
				for (let input of inputs) {
					if (input.checked) {
						input.click();
					}
				}
			}
		}

		function removeNoneClassFromAddToCart(selectedOption) {
			if (selectedOption.value != "none-option") {
				let category = selectedOption.getAttribute("data-category");
				addToCartButtonWCPB.classList.remove(`none-category-${category}`);
			}
		}

		function addNoneClassToAddToCart(selectedOption) {
			let classes = selectedOption.classList;

			if (classes.contains('delays-add-to-cart') && selectedOption.value == "none-option") {
				let category = selectedOption.getAttribute("data-category");
				addToCartButtonWCPB.classList.add(`none-category-${category}`);
			}
		}

		// Move all popups to be a child of the main element.
		const mainTag = document.querySelector('main');
		const radioNonePopups = document.querySelectorAll('.radio-none-popup');

		radioNonePopups.forEach((popup) => {
			mainTag.appendChild(popup);
		});

		// Hide the popup if the Go Back button is clicked.
		const popupNoButtons = document.getElementsByClassName('radio-none-btn-no');
		for (let i = 0; i < popupNoButtons.length; i++) {
			popupNoButtons[i].addEventListener('click', function() {
				const parent = this.closest('.radio-none-popup');
				parent.classList.add('hidden');
			});
		}

		// Add to cart and hide the popup if the Add to Cart button is clicked.
		const popupYesButtons = document.getElementsByClassName('radio-none-btn-yes');
		for (let i = 0; i < popupYesButtons.length; i++) {
			popupYesButtons[i].addEventListener('click', function() {
				const parent = this.closest('.radio-none-popup');
				parent.classList.add('hidden');
				addToCartButton.click();
			});
		}

		// Get all inputs with class "delays-add-to-cart"
		const delaysInputs = document.querySelectorAll('input[class="delays-add-to-cart"]');
		delaysInputs.forEach(input => {
			if (input.checked) {
				let category = input.getAttribute("data-category");
				addToCartButtonWCPB.classList.add(`none-category-${category}`);
			}
		});

		// Prevent normal add to cart and open a popup.
		addToCartButtonWCPB.addEventListener('click', function() {
			const classes = addToCartButtonWCPB.classList;

			for (let i = 0; i < classes.length; i++) {
				const classValue = classes[i];
				if (classValue.startsWith('none-category-')) {
					const categoryNumber = parseInt(classValue.slice(-1));
					const popup = document.querySelector(`#radio-none-popup-${categoryNumber}`);
					popup.classList.remove('hidden');
					return;
				}
			}

			addToCartButton.click();
			
		});

	</script>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' );