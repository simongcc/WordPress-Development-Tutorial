<?php

// Copy the code blow.

// Step 6.
// Add custom stock notice and remove default sales badge.
add_action(
	'woocommerce_before_single_product_summary',
		function() {
			global $product;
			$html         = '';

			?>
			<div class="" style="background: red; color: white; display: inline-block; padding: 0.45em 0.85em; float: right;">
			<?php

			if( $product->get_stock_quantity() < 5 && $product->get_stock_quantity() > 0 ) {
				echo '尚餘少量';
			}

			if( $product->get_stock_quantity() === 0 ) {
				$availability = $product->get_availability();
	
				if ( ! empty( $availability['availability'] ) ) {
					$class = $availability['class'];
					$availability = $availability['availability'];
					?>
					<p class="stock <?php echo esc_attr( $class ); ?>" style="color: inherit;     margin: 0;"><?php echo wp_kses_post( $availability ); ?></p>
					<?php
				}
			}
		?>
		</div>
		<?php
	}, 30);

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );