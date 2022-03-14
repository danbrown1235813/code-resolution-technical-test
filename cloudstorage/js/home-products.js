/**
 * Homepage JS 
 */

 jQuery(function(){

 	// Initialise page

 	// Global variables 
	let loading = '<div class="loading"><div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>'; // https://loading.io/css/

	// Nice error text
	let before_error = 'Whoops. ';
	let after_error = '. Try a search on our <a href="#">homepage</a>. Or our <a href="#">support team</a> would love to assist you.';

	/**
	 * Get products
	 */

	// Default product settings
	let in_stock_setting = 'false'
	let order_by_setting = 'lowest';

	// Remember these settings in HTML data attributes
	jQuery('#settings-data').attr('data-in-stock', in_stock_setting);
	jQuery('#settings-data').attr('data-order-by', order_by_setting);

	// Fetch initial page products
	fetch_data(['order_by=' + order_by_setting, 'in_stock=' + in_stock_setting]); 
	
	// That's it. The page is initialised. 


	/**
	 * Order by price, lowest first
	 */
	jQuery(".sort_lowest").click(function(e){

		e.preventDefault();

		// Remember this order-by setting
		jQuery('#settings-data').attr('data-order-by', 'lowest');
		jQuery('.order-by__current').text(': Lowest');

		// Check in-stock setting
		let in_stock_setting = jQuery('#settings-data').attr('data-in-stock');

		// Remove products and replace with loader
		keep_products_height();
		jQuery('.products').empty().append(loading);

		// Get the products
		fetch_data(['order_by=lowest', 'in_stock='+ in_stock_setting]);
	});


	/** 
	 * Order by price, highest first
	 */
	 jQuery(".sort_highest").click(function(e){

		e.preventDefault();

		// Remember this order-by setting
		jQuery('#settings-data').attr('data-order-by', 'highest');
		jQuery('.order-by__current').text(': Highest');

		// Check in-stock setting
		let in_stock_setting = jQuery('#settings-data').attr('data-in-stock');

		// Remove products and replace with loader
		keep_products_height();
		jQuery('.products').empty().append(loading);

		fetch_data(['order_by=highest', 'in_stock='+ in_stock_setting]);
	});

 
	/**
	 * Toggle in stock products
	 */
	 jQuery(".toggle_in_stock").click(function(){

 		let $button = jQuery(".toggle_in_stock");

 		/**
 		 * Show in stock  
 		 */
 		if( !$button.hasClass('toggled')){

 			$button.addClass('toggled'); // Add a toggled class to show only in stock

 			// Change button text and ellipse colour
 			jQuery('.filter a img').attr("src", page_data.stylesheet_url + '/images/ellipse-teal.svg'); 
 			jQuery('.filter a span').text('Showing In Stock');

 			// Remember this in-stock data
 			jQuery('#settings-data').attr('data-in-stock', 'true');

 			// Check other data
 			let order_by_setting = jQuery('#settings-data').attr('data-order-by');

 			// Remove products and replace with loader
 			keep_products_height();
			jQuery('.products').empty().append(loading);

 		  	fetch_data(['in_stock=true', 'order_by=' + order_by_setting]);

 		/**
 		 * Show all 
 		 */
 		} else {

 		  	$button.removeClass('toggled'); // Remove the toggled class to show all products

 			// Unmute the button 
 			jQuery('.filter a img'). attr("src", page_data.stylesheet_url + '/images/ellipse.svg'); 
 			jQuery('.filter a span').text('Showing All');

 			// Remember this in-stock setting
 			jQuery('#settings-data').attr('data-in-stock', 'false');

 			// Check order-by setting
 			let order_by_setting = jQuery('#settings-data').attr('data-order-by');

 			// Remove products and replace with loader
 			keep_products_height();
			jQuery('.products').empty().append(loading);

 		  	fetch_data(['in_stock=false', 'order_by=' + order_by_setting]);
 		} 
 	});


	 /**
	  * Open / close the 'Order By' dropdown
	  */
	 jQuery('.order-by').click(function(e){

	 	let $button = jQuery(this);

	 	// Open dropdown
	 	if(!$button.hasClass('toggled')){ // Adds a toggled class when dropdown is opened

	 		$button.addClass('toggled');
	 		jQuery('.order-by__options') .css("display", "flex")
			    .hide()
			    .fadeIn(300);

	 	// Close dropdown
	 	} else {

	 		$button.removeClass('toggled'); // Removes a toggled class when dropdown is closed
	 		jQuery('.order-by__options').fadeOut();
	 	}
	 });
});


/**
 * Maintain products height after removal
 * Otherwise page jumps when height becomes 0
 * 
 * NB Remove afterward with: jQuery('.products').css('height', '');
 */
function keep_products_height(){

	$products = jQuery('.products');
	let height = $products.height();
	$products.height(height);
}


/**
 * API query function to fetch products 
 * 
 * Accepts arguments: query_vars (array of GET parameters)
 * 
 * Example usage: fetch_data(['in_stock=true', 'order_by=highest'])
 */
function fetch_data(query_vars = []){	

	var query_str = "?" +query_vars.join("&"); // Format for GET request

	// Request the products from the API
	// Example: /wp-json/api/fetch-data?order-by=lowest&in_stock=true
	jQuery.get(page_data.rest_url + "api/fetch-data" + query_str, function(products, status){
		
		// Display the returned products
		products.forEach(function(item, index){

			let product_btn; // Select or unavailable
			let cost = parseFloat(item.cost).toFixed(2); // Maintain .00's in price

			// Create markup for for available / unavailable buttons
			if(item.in_stock){

				product_btn = `<div class="product__select">
						<a class="product__select-btn"><span>Select</span> <img src="${page_data.stylesheet_url}/images/select-icon.svg'" alt=""></a>
					</div>`;
			} else {

				product_btn = `<div class="product__select">
						<a class="product__select-btn unavailable"><span>Unavailable</span> <img src="${page_data.stylesheet_url}/images/select-icon-unavailable.svg" alt=""></a>
					</div>`;
			}

			jQuery('.loading').hide(); // Remove the loading animation

			// Create and add product HTML 
			jQuery('.products').append(`<div class="product">
				<img class="product__image" src="${page_data.stylesheet_url}/images/product-image.svg" alt="">
				<div class="product__body">

					<div class="product__info">
						<h3>${item.title}</h3>
						<p>${item.meta}</p>
					</div>

					<div class="product__cost">
						<p>\$${cost}/pm</p>
					</div>
					${product_btn}
				</div>
			</div>`).css('opacity', 0).animate({opacity: 1}, 300); // Fade back in (opacity as it doesn't affect height)
		});
		jQuery('.products').css('height', ''); // Product container can go back to auto height now

	// Handle errors if no products returned
	}).fail(function(err){

		// Display error message (we wouldn't output the real error message on a live site)
    	let error_message = before_error + err.responseJSON.message + after_error;
    	jQuery('.loading').hide();
    	jQuery('.products').css('height', ''); // Unset products container height
    	jQuery('.products').append(`<p class="error">${error_message}</p>`);

  	});
}

