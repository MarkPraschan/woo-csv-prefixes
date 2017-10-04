// ========= EXPORT PREFIXES = WooCommerce Customer/Order CSV Export ========= //

// add custom column headers
function wc_csv_export_modify_column_headers( $column_headers ) { 
 
	$new_headers = array(
  // === HEADERS / COLUMN NAMES GO HERE === //
		'prefixed_order_num' => 'Pre_Order_Number',
		'prefixed_user_id' => 'Pre_User_Id',
		// add other column headers here in the format column_key => Column Name
	);
 
	return array_merge( $column_headers, $new_headers );
}
add_filter( 'wc_customer_order_csv_export_order_headers', 'wc_csv_export_modify_column_headers' );
// set the data for each for custom columns
function wc_csv_export_modify_row_data( $order_data, $order, $csv_generator ) {
 
	$custom_data = array(
  // === CUSTOM PREFIXES GO HERE === //
  // === I've used "WEB-" as my prefix so order number "12345" is output as "WEB-12345" === //
		'prefixed_order_num' => 'WEB-' . $order->get_id(),
		'prefixed_user_id' => 'WEB-' . $order->get_user_id(),
		// add other row data here in the format column_key => data
	);
 
	$new_order_data   = array();
	$one_row_per_item = false;
	
	if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {
		// pre 4.0 compatibility
		$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );
	} elseif ( isset( $csv_generator->format_definition ) ) {
		// post 4.0 (requires 4.0.3+)
		$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
	}
	if ( $one_row_per_item ) {
		foreach ( $order_data as $data ) {
			$new_order_data[] = array_merge( (array) $data, $custom_data );
		}
	} else {
		$new_order_data = array_merge( $order_data, $custom_data );
	}
	return $new_order_data;
}
add_filter( 'wc_customer_order_csv_export_order_row', 'wc_csv_export_modify_row_data', 10, 3 );
