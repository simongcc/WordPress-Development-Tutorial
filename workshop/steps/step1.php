<?php

// Copy the code blow.

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