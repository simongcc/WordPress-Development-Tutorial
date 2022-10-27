<?php

// Copy the code blow.

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