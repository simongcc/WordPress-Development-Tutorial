<?php

// Copy the code blow.

// Step 6.
// Add custom stock notice and remove default sales badge.
add_action(
	'woocommerce_before_single_product_summary',
		function() {
			global $product;
			$html         = '';

			if( $product->get_stock_quantity() < 5 ) {
				?>
				<div class="" style="background: red; color: white; display: inline-block; padding: 0.45em 0.85em; float: right;">
				<?php
				echo '尚餘少量';
				?>
				</div>
				<?php
			}

			// $availability = $product->get_availability();

			// var_dump($product->get_stock_quantity());

			// if ( ! empty( $availability['availability'] ) ) {
			// 	ob_start();

			// 	wc_get_template(
			// 		'single-product/stock.php',
			// 		array(
			// 			'product'      => $product,
			// 			'class'        => $availability['class'],
			// 			'availability' => $availability['availability'],
			// 		)
			// 	);

			// 	$html = ob_get_clean();
			// }

		echo $html;
	}, 30);

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );