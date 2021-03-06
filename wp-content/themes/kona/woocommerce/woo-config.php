<?php 

/*-----------------------------------------------------------------------------------

	Ajax update mini cart

-----------------------------------------------------------------------------------*/
if( !function_exists( 'sr_woo_minicart_callback' ) ) {
	function sr_woo_minicart_callback() {
		global $wpdb;
		
		if ($_POST['wpml']) {
			global $sitepress;
			$sitepress->switch_lang($_POST['wpml'], true);
		}
		wc_print_notices();  // the add to cart message is not shown on next page
		?>
       	<div>
       	<div class="menu-cart-content">
			<?php woocommerce_mini_cart(); ?>
    	</div>
    	</div>
        <?php
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_nopriv_sr_woo_minicart', 'sr_woo_minicart_callback'); 
add_action('wp_ajax_sr_woo_minicart', 'sr_woo_minicart_callback');





/*-----------------------------------------------------------------------------------

	Ajax add to cart

-----------------------------------------------------------------------------------*/
if( !function_exists( 'sr_add_to_cart_callback' ) ) {
	function sr_add_to_cart_callback() {
		global $wpdb;
				
		echo 'added to cart';
		// the add to cart action is done automatically.  No more action need to take here
		
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_nopriv_sr_add_to_cart', 'sr_add_to_cart_callback'); 
add_action('wp_ajax_sr_add_to_cart', 'sr_add_to_cart_callback');



/*-----------------------------------------------------------------------------------

	Ajax search

-----------------------------------------------------------------------------------*/
if( !function_exists( 'sr_ajax_search_callback' ) ) {
	function sr_ajax_search_callback() {
		global $wpdb;
		
		if ($_POST['wpml']) {
			global $sitepress;
			$sitepress->switch_lang($_POST['wpml'], true);
		}
		
		$columns = 5;
		$spaced = "spaced-big";
		$unveil = 'no-anim';
		if (get_option('_sr_headersearchcol')) { $columns = intval(get_option('_sr_headersearchcol')); }
		if (get_option('_sr_shopgridspaced')) { $spaced = get_option('_sr_shopgridspaced'); }
		if (get_option('_sr_shopgridunveil')) { $unveil = get_option('_sr_shopgridunveil'); }
		$count = $columns * 2;
		
		$search = sanitize_text_field( $_POST['s'] );
			
		echo do_shortcode( '[sr-shopitems gridid="search-shop-grid" gridwidth="wrapper" columns="'.$columns.'" style="equal" unveil="'.$unveil.'" spacing="'.$spaced.'" unveil="do-anim" layoutcustom="inherit" filtershow="all" filtercategory="null" filteritems="null" count="'.$count.'" filterorder="date" filtersort="DESC" pagination="loadonclick" search="'.$search.'" ajax="1" showprice="1"]' );
		
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_nopriv_sr_ajax_search', 'sr_ajax_search_callback'); 
add_action('wp_ajax_sr_ajax_search', 'sr_ajax_search_callback');


/*-----------------------------------------------------------------------------------

	Ajax Quick View

-----------------------------------------------------------------------------------*/
if( !function_exists( 'sr_ajax_quickview_callback' ) ) {
	function sr_ajax_quickview_callback() {
		global $wpdb;
				
		//echo 'id = '.$_POST['prodId'];
		//echo do_shortcode('[product_page id="'.$_POST['prodId'].'"]');
		
		$params = array(
		 'p' => $_POST['prodId'],
		 'post_type' => 'product'
		);
		$wc_query = new WP_Query($params);
		if ($wc_query->have_posts()) {
			while ($wc_query->have_posts()) { 
				$wc_query->the_post(); 
				$quickView = true;
				include( locate_template( 'woocommerce/content-single-product.php' ) );
			}
		}
		
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_nopriv_sr_ajax_quickview', 'sr_ajax_quickview_callback'); 
add_action('wp_ajax_sr_ajax_quickview', 'sr_ajax_quickview_callback');



/*-----------------------------------------------------------------------------------

	Switch currency for all needed ajax calls

-----------------------------------------------------------------------------------*/
add_filter( 'wcml_multi_currency_ajax_actions', 'add_action_to_multi_currency_ajax', 10, 1 );
function add_action_to_multi_currency_ajax( $ajax_actions ) {
  
    $ajax_actions[] = 'sr_woo_minicart'; // Add a AJAX action to the array
    $ajax_actions[] = 'sr_ajax_search'; 
    $ajax_actions[] = 'sr_ajax_quickview';
    return $ajax_actions;
  
}


/*-----------------------------------------------------------------------------------

	WooCommerce Configuration

-----------------------------------------------------------------------------------*/
// Adds theme support for woocommerce 
add_theme_support('woocommerce');
add_theme_support( 'wc-product-gallery-zoom' );

// Disbale default woo css
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );



/*-----------------------------------------------------------------------------------*/

/*	Remove some default elements

/*-----------------------------------------------------------------------------------*/

// Remove Breadcrumb

// for single pages
function kona_breadcrumbs() {
    if(is_product()){
    	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
    }
}
add_filter('woocommerce_before_main_content','kona_breadcrumbs');

// for product pages
if (!get_option('_sr_shopgridshowbreadcrumb') && get_option('_sr_optiontree')) {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}

// Change Breadcrumb separator
add_filter( 'woocommerce_breadcrumb_defaults', 'kona_change_breadcrumb_delimiter' );
function kona_change_breadcrumb_delimiter( $defaults ) {
	$defaults['delimiter'] = '';
	return $defaults;
}


// Products per page
add_filter( 'loop_shop_per_page', 'kona_loop_shop_per_page', 20 );
function kona_loop_shop_per_page( $cols ) {
	$shopcount = 12;
  	if (get_option('_sr_shopgridcount')) { $shopcount = intval(get_option('_sr_shopgridcount')); }
	return $shopcount;
}


// Remove realted products
if (!get_option('_sr_shopsinglerelated') && get_option('_sr_optiontree')) {
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}

// Remove Upsells "You may also like..."
if (!get_option('_sr_shopsingleupsells')) {
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
}

// Remove Crosssells "You may be interested in..." from cart
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10, 1 );
if (get_option('_sr_shopsingleupsells')) {
	add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 5, 1 );
} 

// Disable Reviews
if (!get_option('_sr_shopsinglereviews') && get_option('_sr_optiontree')) {
	add_filter( 'woocommerce_product_tabs', 'kona_woo_disablereviews', 98 );
		function kona_woo_disablereviews($tabs) {
		unset($tabs['reviews']);
		return $tabs;
	}
}

// remove stars on product-info
if (!get_option('_sr_shopsinglerating')) {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
}


// wrap the info messages on checkout page
add_action( 'woocommerce_before_checkout_form', 'kona_wrap_forms_start', 1 );
function kona_wrap_forms_start() { echo '<div class="before-checkout clearfix">'; }
add_action( 'woocommerce_before_checkout_form', 'kona_wrap_forms_end', 12 );
function kona_wrap_forms_end() { echo '</div>'; }


// remove the tab titles in single
add_filter('woocommerce_product_description_heading', '__return_null');
add_filter('woocommerce_product_additional_information_heading', '__return_null');


// remove the print_notice for loop page + single page
remove_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );


// remove the variation from product title in cart and display it under the title
// Example:   Product Name + Blue   -->    Product Name
add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_false' );


/*-----------------------------------------------------------------------------------

	Register Shop Widget areas

-----------------------------------------------------------------------------------*/
if( !function_exists( 'kona_shop_widgets' ) ) {
	function kona_shop_widgets() {
		
		$titleSize = 'h6';
		
		if (get_option('_sr_shopgridsidebar') !== 'false') {
			register_sidebar( array(
				'name' => esc_html__( 'Shop Sidebar', 'kona' ),
				'id' => 'shop-sidebar',
				'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
				'after_widget' => "</div>",
				'before_title' => '<'.$titleSize.' class="widget-title title-alt">',
				'after_title' => '</'.$titleSize.'>'
			) );
		}
		
		
		if (get_option('_sr_shopgridshowfilter')) {
			$filterCols = get_option('_sr_shopgridfiltercolumns') ;
			if( !get_option('_sr_shopgridfiltercolumns') ) { $filterCols = array(1,2,3); }  // default for widget imports
			for	($i=1;$i<=$filterCols;$i++) {
				$sidebarName = esc_html__("Filter (1st column)", 'kona' ); $sidebarId = "filter-1st";
				if ($i == 2 ) { $sidebarName = esc_html__("Filter (2nd column)", 'kona' ); $sidebarId = "filter-2nd"; } else
				if ($i == 3 ) { $sidebarName = esc_html__("Filter (3rd column)", 'kona' ); $sidebarId = "filter-3rd"; } else
				if ($i == 4 ) { $sidebarName = esc_html__("Filter (4th column)", 'kona' ); $sidebarId = "filter-4th"; }
				if ($i == 5 ) { $sidebarName = esc_html__("Filter (5th column)", 'kona' ); $sidebarId = "filter-5th"; }
				register_sidebar( array(
					'name' => $sidebarName,
					'id' => $sidebarId,
					'before_widget' => '<div id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => "</div>",
					'before_title' => '<'.$titleSize.' class="widget-title title-alt">',
					'after_title' => '</'.$titleSize.'>'
				) );
			}
		}
		
		
	}
	
}
add_action( 'widgets_init', 'kona_shop_widgets' );



/*-----------------------------------------------------------------------------------*/

/*	BEFORE GRID ( Result count / Sort by / Filter )

/*-----------------------------------------------------------------------------------*/
// grid options (result count + ordering)
if (!get_option('_sr_shopgridshowresults') && !get_option('_sr_shopgridshowsorting')) { } else {
	add_action( 'woocommerce_before_shop_loop', 'kona_woo_wrapgridoptions_start', 2 );
	if ( ! function_exists( 'kona_woo_wrapgridoptions_start' ) ) {
		function kona_woo_wrapgridoptions_start() { echo '<div class="grid-options clearfix">'; } 
	}
	add_action( 'woocommerce_before_shop_loop', 'kona_woo_wrapgridoptions_end', 52 );
	if ( ! function_exists( 'kona_woo_wrapgridoptions_end' ) ) {
		function kona_woo_wrapgridoptions_end() { echo '</div>'; } 
	}
}

// Remove the result count
if (!get_option('_sr_shopgridshowresults')) {
	remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_result_count', 20 );
}

// Remove the sorting dropdown from Woocommerce
if (!get_option('_sr_shopgridshowsorting')) {
	remove_action( 'woocommerce_before_shop_loop' , 'woocommerce_catalog_ordering', 30 );
}

// Move the sorting dropdown from Woocommerce
if (get_option('_sr_shopgridshowfilter') && get_option('_sr_shopgridshowsorting')) {
	add_action( 'woocommerce_archive_description' , 'woocommerce_catalog_ordering', 199 );
}

// add filter option to shop head
if (get_option('_sr_shopgridshowfilter')) {
	add_action( 'woocommerce_archive_description', 'kona_woo_add_filter', 200 );
	if ( ! function_exists( 'kona_woo_add_filter' ) ) {
		function kona_woo_add_filter() { 
			echo '<a href="#" class="sr-button filter-open sr-filteropen">'.esc_html__("Filter", 'kona' ).'</a>';
		} 
	}
	
	add_action( 'woocommerce_archive_description', 'kona_woo_before_filter', 198 );
	if ( ! function_exists( 'kona_woo_before_filter' ) ) {
		function kona_woo_before_filter() { 
			$buttonClass = "";
			if (!get_option('_sr_shopgridshowresults')) { $buttonClass .= " static"; }
			if (!get_option('_sr_shopgridshowresults')) { $buttonClass .= " filter-left"; }
			echo '<div class="filter-option '.esc_attr($buttonClass).'">';
			echo '<div class="filter-buttons clearfix">';
		} 
	}
	
	add_action( 'woocommerce_archive_description', 'kona_woo_after_filter', 201 );
	if ( ! function_exists( 'kona_woo_after_filter' ) ) {
		function kona_woo_after_filter() { 
			echo '</div>';
			echo '</div>';
			
			echo '<div class="clear"></div>';
			echo '<div class="filter-container">';
			
			echo '<div class="column-section spaced-big clearfix">';
				$filterCols = get_option('_sr_shopgridfiltercolumns') ;
				$colClass = "one-third";
				if ($filterCols == 4) { $colClass = "one-fourth"; }
				if ($filterCols == 5) { $colClass = "one-fifth"; }
				for	($i=1;$i<=$filterCols;$i++) {
					echo '<div class="column '.esc_attr($colClass).'';
					if ($i == $filterCols) { echo ' last-col'; }
					echo '">';
					if ($i == 1 && is_active_sidebar( 'filter-1st' ) ) { dynamic_sidebar( 'filter-1st' ); } else
					if ($i == 2 && is_active_sidebar( 'filter-2nd' ) ) { dynamic_sidebar( 'filter-2nd' ); } else
					if ($i == 3 && is_active_sidebar( 'filter-3rd' ) ) { dynamic_sidebar( 'filter-3rd' ); } else
					if ($i == 4 && is_active_sidebar( 'filter-4th' ) ) { dynamic_sidebar( 'filter-4th' ); }
					echo '</div>';
				}
            echo '</div>';
			echo '</div>';
		} 
	}
}


/*-----------------------------------------------------------------------------------*/

/*	Edit Price output

/*-----------------------------------------------------------------------------------*/
function sr_price_html( $price, $product ){
	$priceAppearance = get_option('_sr_shopgridpriceappearance') ;
	if ( $product->get_price() && $product->get_regular_price() && ($product->get_price() !== $product->get_regular_price()) ) {
		// sale price for simple products
		$from = $product->get_regular_price();
		$to = $product->get_price();
		return '<ins>'.( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) .'</ins>
		<del>'. ( ( is_numeric( $from ) ) ? wc_price( $from ) : $from ) .' </del>';
	} else if ($product->is_type( 'variable' ) && $priceAppearance == "range" && $product->get_variation_price( 'max', true ) && $product->get_variation_price( 'min', true )) {
		// range price for variable product
		$max_price = $product->get_variation_price( 'max', true );
		$min_price = $product->get_variation_price( 'min', true );
		return '<ins>'.( ( is_numeric( $min_price ) ) ? wc_price( $min_price ) : $min_price ) .'</ins> &ndash;
		<ins>'. ( ( is_numeric( $max_price ) ) ? wc_price( $max_price ) : $max_price ) .' </ins>';
	} else if ($product->is_type( 'variable' ) && ($product->get_variation_regular_price( 'min', true ) !== $product->get_variation_sale_price( 'min', true ))) {
		// standard sale price for variable product
		$min_price_regular = $product->get_variation_regular_price( 'min', true );
		$min_price_sale    = $product->get_variation_sale_price( 'min', true );
		return '<ins>'.( ( is_numeric( $min_price_sale ) ) ? wc_price( $min_price_sale ) : $min_price_sale ) .'</ins>
		<del>'. ( ( is_numeric( $min_price_regular ) ) ? wc_price( $min_price_regular ) : $min_price_regular ) .' </del>';
	} else {
		// normal price
		$to = $product->get_price();
		return '<ins>' . ( ( is_numeric( $to ) ) ? wc_price( $to ) : $to ) . '</ins>';
	}
}
//add_filter( 'woocommerce_get_price_html', 'sr_price_html', 100, 2 );



/*-----------------------------------------------------------------------------------*/

/*	Mini Cart

/*-----------------------------------------------------------------------------------*/
function kona_woo_minicart_menu() {
	
	$addClass = 'cart-open ';
	if (is_cart()) { $addClass = ''; }
	if (WC()->cart->get_cart_contents_count() == '0') {
		$addClass .= 'cart-empty';
	}
	
	$cartIcon = get_option('_sr_shopminicarticon');
	$cartClass = '';
	if ($cartIcon && $cartIcon !== 'none') { $cartClass .= ' cart-withicon icon-'.$cartIcon; }
	
	?>
    <div class="header-cart <?php echo esc_attr($cartClass); ?>">
    	<a href="<?php echo wc_get_cart_url(); ?>" class="cart-amount <?php echo esc_attr($addClass); ?>">
            <span class="minicart-count"> <?php echo WC()->cart->get_cart_contents_count(); ?></span>
            
            <?php if ($cartIcon && $cartIcon !== 'none') { ?>
            <span class="icon">
            	<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 36.9 36.9">
            	<?php 
														   
				switch ($cartIcon) {
					case "bag1":
						echo '<path d="M30.4,11.2c-0.1-0.6-0.6-1.1-1.2-1.1h-4.9V7.8c0-3.2-2.6-5.9-5.9-5.9s-5.9,2.6-5.9,5.9v2.2H7.7 c-0.6,0-1.2,0.5-1.2,1.1l-2,22.1c0,0.3,0.1,0.7,0.3,1c0.2,0.3,0.6,0.4,0.9,0.4h25.5c0.4,0,0.7-0.1,0.9-0.4c0.2-0.3,0.4-0.6,0.3-1 L30.4,11.2z M15.1,7.8c0-1.9,1.5-3.4,3.4-3.4c1.9,0,3.4,1.5,3.4,3.4v2.2h-6.7V7.8z M7,32.1l1.8-19.6h3.8v2c0,0.7,0.6,1.2,1.2,1.2 s1.2-0.6,1.2-1.2v-2h6.7v2c0,0.7,0.6,1.2,1.2,1.2s1.2-0.6,1.2-1.2v-2h3.8l1.8,19.6H7z"/>';
						break;
					case "bag2":
						echo '<path d="M31.2,11.1h-6.9V8.8c0-3.2-2.6-5.9-5.9-5.9s-5.9,2.6-5.9,5.9v2.2H5.7c-0.7,0-1.2,0.6-1.2,1.2v20.1 c0,0.7,0.6,1.2,1.2,1.2h25.5c0.7,0,1.2-0.6,1.2-1.2V12.3C32.4,11.6,31.9,11.1,31.2,11.1z M15.1,8.8c0-1.9,1.5-3.4,3.4-3.4 c1.9,0,3.4,1.5,3.4,3.4v2.2h-6.7V8.8z M29.9,31.1h-23V13.6h23V31.1z"/';
						break;
					case "cart1":
						echo '<path class="st0" d="M29.8,26.5H9.4c-0.6,0-1.1-0.5-1.2-1.1l-2.8-19H2c-0.7,0-1.2-0.6-1.2-1.2S1.3,3.9,2,3.9h4.4
		C7,3.9,7.6,4.4,7.7,5l2.8,19h18.3l2.3-11H12.5c-0.7,0-1.2-0.6-1.2-1.2s0.6-1.2,1.2-1.2h20.1c0.4,0,0.7,0.2,1,0.5
		c0.2,0.3,0.3,0.7,0.3,1L31,25.5C30.9,26.1,30.4,26.5,29.8,26.5z"/>
<circle class="st0" cx="13.2" cy="30.4" r="3.1"/>
<circle class="st0" cx="25.3" cy="30.4" r="3.1"/>
';
						break;
				}
														   
				?>
				</svg>
			</span>
     		<?php } ?>
      	</a>
    </div>
	<?php
	}
	
function kona_woo_minicart_content($class = false) {
	if (get_option('_sr_shopminicartajax')) { $class .= ' ajax-cart'; }	
	?>
    <div class="menu-cart-content <?php echo esc_attr($class); ?>">
   		<?php woocommerce_mini_cart(); ?>
    </div>
	<?php
	}



/*-----------------------------------------------------------------------------------*/

/*	Wishlist Menu

/*-----------------------------------------------------------------------------------*/
function kona_woo_wishlist_menu() {
		
	?>
    <div class="header-wishlist">
    	<?php echo do_shortcode("[ti_wishlist_products_counter]"); ?>
    </div>
	<?php
	}
	


/*-----------------------------------------------------------------------------------*/

/*	ADD CART WRAPPER

/*-----------------------------------------------------------------------------------*/
function add_cart_wrapper_before() { echo '<div class="wrapper-medium">'; }
add_action('woocommerce_before_cart', 'add_cart_wrapper_before', 1);

function add_cart_wrapper_after() { echo '</div> <!-- close wrapper medium -->'; }
add_action('woocommerce_after_cart', 'add_cart_wrapper_after', 50);





/*-----------------------------------------------------------------------------------*/

/*	Custom Gallery field for variations

/*-----------------------------------------------------------------------------------*/
add_action( 'woocommerce_product_after_variable_attributes', 'woo_variable_fields', 10, 3 );
add_action( 'woocommerce_save_product_variation', 'save_variation_fields', 10, 2 );

function woo_variable_fields( $loop, $variation_data, $variation ) {

  echo '<div class="variation-custom-fields sr-style variation-gallery">';
    	
	echo '<div class="label">Variation Gallery</div>';
	echo '<div id="sortable-['.$loop.']" class="sortable-list">';
	echo '<textarea name="_sr_variation_gallery['.$loop.']" id="_sr_variation_gallery['.$loop.']" class="sortable-value" style="display:none;"> '.get_post_meta($variation->ID, '_sr_variation_gallery', true).'</textarea>';
	echo '<ul id="sortable-variation['.$loop.']" class="sortable-container sortable-media-options clear">';
	
	// --> important - output is &quot; back instead of " for json
	$json = str_replace('&quot;','"',get_post_meta($variation->ID, '_sr_variation_gallery', true));   
	$json = json_decode($json);
	$options = "size:select:normal,fullheight";
	if($json) {
	foreach($json->sortable as $j) {
		echo '<li>';
		echo '<input type="hidden" name="type" value="'.$j->type.'" class="to-json">';

		switch($j->type) {
			
			// image
			case 'image':
				echo '<input type="hidden" name="id" value="'.$j->id.'" class="to-json">';
				echo '<div class="image-preview">'.wp_get_attachment_image( $j->id, 'thumbnail' ).'</div>';

				// options
				$mediaOptions = explode("|",$options);

				if($options) {
					echo '<div class="options">';
					foreach($mediaOptions as $m) {
						$field = explode(":",$m);

						// option field value
						$fieldVal = json_encode($j);
						$fieldVal = json_decode($fieldVal,true);
						$fieldVal = $fieldVal[$field[0]];

						if($field[1] == 'textarea') {
							echo '<textarea name="'.esc_attr($field[0]).'" class="to-json" placeholder="'.esc_attr($field[0]).'"></textarea>';	
						} else if($field[1] === 'text') {
							echo '<input type="text" name="'.esc_attr($field[0]).'" placeholder="'.esc_attr($field[0]).'" class="to-json">';	
						} else if($field[1] === 'select') {
							echo '<select name="'.esc_attr($field[0]).'" class="to-json">';
							$fieldOptions = explode(",",$field[2]);
							foreach($fieldOptions as $f) {
								$selected = ""; if ($f == $fieldVal) { $selected = 'selected="selected"'; }
								echo '<option value="'.$f.'" '.$selected.'>'.$field[0].': '.$f.'</option>';
							}
							echo '</select>';
						}
					}
					echo '</div>';
				}

			break;

		} // END switch

		echo '<a href="#" class="delete-sortable">delete</a></li>';
	} // END foreach
	} // END if()
	

	echo '</ul>';
	echo '<a class="add-to-sortable-media-options add-sortable-button sr-button" data-type="image" data-options="'.$options.'">Add Image</a>';
	echo '</div>';
	
   
  echo "</div>"; 

}

/* Save new fields for variations */
function save_variation_fields( $variation_id, $i) {
    $sr_variation_gallery = stripslashes( $_POST['_sr_variation_gallery'][$i] );
	if ($sr_variation_gallery == ' ' || $sr_variation_gallery == '  ') { $sr_variation_gallery = ''; }
    update_post_meta( $variation_id, '_sr_variation_gallery', $sr_variation_gallery );
}

// Add New Variation Settings
add_filter( 'woocommerce_available_variation', 'load_variation_settings_fields' );
function load_variation_settings_fields( $variations ) {
	$variations['sr_variation_gallery'] = get_post_meta( $variations[ 'variation_id' ], '_sr_variation_gallery', true );
	return $variations;
}



/*-----------------------------------------------------------------------------------*/

/*	Additional thumbnail field(s)

/*-----------------------------------------------------------------------------------*/
add_filter( 'admin_post_thumbnail_html', 'kona_add_hover_image', 10, 2 ); //same as before
function kona_add_hover_image( $myhtml, $post_id ) {
	if ( get_post_type( get_the_ID() ) == 'product' ) {
		$hoverImage = "";
		if (get_post_meta( $post_id, '_sr_producthover', true )) { $hoverImage = get_post_meta( $post_id, '_sr_producthover', true ); }
		$removeClass = 'hide'; if ($hoverImage) { $removeClass = ''; }
		return $myhtml .= '
			<div class="sr-style">
				<div class="hover-image">
					<!--<div class="option_name">
						<label for="_sr_producthover">'.esc_html__("Hover image",'kona').'</label>
					</div>-->
					<div class="option_value">
						<input class="upload_image" type="hidden" name="_sr_producthover" id="_sr_producthover" value="'.esc_url($hoverImage).'" />
						<span class="preview_image"><img class="" src="'.esc_url($hoverImage).'" /></span>
						<input class="sr_remove_image_button sr-button button-remove '.esc_attr($removeClass).'" type="button" value="'.esc_attr__("Remove hover image",'kona').'" />
						<input class="sr_upload_image_button sr-button " type="button" value="'.esc_attr__("Choose hover image (optional)",'kona').'" />
					</div>
				</div>
			</div>
			';
	} else {
		return $myhtml;
	}
}

// function and action to save the new value to the post
function kona_add_hover_image_meta_save( $post_id ) {
    if( isset( $_POST[ '_sr_producthover' ] ) ) {
        update_post_meta( $post_id, '_sr_producthover', sanitize_text_field( $_POST[ '_sr_producthover' ] ) );
    }   
}
add_action( 'save_post', 'kona_add_hover_image_meta_save' );




/*-----------------------------------------------------------------------------------

	Custom Category fields

-----------------------------------------------------------------------------------*/
//Product Category - Add category
function sr_add_category_fields() {
    ?>
    <div class="form-field sr-style">
        <label><strong><?php _e( 'Title Image', 'kona' ); ?></strong></label>
		<small>Title images when visitor is visiting the category page.</small>
		<div class="option" style="padding: 0;">
			<div class="option_value">
				<input class="upload_image" type="hidden" name="product_cat_titleimage" id="product_cat_titleimage" value="<?php echo esc_url($titleImage); ?>" size="30" />
				<input class="sr_upload_image_button sr-button" type="button" value="Upload Image" />
				<input class="sr_remove_image_button sr-button button-remove hide" type="button" value="Remove Image" /><br />
				<span class="preview_image"><img class="product_cat_titleimage"  src="<?php echo esc_url( $image ); ?>" alt="preview image" /></span>
			</div>
		</div>
	</div>
    <?php
}

//Product Category - Edit category
function sr_edit_category_fields($term) {
	$titleImage = get_term_meta($term->term_id, 'product_cat_titleimage', true);
	$removeClass = 'hide';
	if ($titleImage) {
		$removeClass = '';
	}
    ?>
    <tr class="form-field sr-style">
		<th scope="row" valign="top"><label><strong><?php _e( 'Title Image', 'kona' ); ?></strong></label><br>
		<small>Title images when visitor is visiting the category page.</small>
		</th>
		<td>
			<div class="option" style="padding: 0;">
				<div class="option_value">
					<input class="upload_image" type="hidden" name="product_cat_titleimage" id="product_cat_titleimage" value="<?php echo esc_url($titleImage); ?>" size="30" />
					<input class="sr_upload_image_button sr-button" type="button" value="Upload Image" />
					<input class="sr_remove_image_button sr-button button-remove <?php echo esc_attr($removeClass); ?>" type="button" value="Remove Image" /><br />
					<span class="preview_image"><img class="product_cat_titleimage"  src="<?php echo esc_url( $titleImage ); ?>" alt="preview image" /></span>
				</div>
			</div>
		</td>
	</tr>
    <?php
}

add_action('product_cat_add_form_fields', 'sr_add_category_fields', 50, 50);
add_action('product_cat_edit_form_fields', 'sr_edit_category_fields', 50, 50);

// Save extra taxonomy fields callback function.
function sr_save_category_fields($term_id) {
    $product_cat_titleimage = filter_input(INPUT_POST, 'product_cat_titleimage');
    update_term_meta($term_id, 'product_cat_titleimage', $product_cat_titleimage);
}

add_action('edited_product_cat', 'sr_save_category_fields', 10, 1);
add_action('create_product_cat', 'sr_save_category_fields', 10, 1);



/*-----------------------------------------------------------------------------------

	Custom Button Style

-----------------------------------------------------------------------------------*/
if( !function_exists( 'kona_button_style' ) ) {
	function kona_button_style() {
		global $wpdb;
		
		$buttonStyle = '';
		if (get_option('_sr_shopsinglebutton')) { $buttonStyle = get_option('_sr_shopsinglebutton'); }
		
		/*if (in_the_loop()) {
			return 'style-2';
		}*/
		
		if (!$buttonStyle || $buttonStyle == 'simple') {
			return 'text-trans';
		} else if ($buttonStyle == 'full') {
			return '';
		} else if ($buttonStyle == 'customsimple') {
			return 'text-trans custom';
		} else if ($buttonStyle == 'customfull') {
			return 'custom';
		}
	}
}




/*-----------------------------------------------------------------------------------

	Sale Badge in percentage

-----------------------------------------------------------------------------------*/
if (get_option('_sr_shopgridsaleappearance') == 'percentage') {
	add_filter( 'woocommerce_sale_flash', 'add_percentage_to_sale_flash', 20 );
	function add_percentage_to_sale_flash( $html ) {
		global $product;
		//prnt_r($product);
		if ($product->is_type('simple')) { //if simple product
			$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 ).'%';
		} else { //if variable product
			$percentage = get_variable_sale_percentage( $product );
		}

		$output =' <span class="onsale">-'.$percentage.'</span>';
		return $output;
	}

	function get_variable_sale_percentage( $product ) {
		//get variables
		$variation_min_regular_price    = $product->get_variation_regular_price('min', true);
		$variation_max_regular_price    = $product->get_variation_regular_price('max', true);
		$variation_min_sale_price       = $product->get_variation_sale_price('min', true);
		$variation_max_sale_price       = $product->get_variation_sale_price('max', true);

		//get highest and lowest percentages
		$lower_percentage   = round( ( ( $variation_min_regular_price - $variation_min_sale_price ) / $variation_min_regular_price ) * 100 );
		$higher_percentage  = round( ( ( $variation_max_regular_price - $variation_max_sale_price ) / $variation_max_regular_price ) * 100 );

		//sort array
		$percentages = array($lower_percentage, $higher_percentage);
		sort($percentages);

		return $percentages[1].'%';
	}
}



/*-----------------------------------------------------------------------------------

	NEW + HOT Badge (on shop page) --- single page on sal-flash-template

-----------------------------------------------------------------------------------*/
if (get_option('_sr_shopgridshownew') || get_option('_sr_shopgridshowhot')) { 
	add_action( 'woocommerce_before_shop_loop_item_title', function() {
		echo '<span class="badges">';
	}, 1 );
	
	if (get_option('_sr_shopgridshownew')) {
		add_action( 'woocommerce_before_shop_loop_item_title', function() {
			$postdate      = get_the_time( 'Y-m-d' ); 			// Post date
			$postdatestamp = strtotime( $postdate );  			// Timestamped post date
			$newness       = get_option('_sr_shopgridnewdays'); // Newness in days
			if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) {
				echo '<span class="new-badge">' . esc_html__( 'New', 'kona' ) . '</span>';
			}
		}, 9 );
	}
		
	if (get_option('_sr_shopgridshowhot')) {
		add_action( 'woocommerce_before_shop_loop_item_title', function() {
			$prodID = get_the_ID();
			$prods = ','.get_option('_sr_shopgridhotprodcuts').','; 
			if (strpos($prods, ','.$prodID.',') !== false) {
				echo '<span class="hot-badge">' . esc_html__( 'Hot', 'kona' ) . '</span>';
			}
		}, 11 );
	}
	
	add_action( 'woocommerce_before_shop_loop_item_title', function() {
		echo '</span>';
	}, 15 );
}



remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
add_action( 'woocommerce_cart_is_empty', 'custom_empty_cart_message', 10 );

function custom_empty_cart_message() {
    $html  = '<div class="empty-cart sr-empty cart-empty">
		<div class="empty-icon">0</div>
		<div class="empty-main-text h3"><strong>'.__( 'Your cart is empty', 'kona' ).'</strong></div>
		<div class="empty-sub-text title-alt">'.__( "Looks like you haven't made your choice yet", 'kona' ).'</div>
		<p class="return-to-shop">
			<a class="button wc-backward" href="'.esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ).'">
				'.__( 'Return to shop', 'kona' ).'
			</a>
		</p>
	</div>';
    echo $html;
}



/*-----------------------------------------------------------------------------------

	ADD SKU search to default widget search (ajax search done in pagebuilder-frontend)

-----------------------------------------------------------------------------------*/
function kona_product_search_join( $join, $query ) {
	if ( ! $query->is_main_query() || is_admin() || ! is_search() || ! is_woocommerce() ) {
		return $join;
	}

	global $wpdb;

	$join .= " LEFT JOIN {$wpdb->postmeta} kona_post_meta ON {$wpdb->posts}.ID = kona_post_meta.post_id ";

	return $join;
}
add_filter( 'posts_join', 'kona_product_search_join', 10, 2 );

function kona_product_search_where( $where, $query ) {
	if ( ! $query->is_main_query() || is_admin() || ! is_search() || ! is_woocommerce() ) {
		return $where;
	}

	global $wpdb;

	$where = preg_replace(
		"/\(\s*{$wpdb->posts}.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
		"({$wpdb->posts}.post_title LIKE $1) OR (kona_post_meta.meta_key = '_sku' AND kona_post_meta.meta_value LIKE $1)", $where );

	return $where;
}
add_filter( 'posts_where', 'kona_product_search_where', 10, 2 );
?>