<?php
/**
 * Add JS and CSS to site 
 */
function cs_enqueue(){

	// On homepage 
    if(is_front_page()){

    	wp_enqueue_style('cs-style', get_stylesheet_uri());
    	wp_enqueue_script('jquery');
    	wp_enqueue_style('gf-open-sans', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&display=swap');
    	wp_enqueue_style('animate-css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

    	// The main homepage script
    	wp_enqueue_script('home_products', get_template_directory_uri() .'/js/home-products.js', ['jquery']);

    	// Pass variables to home-products.js 
    	wp_localize_script( 'home_products', 'page_data', array(
    	    'rest_url' => get_rest_url(),
    	    'stylesheet_url' => get_template_directory_uri() 
    	));	
    }
}
add_action('wp_enqueue_scripts', 'cs_enqueue');


/**
 * Create the custom JSON endpoint
 */
add_action( 'rest_api_init', function(){

	register_rest_route( 'api', '/fetch-data', array(
		'methods' => 'GET',
		'callback' => 'cs_data_endpoint',
	));
});


/**
 * Custom JSON endpoint
 *   
 */
function cs_data_endpoint( $request ) {

	/**
	 * Expected GET parameters:
	 * 
	 * order_by: 	highest, lowest
	 * in_stock: 	true, false
	 * _id: 		SDI61BVO6XS
	 * 
	 * Example: /api/fetch-data?order-by=highest&in-stock=true
	 */

	$order_by = $request->get_param( 'order_by' ); 
	$in_stock = $request->get_param( 'in_stock' ); 
	$_id = $request->get_param( '_id' ); 

	/** 
	 * Validate the GET parameters
	 * Return errors if they fail
	 */

	// Validate $in_stock
	// Must be 'true' or 'false'
	if(isset($in_stock) && $in_stock !== 'true' && $in_stock !== 'false'){

		// Throw unexpected value error
		return new WP_Error( 'Unexpected value', 'Unexpected value for in_stock parameter. Expects \'true\' or \'false\'', array('status' => 404) );
	} 

	// Validate $order_by
	// Must be 'lowest' or 'highest'
	if(isset($order_by) && $order_by !== 'lowest' && $order_by !== 'highest'){

		// Throw unexpected value error
		return new WP_Error( 'Unexpected value', 'Unexpected value for order_by parameter. Expects \'lowest\' or \'highest\'', array('status' => 404) );
	}

	// Validate $_id
	// Must be string of 11 characters
	if(isset($_id) && strlen($_id) !== 11){

		// Throw unexpected value error
		return new WP_Error( 'Unexpected value', 'Unexpected string length for _id parameter. Expects 11 characters', array('status' => 404) );
	} 

	// The product dataset
	$dataset = [
		[
			'_id' => 'SDI61BVO6XS',
			'order' => 0,
			'title' => 'Starter',
			'in_stock' => true,
			'cost' => 9.99,
			'meta' => '20 GB'
		],
		[
			'_id' => 'KTG54KBF6UY',
			'order' => 1,
			'title' => 'Amateur',
			'in_stock' => true,
			'cost' => 16.00,
			'meta' => '40 GB'
		],
		[
			'_id' => 'XIN61FNM1FU',
			'order' => 2,
			'title' => 'Pro',
			'in_stock' => false,
			'cost' => 29.00,
			'meta' => '120 GB'
		],
		[
			'_id' => 'BKC22IMQ5FV',
			'order' => 3,
			'title' => 'Enterprise',
			'in_stock' => true,
			'cost' => 99.00,
			'meta' => 'Unlimited'
		]
	];


	/**
	 * Returns individual products by _id
	 */

	// Get product by '_id'
	if (isset($_id)) {

		// Search dataset by '_id'
		$key = array_search($_id, array_column($dataset, '_id'));

		// If no matching _id return an error
		if(empty($key) && !is_numeric($key)){ // (to fix the 0 == false issue https://stackoverflow.com/a/5924985/1382451

			return new WP_Error( 'No product', 'There is no product matching that id', array('status' => 404) );
		} 

		// Return the product
		$response = new WP_REST_Response($dataset[$key]);
		$response->set_status(200);
		return $response;
	}


	/**
	 * Default to all products when no GET vars are passed
	 */

	if(!isset($order_by) && !isset($in_stock) && !isset($id)) {

		// Error if no products found
		if (empty($dataset)) {

		   return new WP_Error( 'Empty dataset', 'There are no products to display', array('status' => 404) );
		}

		// Return all the products, default to order ASC
		$keys = array_column($dataset, 'order');
		array_multisort($keys, SORT_ASC, $dataset);
		
		$response = new WP_REST_Response($dataset);
		$response->set_status(200);
		return $response;
	}


	/**
	 * Build the main product query 
	 * (for order_by and in_stock parameters) 
	 */

	$products = []; // Fill with products and return as JSON
 	

 	/**
 	 * In Stock / All 
 	 */

 	// Just get products in stock
	if ($in_stock === 'true') {

		// Remove out of stock products
		foreach ($dataset as $key => $product) {
			if($product['in_stock']){
				
				$products[] = $product;
			}
		}

	// Otherwise get all products
	// When in_stock = false, but also as a fallback when 'in_stock' not set
	} else { 
		
		// Keep all products
		$products = $dataset;
	}

	/**
	 * Order
	 */

	// Put in order (by 'order' field in dataset)
	if($order_by){

		// Lowest first
		if($order_by == 'lowest'){

			$keys = array_column($products, 'order');
			array_multisort($keys, SORT_ASC, $products);
		
		// Highest first
		} else if($order_by == 'highest'){

			$keys = array_column($products, 'order');
			array_multisort($keys, SORT_DESC, $products);

		} 
	} 

	// Return the populated dataset
	$response = new WP_REST_Response($products);
	$response->set_status(200);
	return $response;
}


/**
 * Create ACF fields 
 */
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_622a0537d7afe',
		'title' => 'Homepage Info',
		'fields' => array(
			array(
				'key' => 'field_622a05637a782',
				'label' => 'Title',
				'name' => 'title',
				'type' => 'text',
				'instructions' => 'The title of the page',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Dones quis lectus gravidas',
				'placeholder' => 'Dones quis lectus gravidas',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_622a059f7a783',
				'label' => 'Description',
				'name' => 'description',
				'type' => 'textarea',
				'instructions' => 'The text to display on the page.',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nisi libero, posuere quis libero et, efficitur congue eros!',
				'placeholder' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas nisi libero, posuere quis libero et, efficitur congue eros!',
				'maxlength' => '',
				'rows' => '',
				'new_lines' => 'wpautop',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'page_type',
					'operator' => '==',
					'value' => 'front_page',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	));

endif;		

