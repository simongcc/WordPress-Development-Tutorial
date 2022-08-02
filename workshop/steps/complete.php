<?php
/**
 * @package Workshop_Shop_DIY
 * @version 1.0.0
 */
/*
Plugin Name: Learnmore Workshop - Shop DIY
Plugin URI: https://learnmore.com.hk/
Description: This is a WordPress plugin demonstration in the Learnmore.com.hk Workshop for adding custom features/layout to WooCommerce.
Author: Simon Ng
Version: 1.0.0
Author URI: http://zenrity.com/
*/

/** Add any code below. */

// Step 1.
// Remove the original title, rating and product images output.
add_action(
	'init',
	function() {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	}
);

// Step 2.
// Add the product image and title with custom layout.
add_action(
	'woocommerce_before_single_product_summary',
	function() {
		// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
		if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
			return;
		}

		global $product;

		$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
		$post_thumbnail_id = $product->get_image_id();
		$wrapper_classes   = apply_filters(
			'woocommerce_single_product_image_gallery_classes',
			array(
				'woocommerce-product-gallery',
				'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
				'woocommerce-product-gallery--columns-' . absint( $columns ),
				'images',
			)
		);
		?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
		<?php woocommerce_template_single_title(); ?>
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		if ( $post_thumbnail_id ) {
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div>
		<?php
	},
	20
);

// Step 3.
// Add the rating to new location.
add_action(
	'woocommerce_after_single_product_summary',
	function() {
		?>
	<div style="
		float: left;
		clear: both;
	">
		<?php
		woocommerce_template_single_rating();
	},
	5
);

// Step 4.
// Remove the original price, excerpt meta and sharing.
add_action(
	'init',
	function() {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
	}
);

// Step 5.
// Remove default "Out of stock" notice in default location.
add_filter( 'woocommerce_get_stock_html', function() {
	return '';
}, 999, 2 );

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