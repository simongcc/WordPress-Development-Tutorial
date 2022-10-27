<?php

// Copy the code blow.

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