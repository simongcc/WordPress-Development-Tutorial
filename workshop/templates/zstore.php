<?php
/**
 * @package Workshop_Shop_DIY
 * @version 1.0.1
 */
/**
 * ZStore style single product page for WooCommerce.
 */

// Preparation.
// Show/Hide product settings in WooCommerce > Settings > Products Tab > Inventory.

// Follow ZStore's single product page's image size - 600px * 600px.
// This will affect thumbnail generation, so do it before importing any sample products.
add_filter( 'storefront_woocommerce_args', function( $settings ) {
	$settings['single_image_width'] = 600;
	return $settings;
});

// Extend the width of the content area so that the gallery image is approx. 600px * 600px
add_action( 'wp_head', function() {
 ?>
<style>
/* Make the default storefront product page content area displaying in full width */
@media (min-width: 768px) {
	.right-sidebar #primary.content-area {
		width: 100%;
	}

	.single-product #primary div.product .woocommerce-product-gallery {
		width: 49%;
	}

	.single-product #primary div.product .summary {
		width: calc(100% - 55%);
	}

	.col-full {
		max-width: 74%;
	}

	.flex-control-nav {
		/* display: flex;
    	justify-content: space-between; */
	}
}
</style>
 <?php
} );

if( !function_exists('sing_wp_remove_storefront_sidebar') ) {
	/**
	 * Remove storefront sidebar.
	 */
	function sing_wp_remove_storefront_sidebar() {
		if ( is_woocommerce() || is_checkout() ) {
			remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
		}
	}	
	add_action( 'get_header', 'sing_wp_remove_storefront_sidebar' );
}


// Step 1.
// Remove default stock information in default location.
add_filter( 'woocommerce_get_stock_html', '__return_false', 999, 2 );

// Remove on-sale badge.
add_filter( 'woocommerce_sale_flash', '__return_false', 999, 2 );

// Remove product meta - sku.
add_filter( 'wc_product_sku_enabled', '__return_false' );

// Remove all product meta instead of adding template for simple demonstration sake.
add_action( 'woocommerce_product_meta_start', function() {
	ob_start();
} );

add_action( 'woocommerce_product_meta_end', function() {
	// $content = ob_get_contents();

	ob_end_clean();
} );

// Step 2.
// Modify the layout of title and rating.
// Group the title and rating together under a common container.
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', function() {
	echo '<div class="title-container">';
}, 4);

add_action( 'woocommerce_single_product_summary', function(){
	global $product;

	if ( ! wc_review_ratings_enabled() ) {
		return;
	}

	$rating_count = $product->get_rating_count();
	$average      = $product->get_average_rating();

	if ( $rating_count > 0 ) : ?>

		<div class="woocommerce-product-rating">
			<?php echo wc_get_rating_html( $average, $rating_count ); // WPCS: XSS ok. ?>
		</div>

	<?php endif; ?>
	</div><?php
}, 6 );

// Update the layout for the title and rating.
add_action( 'wp_head', function() {
	?>
   <style>
   /* Make the default storefront product page content area displaying in full width */
   @media (min-width: 768px) {
	   #primary .title-container {
		   display: flex;
		   justify-content: space-between;
	   }

	   #primary .title-container .product_title {
		   font-weight: 700;
		   font-size: 28px;
		   line-height: 1.8;
	   }

	   #primary .title-container .woocommerce-product-rating {
		   margin-top: 0;
		   border-left: 1px dotted #d5d5d5;
		   padding: 4px 4px 0 16px;
		   height: 56px;
	   }
   }
   </style>
	<?php
} );

// Step 3.
// Add dimension and weight.
add_action( 'woocommerce_single_product_summary', function(){
	global $product;

	$length = $product->get_length();
	$width = $product->get_width();
	$height = $product->get_height();
	?>
	<div class="physical-info-container">
		<span class="dimension">尺寸：<?php printf( '%scm * %scm * %scm', $length, $width, $height );?></span>
		<span class="weight">重量：<?php echo $product->get_weight(); ?>kg</span>
	</div>
	<?php
	
}, 8 );

// Update the layout for the title and rating.
add_action( 'wp_head', function() {
	?>
   <style>
   /* Make the default storefront product page content area displaying in full width */
   @media (min-width: 768px) {
	   #primary .physical-info-container {
		background: #ebebeb;
		font-size: 14px;
		display: inline-block;
		padding: 2px 6px;
	   }

	   #primary .physical-info-container span:last-of-type {
		margin-left: 1rem;
	   }
   }
   </style>
	<?php
} );

// Step 4.
// Update the price format.
add_filter( 'woocommerce_format_sale_price', function( $price, $regular_price, $sale_price ){
	$price = '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins><del aria-hidden="true">' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>';

	return $price;
}, 10, 3);

// Update the layout for the price.
add_action( 'wp_head', function() {
	?>
   <style>
   /* Make the default storefront product page content area displaying in full width */
   @media (min-width: 768px) {
	   #primary .price ins {
		   font-size: 28px;
		   font-weight: 700;
	   }

	   #primary .price del {
		margin-left: 8px;
	   }
   }
   </style>
	<?php
} );

// Step 5
// Update quantity control.
add_filter( 'wc_get_template', function($template, $template_name, $args, $template_path, $default_path) {
	if( 'global/quantity-input.php' === $template_name ) {
		$product = $GLOBALS['product'];
		if( is_product() && $product->is_type( 'simple' ) ) {
			$args = array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			);
		}

		$defaults = array(
			'input_id'     => uniqid( 'quantity_' ),
			'input_name'   => 'quantity',
			'input_value'  => '1',
			'classes'      => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
			'max_value'    => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
			'min_value'    => apply_filters( 'woocommerce_quantity_input_min', 0, $product ),
			'step'         => apply_filters( 'woocommerce_quantity_input_step', 1, $product ),
			'pattern'      => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
			'inputmode'    => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
			'product_name' => $product ? $product->get_title() : '',
			'placeholder'  => apply_filters( 'woocommerce_quantity_input_placeholder', '', $product ),
			// When autocomplete is enabled in firefox, it will overwrite actual value with what user entered last. So we default to off.
			// See @link https://github.com/woocommerce/woocommerce/issues/30733.
			'autocomplete' => apply_filters( 'woocommerce_quantity_input_autocomplete', 'off', $product ),
		);

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Apply sanity to min/max args - min cannot be lower than 0.
		$args['min_value'] = max( $args['min_value'], 0 );
		$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

		// Max cannot be lower than min if defined.
		if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
			$args['max_value'] = $args['min_value'];
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			if ( isset( $args['action_args'] ) ) {
				wc_doing_it_wrong(
					__FUNCTION__,
					__( 'action_args should not be overwritten when calling wc_get_template.', 'woocommerce' ),
					'3.6.0'
				);
				unset( $args['action_args'] );
			}
			extract( $args ); // @codingStandardsIgnoreLine
		}

		if( ! isset( $classes ) || empty( $classes ) ) {
			$classes[] = 'input-text qty text';
		}
		
		if ( $max_value && $min_value === $max_value ) {
			?>
			<div class="quantity hidden">
				<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty"
					   name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>"/>
			</div>
			<?php
		} else {
			/* translators: %s: Quantity. */
			$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'sing' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'sing' );
			?>
			<div class="quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
				<label class="screen-reader-text"
					   for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $label ); ?></label>
				<!-- <label class="label"
					   for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'sing' ); ?></label> -->
				<div class="qty-box" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
					<span class="decrease icon-minus icon"></span>
					<input
							type="number"
							id="<?php echo esc_attr( $input_id ); ?>"
							class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
							step="<?php echo esc_attr( $step ); ?>"
							min="<?php echo esc_attr( $min_value ); ?>"
							max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
							name="<?php echo esc_attr( $input_name ); ?>"
							value="<?php echo esc_attr( $input_value ); ?>"
							title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'sing' ); ?>"
							size="4"
							placeholder="<?php echo esc_attr( $placeholder ); ?>"
							inputmode="<?php echo esc_attr( $inputmode ); ?>"/>
					<?php do_action( 'woocommerce_after_quantity_input_field' ); ?>
					<span class="increase icon-plus icon"></span>
				</div>
			</div>
			<?php
		}

		return false;
	}
	return $template;
}, 10, 5 );

