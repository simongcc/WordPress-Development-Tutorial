<?php

// Copy the code blow.

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

