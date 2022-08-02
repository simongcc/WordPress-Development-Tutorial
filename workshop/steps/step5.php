<?php

// Copy the code blow.

// Step 5.
// Remove default "Out of stock" notice in default location.
add_filter( 'woocommerce_get_stock_html', function() {
	return '';
}, 999, 2 );