// Update the layout for the price.
add_action( 'wp_head', function() {
	?>
   <style>
   /* Make the default storefront product page content area displaying in full width */
   /** 
	* SVG icon using base64, since not too many icons will be used. This will save more bandwidth from
	* 1. reducing no of HTTP call to directly load the SVG
	* 2. size of the whole icon font files for each page
	*/
	.icon {
	width: 30px;
	height: 30px; }
	.icon:before {
		content: " ";
		width: 30px;
		height: 30px;
		display: inline-block; }

	.icon-minus:before {
	background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPCEtLSBHZW5lcmF0b3I6IEljb01vb24uaW8gLS0+IDwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+IDxzdmcgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMzIgMzIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIGZpbGw9IiMwMDAwMDAiPjxnPjxwYXRoIGQ9Ik0gOSwxOGwgMTYsMCBDIDI1LjU1MiwxOCwgMjYsMTcuNTUyLCAyNiwxN0MgMjYsMTYuNDQ4LCAyNS41NTIsMTYsIDI1LDE2bC0xNiwwIEMgOC40NDgsMTYsIDgsMTYuNDQ4LCA4LDE3IEMgOCwxNy41NTIsIDguNDQ4LDE4LCA5LDE4eiI+PC9wYXRoPjwvZz48L3N2Zz4="); }

	.icon-plus:before {
	background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPCEtLSBHZW5lcmF0b3I6IEljb01vb24uaW8gLS0+IDwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+IDxzdmcgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMzIgMzIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIGZpbGw9IiMwMDAwMDAiPjxnPjxwYXRoIGQ9Ik0gOSwxOEwgMTYsMTggbDAsNyBDIDE2LDI1LjU1MiwgMTYuNDQ4LDI2LCAxNywyNlMgMTgsMjUuNTUyLCAxOCwyNUwgMTgsMTggbCA3LDAgQyAyNS41NTIsMTgsIDI2LDE3LjU1MiwgMjYsMTcgQyAyNiwxNi40NDgsIDI1LjU1MiwxNiwgMjUsMTZMIDE4LDE2IEwgMTgsOSBDIDE4LDguNDQ4LCAxNy41NTIsOCwgMTcsOFMgMTYsOC40NDgsIDE2LDlMIDE2LDE2IEwgOSwxNiBDIDguNDQ4LDE2LCA4LDE2LjQ0OCwgOCwxN0MgOCwxNy41NTIsIDguNDQ4LDE4LCA5LDE4eiI+PC9wYXRoPjwvZz48L3N2Zz4="); }

	/** Disable default Woocommerce style for the following elements. */
	.woocommerce table.shop_table_responsive tr .product-quantity::before,
	.woocommerce-page table.shop_table_responsive tr .product-quantity::before {
	content: none; }

	.woocommerce.single .entry-summary > form.cart .quantity {
	display: block; }

	.woocommerce.single .quantity input {
	margin-right: 0; 
	box-shadow: none;
	}

	.woocommerce.single .entry-summary > form.cart .product-addon-totals {
	font-size: 1rem; }

	.woocommerce .quantity {
	position: relative; }
	.woocommerce .quantity .icon {
		width: 36px;
		height: 36px; }
		.woocommerce .quantity .icon:before {
		width: 36px;
		height: 36px; }
	.woocommerce .quantity .qty {
		border-width: 0;
		width: 60px;
		padding: 10px 0;
		-moz-appearance: textfield;
		appearance: textfield;
		-webkit-appearance: textfield;
		text-align: center;
		font-weight: 600;
		display: inline-block;
		background-color: transparent;
		margin-right: 0; }
		.woocommerce .quantity .qty::-webkit-outer-spin-button, .woocommerce .quantity .qty::-webkit-inner-spin-button {
		-webkit-appearance: none; }
	.woocommerce .quantity .qty-box {
		border: 1px solid rgba(0, 0, 0, 0.25);
		padding: 0 20px;
		-webkit-transition: 0.5s;
		-o-transition: 0.5s;
		-moz-transition: 0.5s;
		transition: 0.5s;
		position: relative; }
		.woocommerce .quantity .qty-box input {
		line-height: normal; }
	.woocommerce .quantity .decrease,
	.woocommerce .quantity .increase {
		cursor: pointer;
		color: #999;
		-webkit-transition: 0.5s;
		-o-transition: 0.5s;
		-moz-transition: 0.5s;
		transition: 0.5s;
		display: inline-block;
		position: absolute;
		top: 0;
		left: 5px; }
		.woocommerce .quantity .decrease:before,
		.woocommerce .quantity .increase:before {
		-moz-background-size: 55%;
			background-size: 55%;
		background-position: center center;
		background-repeat: no-repeat;
		opacity: 0.5; }
	.woocommerce .quantity .increase {
		right: 5px;
		left: auto; }

	.woocommerce div.product form.cart {
	margin-bottom: 30px; }
	.woocommerce div.product form.cart div.quantity {
		margin: 0 30px 0 0; }
		.woocommerce div.product form.cart div.quantity .label {
		font-size: 12px;
		display: block;
		line-height: 1;
		margin-bottom: 3px; }

	.woocommerce div.product table div.quantity {
	float: none;
	margin: 0; }

	.woocommerce div.product.product-type-grouped .cart div.quantity {
	padding-left: 0; }
	.woocommerce div.product.product-type-grouped .cart div.quantity .label {
		display: none; }

	.woocommerce-cart .woocommerce table.shop_table td .quantity {
	width: 100px; }

	.star-rating-placeholder {
	height: 16px;
	margin: 0 auto 10px; }

	/**
	* Responsive.
	* Note: global.scss used 768, so follow this
	*/
	@media (max-width: 768px) {
	.woocommerce div.product form.cart div.quantity .qty-box {
		text-align: center; }
	.woocommerce div.product form.cart div.quantity .label {
		font-size: 16px; }
	.woocommerce table.shop_table_responsive tr .product-quantity::before .quantity,
	.woocommerce.woocommerce-page table.shop_table_responsive tr .product-quantity::before .quantity {
		display: -webkit-box;
		display: -webkit-flex;
		display: -moz-box;
		display: -ms-flexbox;
		display: flex; }
		.woocommerce table.shop_table_responsive tr .product-quantity::before .quantity:before,
		.woocommerce.woocommerce-page table.shop_table_responsive tr .product-quantity::before .quantity:before {
		content: attr(data-title) ": ";
		font-weight: 700;
		display: block;
		text-align: left; }
	.woocommerce .quantity .qty-box input {
		width: 100px; }
	.woocommerce .quantity .qty {
		font-size: 18px; }
	.woocommerce.single .entry-summary > form.cart button {
		font-size: 22px; }
	.woocommerce.single .entry-summary > form.cart .product-addon-totals {
		font-size: 1rem; } }

	@media (max-width: 479px) {
	.woocommerce div.product form.cart div.quantity {
		margin-bottom: 15px;
		float: none;
		margin-right: 0; }
	.woocommerce div.product .quantity .qty {
		width: 100%;
		min-width: 60px; }
	.woocommerce.single .entry-summary > form.cart .quantity {
		-webkit-flex-basis: 100%;
			-ms-flex-preferred-size: 100%;
				flex-basis: 100%; } }

		/* display control like ZStore */
		form.cart {
			position: relative;
		}

		form.cart .single_add_to_cart_button {
			position: absolute;
			left: 0;
			z-index: 10;

			/* Cover the input area. */
			width: 277px;
			border-radius: 5px;
			height: 52px;
		}

		.hide {
			display: none;
		}

		/* Mimic Zstore */
		#primary .quantity {
			width: 50%;
			text-align: center;
		}

		#primary .qty-box {
			padding: 6px 20px;
			border: none;
			background: #2640b2;
			border-radius: 5px;
		}

		#primary .qty-box input {
			color: white;
		}

		#primary .qty-box .decrease{
			top: 7px;
    		left: 50px;
		}
		
		#primary .qty-box .increase{
			top: 7px;
    		right: 50px;
		}
		
		#primary .qty-box .decrease:before,
		#primary .qty-box .increase:before{
			background-size: 80%;
			opacity: 1;
			filter: invert(1);
		}
   </style>

   <script>
		"use strict";

		(function ($) {
		'use strict';

		var SA = SA || {};

		SA.init = function () {
			SA.$body = $(document.body), SA.$window = $(window); // Single Product.

			this.productQuantity();
		};
		/**
		 * Change product quantity.
		 */
		SA.productQuantity = function () {
			SA.$body.on('click', '.quantity .increase, .quantity .decrease', function (e) {
			e.preventDefault();
			var $this = $(this),
				$qty = $this.siblings('.qty'),
				current = 0,
				min = parseFloat($qty.attr('min')),
				max = parseFloat($qty.attr('max')),
				step = parseFloat($qty.attr('step'));

			if ($qty.val() !== '') {
				current = parseFloat($qty.val());
			} else if ($qty.attr('placeholder') !== '') {
				current = parseFloat($qty.attr('placeholder'));
			}

			min = min ? min : 0;
			max = max ? max : current + 1;

			if ($this.hasClass('decrease') && current > min) {
				$qty.val(current - step);
				$qty.trigger('change');
			}

			if ($this.hasClass('increase') && current < max) {
				$qty.val(current + step);
				$qty.trigger('change');
			}
			}); // Prevent empty value by user.

			SA.$body.on('blur', '.quantity input', function (e) {
			if ('' === $(this).val()) {
				// $(this).val(1);
				$('.quantity .increase').click(); // Make sure the input box is non-empty and trigger the product addon script as well.
			}
			});
		};
		/**
		 * Document ready.
		 */


		$(function () {
			SA.init();

			// Display control.
			var updateSubmit = function(e){
				e.stopPropagation();
				e.preventDefault();

				$(this).addClass('hide');
				return false;
			}

			$('button[name="add-to-cart"].single_add_to_cart_button').on('submit', updateSubmit);
			$('button[name="add-to-cart"].single_add_to_cart_button').on('click', updateSubmit);

		});
	})(jQuery);
   </script>
	<?php
} );
