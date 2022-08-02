<?php

// Copy the code blow.

// Step 7.
// Add right hand side information.
add_action( 'woocommerce_single_product_summary', function() {
	global $product;
	?>
	<table>
		<tbody>
			<tr>
				<th style="width: 130px;"><?php woocommerce_show_product_sale_flash(); ?></th>
				<td>
					<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>" style="margin: 0;"><?php echo $product->get_price_html(); ?></p>
					<p>已售出： <?php echo $product->get_total_sales();?></p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td></td>
			</tr>
		</tbody>
	</table>
	<?php
}, 10);

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 21 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 41 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 51 );