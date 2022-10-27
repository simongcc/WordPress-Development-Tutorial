<?php

// Copy the code blow.

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