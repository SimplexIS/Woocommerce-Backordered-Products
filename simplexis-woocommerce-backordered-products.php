<?php
/**
 * Plugin Name: SimplexIS Woocommerce backorderd products
 * Description: This plugin will create a submenu page, showing a table of all backordered products.
 * Version: 1.1
 * Author: SimplexIS
 * Author URI: http://simplexis.nl/
 * License: GPLv3
 */

add_action('admin_menu', 'simplexis_woocommerce_backordered_products_menu');

function simplexis_woocommerce_backordered_products_menu() {
	add_submenu_page('woocommerce', 'Backordered products', 'Backordered products', 'manage_options', 'simplexis-woocommerce-backordered-products', 'simplexis_woocommerce_backordered_products_page');
}

function simplexis_woocommerce_backordered_products_page() {
	if (!current_user_can('manage_options')) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	global $wpdb;

	$products = $wpdb->get_results(
		"
		SELECT ".$wpdb->prefix."woocommerce_order_items.order_item_name, ".$wpdb->prefix."woocommerce_order_items.order_id, ".$wpdb->prefix."woocommerce_order_itemmeta.meta_value
		FROM ".$wpdb->prefix."woocommerce_order_itemmeta, ".$wpdb->prefix."woocommerce_order_items, ".$wpdb->prefix."posts
		WHERE ".$wpdb->prefix."woocommerce_order_itemmeta.meta_key =  'Backordered'
		AND ".$wpdb->prefix."woocommerce_order_itemmeta.order_item_id = ".$wpdb->prefix."woocommerce_order_items.order_item_id
		AND ".$wpdb->prefix."woocommerce_order_items.order_id = ".$wpdb->prefix."posts.ID
		AND ".$wpdb->prefix."posts.post_status NOT LIKE 'trash'
		"
		, ARRAY_A);

	echo '<div class="wrap">';
		echo '<h2>Backordered products</h2>';
		echo '<table class="widefat">';
			echo '<thead>';
    				echo '<tr>';
					echo '<th>Product</th>';
					echo '<th>Customer</th>';
					echo '<th>Order #</th>';
					echo '<th>Order status</th>';
					echo '<th>Amount</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tfoot>';
				echo '<tr>';
					echo '<th>Product</th>';
					echo '<th>Customer</th>';
					echo '<th>Order #</th>';
					echo '<th>Order status</th>';
					echo '<th>Amount</th>';
				echo '</tr>';
			echo '</tfoot>';
			echo '<tbody>';
				foreach ($products as $product){
				echo '<tr>';
					echo '<td>'.$product['order_item_name'].'</td>';
					echo '<td>'.get_post_meta($product['order_id'], '_billing_first_name', true).' '.get_post_meta($product['order_id'], '_billing_last_name', true).'</td>';
					echo '<td><a href="'.admin_url('post.php?post='.$product['order_id'].'&action=edit').'">'.$product['order_id'].'</a></td>';
					$order = new WC_Order($product['order_id']);
					echo '<td>'.$order->status.'</td>';
					echo '<td>'.$product['meta_value'].'</td>';
				echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	echo '</div>';
}
?>
