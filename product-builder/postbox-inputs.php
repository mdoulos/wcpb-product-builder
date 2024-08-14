<?php // This file outputs the options visible on the product edit page. 
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }
    
    if (!current_user_can('edit_posts')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

$modify_base = get_post_meta($post->ID, "wcpb-modify-base-price", true);
    $category_count = get_post_meta($post->ID, "wcpb-category-count", true);
    $category_count = intval($category_count);
    if (!$category_count) { $category_count = 1; } // if there are no categories, set the count to 1 so that a blank category is shown.

    ?>
    <div class="wcpb-edit-section wcpb-edit-general">
        <div class="wcpb-edit-field">
            <label for="wcpb-modify-base-price">Modify Base Price (by %)</label>
            <input type="text" id="wcpb-modify-base-price" name="wcpb-modify-base-price" value="<?php echo esc_attr($modify_base); ?>">
        </div>
        <div class="wcpb-edit-info">
            <label>Option Categories: <?php echo $category_count; ?></label>
            <input type="hidden" name="wcpb-category-count" value="<?php echo esc_attr($category_count); ?>" />
        </div>
    </div>
    <div class="wcpb-edit-categoriesheading">
        <label>Categories</label>
    </div>
    <div class="wcpb-edit-container wcpb-edit-section">
        <div class="wcpb-edit-categories">
        <?php
            for ($category = 1; $category <= $category_count; $category++) { 
                $category_name = get_post_meta($post->ID, "wcpb-category-option-$category", true);
                $category_type = get_post_meta($post->ID, "wcpb-category-option-type-$category", true);
                $radio_none_enabled = get_post_meta($post->ID, "wcpb-category-radio-none-$category", true);
                $radio_none_popup = get_post_meta($post->ID, "wcpb-category-radio-none-popup-$category", true);
                $category_option_count = get_post_meta($post->ID, "wcpb-count-option-$category", true);
                $category_option_count = intval($category_option_count);
                if (!$category_option_count) { $category_option_count = 1; } // if there are no options, set the count to 1 so that a blank option is shown.
                $category_disablers = get_post_meta($post->ID, "wcpb-category-disablers-$category", true);
            ?>
                <div class="wcpb-edit-category wcpb-edit-category-<?php echo esc_attr($category); ?>">
                    <div class="wcpb-edit-category-inner">
                        <div class="wcpb-edit-row flex-row">
                            <div class="wcpb-edit-field">
                                <label>Category Name</label>
                                <input type="text" class="cat-input" name="wcpb-category-option-<?php echo esc_attr($category); ?>" value="<?php echo esc_attr($category_name); ?>">
                            </div>
                            <div class="wcpb-edit-field">
                                <label>Category Type</label>
                                <select class="cat-input cat-type-select wcpb-edit-select-type" name="wcpb-category-option-type-<?php echo esc_attr($category); ?>">
                                    <option value="radio" <?php echo ($category_type == "radio") ? 'selected' : ''; ?>>Radio</option>
                                    <option value="checkbox" <?php echo ($category_type == "checkbox") ? 'selected' : ''; ?>>Checkbox</option>
                                    <option value="color" <?php echo ($category_type == "color") ? 'selected' : ''; ?>>Color</option>
                                </select>
                            </div>
                            <div class="wcpb-edit-field wcpb-edit-cat-none" <?php echo ($category_type == "checkbox" || $category_type == "color") ? 'style="display:none;"' : ''; ?> >
                                <label>Include "None"</label>
                                <select class="cat-input" name="wcpb-category-radio-none-<?php echo esc_attr($category); ?>">
                                    <option value="yes" <?php echo ($radio_none_enabled == "yes") ? 'selected' : ''; ?>>Yes</option>
                                    <option value="no" <?php echo ($radio_none_enabled == "no") ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="wcpb-edit-removecategorybutton">
                                <button class="wcpb-edit-remove-btn wcpb-edit-remove-category">Delete Category</button>
                            </div>
                        </div>
                        <div class="wcpb-edit-row flex-row wcpb-edit-cat-popupnone" <?php echo ($category_type == "checkbox" || $category_type == "color" || $radio_none_enabled == "no") ? 'style="display:none;"' : ''; ?>>
                            <div class="wcpb-edit-field">
                                <label>Popup Message If None (Leave Blank to Disable)</label>
                                <input type="text" class="cat-input" name="wcpb-category-radio-none-popup-<?php echo esc_attr($category); ?>" value="<?php echo esc_attr($radio_none_popup); ?>">
                            </div>
                        </div>
                        <div class="wcpb-edit-row flex-row">
                            <div class="wcpb-edit-field">
                                <label>Disable Category if option-#-# Selected</label>
                                <input type="text" class="cat-input" name="wcpb-category-disablers-<?php echo esc_attr($category); ?>" value="<?php echo esc_attr($category_disablers); ?>">
                            </div>
                        </div>
                        <div class="wcpb-edit-options">
                            <div class="wcpb-edit-optionsheading">
                                <label>Options:<span class="wcpb-option-count-label"><?php echo esc_attr($category_option_count); ?></span></label>
                                <input type="hidden" class="cat-input" name="wcpb-count-option-<?php echo esc_attr($category); ?>" value="<?php echo esc_attr($category_option_count); ?>" />
                            </div>
                            <div class="wcpb-edit-options-container">
                            <?php for ($option_count = 1; $option_count <= $category_option_count; $option_count++) {
                                $option_name = get_post_meta($post->ID, "wcpb-name-option-$category-$option_count", true);
                                $option_description = get_post_meta($post->ID, "wcpb-description-option-$category-$option_count", true);
                                $option_image = get_post_meta($post->ID, "wcpb-image-option-$category-$option_count", true);
                                $option_sku = get_post_meta($post->ID, "wcpb-sku-option-$category-$option_count", true);
                                $option_hexcode = get_post_meta($post->ID, "wcpb-hexcode-option-$category-$option_count", true);
                                $option_price = get_post_meta($post->ID, "wcpb-price-option-$category-$option_count", true);
                                ?>

                                <div class="wcpb-edit-option-container">
                                    <div class="wcpb-edit-option">
                                        <div class="wcpb-edit-row flex-row">
                                            <div class="wcpb-edit-field">
                                                <label>Name<span class="wcpb-option-id">option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?></span></label>
                                                <input type="text" class="option-input" name="wcpb-name-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" value="<?php echo esc_attr($option_name); ?>">
                                            </div>
                                        </div>
                                        <div class="wcpb-edit-row wcpb-dual-edit-row flex-row">
                                            <div class="wcpb-edit-field wcpb-edit-opt-hex">
                                                <label>Hexcode</label>
                                                <input type="text" class="option-input" name="wcpb-hexcode-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" value="<?php echo esc_attr($option_hexcode); ?>">
                                            </div>
                                            <div class="wcpb-edit-field wcpb-edit-opt-sku">
                                                <label>Part Number (SKU)</label>
                                                <input type="text" class="option-input" name="wcpb-sku-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" value="<?php echo esc_attr($option_sku); ?>">
                                            </div>
                                            <div class="wcpb-edit-field">
                                                <label>Price</label>
                                                <input type="number" class="option-input" name="wcpb-price-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" value="<?php echo esc_attr($option_price); ?>" step="any">
                                            </div>
                                        </div>
                                        <div class="wcpb-edit-row flex-row">
                                            <div class="wcpb-edit-field wcpb-edit-opt-desc">
                                                <label>Description</label>
                                                <input type="text" class="option-input" name="wcpb-description-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" value="<?php echo esc_attr($option_description); ?>">
                                            </div>
                                        </div>
                                        <div class="wcpb-edit-row wcpb-dual-edit-row flex-row">
                                            <div class="wcpb-edit-field wcpb-edit-opt-img">
                                                <label>Image ID (Should be in Product Gallery)</label>
                                                <input type="text" class="option-input" name="wcpb-image-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" value="<?php echo esc_attr($option_image); ?>">
                                            </div>
                                            <div class="wcpb-edit-removeoptionbutton">
                                                <button id="wcpb-remove-option-<?php echo esc_attr($category); ?>-<?php echo esc_attr($option_count); ?>" class="wcpb-edit-remove-btn wcpb-edit-remove-option">Delete Option</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="wcpb-edit-addoptionbutton">
                                <button class="wcpb-edit-add-btn wcpb-edit-add-option">+ Add Option</button>
                            </div>
                        </div>
                        <span class="wcpb-category-id">category-<?php echo esc_attr($category); ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="wcpb-edit-addcategorybutton">
            <button id="wcpb-add-category" class="wcpb-edit-add-btn wcpb-edit-add-category">+ Add Category</button>

            <script>
                let addCategoryBtn = document.getElementById('wcpb-add-category');
                let categoryContainer = document.querySelector('.wcpb-edit-categories');
                let hiddenCategoriesCountInput = document.getElementsByName("wcpb-category-count")[0];
                let numCategories = parseInt(hiddenCategoriesCountInput.value);
                let optionCategoriesCountLabel = document.querySelector('.wcpb-edit-info label');

                toggleVisibilityOfRemoveButtons();
                toggleVisibilityOfInputs();

                // Click listener to add functionality to the custom buttons.
                document.addEventListener('DOMContentLoaded', function() {
                    document.addEventListener('click', function(e) {

                        if (e.target.classList.contains('wcpb-edit-add-option')) {
                            e.preventDefault();
                            let addOptionBtn = e.target;
                            addOptionClicked(addOptionBtn);

                        } else if (e.target.classList.contains('wcpb-edit-remove-option')) {
                            e.preventDefault();
                            let removeOptionBtn = e.target;
                            removeOptionClicked(removeOptionBtn);

                        } else if (e.target.classList.contains('wcpb-edit-add-category')) {
                            e.preventDefault();
                            addCategoryClicked(addCategoryBtn);

                        } else if (e.target.classList.contains('wcpb-edit-remove-category')) {
                            e.preventDefault();
                            let removeCategoryBtn = e.target;
                            removeCategoryClicked(removeCategoryBtn);
                        }
                    });

                    // Change listener to add functionality to the select elements.
                    document.body.addEventListener('change', function(e) {
                        if (e.target && e.target.matches('.wcpb-edit-select-type')) {
                            toggleVisibilityOfInputs();
                        }
                    });
                });

                // Checks the value of each select on the page and sets the visibility of the inputs accordingly.
                function toggleVisibilityOfInputs() {
                    let selects = document.querySelectorAll('.wcpb-edit-select-type');
                    selects.forEach(function(select) {
                        let selectValue = select.value;
                        let parentCategory = select.closest('.wcpb-edit-category');
                        let options = parentCategory.querySelectorAll('.wcpb-edit-option-container');

                        if (selectValue != "radio") {
                            parentCategory.querySelector('.wcpb-edit-cat-none').style.display = 'none';
                            parentCategory.querySelector('.wcpb-edit-cat-popupnone').style.display = 'none';
                        } else {
                            parentCategory.querySelector('.wcpb-edit-cat-none').style.display = 'flex';
                            parentCategory.querySelector('.wcpb-edit-cat-popupnone').style.display = 'flex';
                        }

                        if (selectValue == "color") {
                            options.forEach(function(option) {
                                option.querySelector('.wcpb-edit-opt-sku').style.display = 'none';
                                option.querySelector('.wcpb-edit-opt-desc').style.display = 'none';
                                option.querySelector('.wcpb-edit-opt-hex').style.display = 'flex';
                            });
                        } else {
                            options.forEach(function(option) {
                                option.querySelector('.wcpb-edit-opt-sku').style.display = 'flex';
                                option.querySelector('.wcpb-edit-opt-desc').style.display = 'flex';
                                option.querySelector('.wcpb-edit-opt-hex').style.display = 'none';
                            });
                        }
                    });

                }



                function addOptionClicked(addOptionBtn) {
                    let parentCategory = addOptionBtn.closest('.wcpb-edit-category');
                    let lastOption = parentCategory.querySelector('.wcpb-edit-option-container:last-of-type');
                    let newOption = lastOption.cloneNode(true);
                    resetInputsToDefaults(newOption);
                    parentCategory.querySelector('.wcpb-edit-options-container').appendChild(newOption);

                    loopCategoriesAndAssignNumbers();
                    toggleVisibilityOfRemoveButtons();
                }

                function removeOptionClicked(removeOptionBtn) {
                    let optionsContainer = removeOptionBtn.closest('.wcpb-edit-options-container');
                    let option = removeOptionBtn.closest('.wcpb-edit-option-container');
                    optionsContainer.removeChild(option);

                    loopCategoriesAndAssignNumbers();
                    toggleVisibilityOfRemoveButtons();
                }

                function addCategoryClicked(addCategoryBtn) {
                    let lastCategory = document.querySelector('.wcpb-edit-category:last-of-type');
                    let newCategory = lastCategory.cloneNode(true);
                    resetCategoryInputsToDefaults(newCategory);
                    resetAllOptionsInCategory(newCategory);
                    categoryContainer.appendChild(newCategory);
                    newCategory.classList.remove('wcpb-edit-category-' + numCategories);
                    newCategory.classList.add('wcpb-edit-category-' + (numCategories+1));

                    // Remove all options in new category except for first one.
                    let options = newCategory.querySelectorAll('.wcpb-edit-option-container');
                    options.forEach(function(option, index) {
                        if (index > 0) {
                            option.remove();
                        }
                    });

                    let select = newCategory.querySelector('.wcpb-edit-select-type');
                    select.value = 'radio';
                    toggleVisibilityOfInputs();

                    numCategories++;
                    hiddenCategoriesCountInput.value = parseInt(numCategories);
                    optionCategoriesCountLabel.innerHTML = 'Option Categories: ' + numCategories;

                    loopCategoriesAndAssignNumbers();
                    toggleVisibilityOfRemoveButtons();
                }

                function removeCategoryClicked(removeCategoryBtn) {
                    let category = removeCategoryBtn.closest('.wcpb-edit-category');
                    categoryContainer.removeChild(category);

                    loopCategoriesAndAssignNumbers();
                    toggleVisibilityOfRemoveButtons();

                    hiddenCategoriesCountInput.value = parseInt(numCategories);
                    optionCategoriesCountLabel.innerHTML = 'Option Categories: ' + numCategories;
                }

                function loopCategoriesAndAssignNumbers() {
                    let categories = document.querySelectorAll('.wcpb-edit-category');
                    let number = 0;
                    categories.forEach(function(category) {
                        number++;

                        // Removes and reassigns the category number to the current place in the new list.
                        category.classList.forEach(function(className) {
                            if (className.startsWith('wcpb-edit-category-')) {
                                category.classList.remove(className);
                            }
                        });
                        category.classList.add('wcpb-edit-category-' + number);

                        assignCategoryNumberToCategoryInputs(category, number);
                        assignCorrectNumbersToOptionsInCategory(category, number);
                    });

                    numCategories = number;
                }

                function assignCategoryNumberToCategoryInputs(categoryElement, catNum) {
                    let categoryInputs = categoryElement.querySelectorAll('.cat-input');
                    categoryInputs.forEach(function(item) {
                        var oldName = item.getAttribute('name');
                        item.setAttribute('name', oldName.replace(/\-\d+$/, '-' + catNum));

                        let categoryID = categoryElement.querySelector('.wcpb-category-id');
                        categoryID.innerHTML = 'category-' + catNum;
                    });
                }

                function assignCorrectNumbersToOptionsInCategory(categoryElement, catNum) {
                    let options = categoryElement.querySelectorAll('.wcpb-edit-option-container');
                    let optionNumber = 0;
                    options.forEach(function(option) {
                        optionNumber++;
                        let optionInputs = option.querySelectorAll('.option-input');
                        optionInputs.forEach(function(item) {
                            var oldName = item.getAttribute('name');
                            item.setAttribute('name', oldName.replace(/(^.*?-)\d+(-\d+)/, '$1' + catNum + '-' + optionNumber));
                        });

                        let optionID = option.querySelector('.wcpb-option-id');
                        optionID.innerHTML = 'option-' + catNum + '-' + optionNumber;
                    });

                    let optionCountLabel = categoryElement.querySelector('.wcpb-option-count-label');
                    optionCountLabel.innerHTML = optionNumber;
                    let hiddenOptionCountInput = categoryElement.querySelector('input[name^="wcpb-count-option"]');
                    hiddenOptionCountInput.value = optionNumber;
                }

                function resetInputsToDefaults(option) {
                    let inputs = option.querySelectorAll('input');
                    inputs.forEach(function(input) {
                        input.value = '';
                    });
                }

                function resetCategoryInputsToDefaults(category) {
                    let inputs = category.querySelectorAll('.cat-input');
                    inputs.forEach(function(input) {
                        input.value = '';
                    });
                }

                function resetAllOptionsInCategory(category) {
                    let options = category.querySelectorAll('.wcpb-edit-option-container');
                    options.forEach(function(option) {
                        resetInputsToDefaults(option);
                    });
                }

                function toggleVisibilityOfRemoveButtons() {
                    let categories = document.querySelectorAll('.wcpb-edit-category');
                    categories.forEach(function(category) { 
                        if (category.querySelector('.wcpb-category-id').innerHTML.endsWith('-1')) {
                            let removeCategoryBtn = category.querySelector('.wcpb-edit-remove-category');
                            removeCategoryBtn.style.display = 'none';
                        } else {
                            let removeCategoryBtn = category.querySelector('.wcpb-edit-remove-category');
                            removeCategoryBtn.style.display = 'block';
                        }
                    });

                    let options = document.querySelectorAll('.wcpb-edit-option-container');
                    options.forEach(function(option) {
                        // if wcpb-option-id ends with -1, hide the remove button.
                        if (option.querySelector('.wcpb-option-id').innerHTML.endsWith('-1')) {
                            let removeOptionBtn = option.querySelector('.wcpb-edit-remove-option');
                            removeOptionBtn.style.display = 'none';
                        } else {
                            let removeOptionBtn = option.querySelector('.wcpb-edit-remove-option');
                            removeOptionBtn.style.display = 'block';
                        }
                    });
                }

            </script>
        </div>
    </div> 
    <?php