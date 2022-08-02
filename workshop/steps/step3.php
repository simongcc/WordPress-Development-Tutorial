<?php

// Copy the code blow.

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