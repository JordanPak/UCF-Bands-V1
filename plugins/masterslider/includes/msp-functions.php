<?php 


/**
 * Get master slider markup for specific slider ID
 * 
 * @param  int      $id   the slider id
 * @return string   the slider markup
 */
if( ! function_exists('get_masterslider') ) {

    function get_masterslider( $id, $args = NULL ){

        // through an error if slider id is not valid number
    	if( ! is_numeric( $id ) ) 
        	return __( "Invalid slider id. Master Slider ID must be a valid number.", MSWP_TEXT_DOMAIN );

        // load masterslider script
        wp_enqueue_style ( 'masterslider-main');
        wp_enqueue_script( 'masterslider-core');

        
        // try to get cached copy of slider transient output
        if( ! MSWP_ENABLE_CACHE || false === ( $slider_output = msp_get_slider_transient( $id ) ) ) { // transient_masterslider_output, filter

            // if transient is not set or expired try to regnarate it and set transient again
            $ms_slider_shortcode = msp_get_ms_slider_shortcode_by_slider_id( $id );
            $slider_output = do_shortcode( $ms_slider_shortcode );
            msp_set_slider_transient( $id, $slider_output );
        }

        return apply_filters( 'masterslider_slider_content', $slider_output );
    }
}


/**
 * Displays master slider markup for specific slider ID
 * 
 * @param  int      $id   the slider id
 * @return void
 */
if( ! function_exists('masterslider') ) {

    function masterslider( $id, $args = NULL ){
        echo get_masterslider( $id, $args = NULL );
    }

}


/**
 * Convert panel data to ms_slider shortcode and return it
 * 
 * @param  string    $panel_data   a serialized string containing panel data object
 * @return string    ms_slider shortcode or empty string
 */
function msp_panel_data_2_ms_slider_shortcode( $panel_data ){
    if ( ! $panel_data ) 
        return '';

    $parser = msp_get_parser();
    $parser->set_data( $panel_data );
    $results = $parser->get_results();  

    // shortcode generation
    $sf = msp_get_shortcode_factory();
    $sf->set_data( $results );
    $shortcodes = $sf->get_ms_slider_shortcode();
    return $shortcodes;
}


/**
 * Convert panel data to ms_slider shortcode and return it
 * 
 * @param  int      $slider_id   The ID of the slider you'd like to get its shortcode
 * @return string   ms_slider shortcode or empty string
 */
function msp_get_ms_slider_shortcode_by_slider_id( $slider_id ){
    // get slider panel data from database
    global $mspdb;
    $panel_data = $mspdb->get_slider_field_val( $slider_id, 'params' );
    $shortcode = msp_panel_data_2_ms_slider_shortcode( $panel_data );
    return $shortcode;
}


/**
 * Takes a slider ID and returns slider's parsed data in an array
 * You can use this function to access slider data (setting, slides, layers, styles)
 *  
 * @param  int        $slider_id   The ID of the slider you'd like to get its parsed data
 * @return array      array containing slider's parsed data
 */
function get_masterslider_parsed_data( $slider_id ){
    // get slider panel data from database
    global $mspdb;
    $panel_data = $mspdb->get_slider_field_val( $slider_id, 'params' );

    if ( ! $panel_data ) 
        return array();

    $parser = msp_get_parser();
    $parser->set_data( $panel_data );
    return $parser->get_results();
}


// Load and init parser on demand
function msp_get_parser() {
    include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/classes/class-msp-parser.php' );
    
    global $msp_parser;
    if ( is_null( $msp_parser ) )
        $msp_parser = new MSP_Parser();
    
    return $msp_parser;
}


function msp_get_shortcode_factory () {
    include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/classes/class-msp-shortcode-factory.php' );
    
    global $mspsf;
    if ( is_null( $mspsf ) )
        $mspsf = new MSP_Shortcode_Factory();
    
    return $mspsf;
}


/**
 * Update custom_css and custom_fonts field in sliders table
 * @param int $slider_id the slider id that is going to be updated
 *                  
 * @return int|false The number of rows updated, or false on error.
 */
function msp_update_slider_custom_css_and_fonts( $slider_id ) {

    if( ! isset( $slider_id ) || ! is_numeric( $slider_id ) )
        return false;

    // get database tool
    global $mspdb;

    $slider_params = $mspdb->get_slider_field_val( $slider_id, 'params' );

    if( ! $slider_params )
        return false;

    // load and get parser and start parsing data
    $parser = msp_get_parser();
    $parser->set_data( $slider_params );
    
    // get required parsed data
    $slider_setting       = $parser->get_slider_setting();
    $slides               = $parser->get_slides();
    $slider_custom_styles = $parser->get_styles();

    $fields = array(
        'slides_num'    => count( $slides ),
        'custom_styles' => $slider_custom_styles,
        'custom_fonts'  => $slider_setting[ 'gfonts' ]
    );
    
    msp_save_custom_styles();

    $mspdb->update_slider( $slider_id, $fields );
}


/**
 * Set/update the value of a slider output transient.
 * 
 * @param  int   $slider_id     The slider id
 * @param  mixed $value         Slider transient output
 * @param  int   $cache_period  Time until expiration in hours, default 12
 * @return bool                 False if value was not set and true if value was set.
 */
function msp_set_slider_transient( $slider_id, $value, $cache_period = null ) {
    $cache_period = is_null( $cache_period ) ? MSWP_CACHE_PERIOD : $cache_period;
    return set_transient( 'masterslider_output_' . $slider_id , $value, (int)$cache_period * HOUR_IN_SECONDS );
}

/**
 * Get the value of a slider output transient.
 * 
 * @param  int     $slider_id     The slider id
 * @return mixed   Value of transient or False If the transient does not exist or does not have a value
 */
function msp_get_slider_transient( $slider_id ) {
    get_transient( 'masterslider_output_' . $slider_id );
}



//// is absolute url ///////////////////////////////////////////////////////////////////

function msp_is_absolute_url( $url ){
    return preg_match( "~^(?:f|ht)tps?://~i", $url );
}


//// finds out if the url contains upload directory path (true, if it's absolute url to internal file)
  
function msp_contains_upload_dir( $url ){
    $uploads_dir = wp_upload_dir();
    return strpos( $url, $uploads_dir['baseurl'] ) !== false;
}

//// create absolute url if the url is relative ////////////////////////////////////////

function msp_the_absolute_media_url( $url ){
    echo msp_get_the_absolute_media_url( $url );
}


    function msp_get_the_absolute_media_url( $url ){
        if( !isset( $url ) || empty( $url ) )    return '';
        
        if( msp_is_absolute_url( $url ) || msp_contains_upload_dir( $url ) ) return $url;
        
        $uploads = wp_upload_dir();
        return $uploads['baseurl'] . $url;
    }


//// create relative url if it's url for internal uploaded file ////////////////////////

function msp_the_relative_media_url( $url ){
    echo msp_get_the_relative_media_url( $url );
}

    
    function msp_get_the_relative_media_url($url){
        if( ! isset( $url ) || empty( $url ) )     return '';
        
        // if it's not internal absolute url 
        if( ! msp_contains_upload_dir( $url ) ) return $url;
        
        $uploads_dir = wp_upload_dir();
        return str_replace( $uploads_dir['baseurl'], '', $url );
    }


/*-----------------------------------------------------------------------------------*/
/*  Custom functions for resizing images 
/*-----------------------------------------------------------------------------------*/


// get resized image by image src ////////////////////////////////////////////////////

function msp_the_resized_image( $img_url = "", $width = null , $height = null, $crop = null , $quality = 100 ) {
    echo msp_get_the_resized_image( $img_url , $width , $height , $crop , $quality );
}
    
    function msp_get_the_resized_image( $img_url = "", $width = null , $height = null, $crop = null , $quality = 100 ) {
        return '<img src="'.msp_aq_resize( $img_url, $width, $height, $crop, $quality ).'" alt="" />';
    }

        function msp_get_the_resized_image_src( $img_url = "", $width = null , $height = null, $crop = null , $quality = 100 ) {
            $resized_img_url = msp_aq_resize( $img_url, $width, $height, $crop, $quality );
            if( empty( $resized_img_url ) ) 
                $resized_img_url = $img_url;
            return apply_filters( 'msp_get_the_resized_image_src', $resized_img_url, $img_url );
        }


// get resized image by attachment id ////////////////////////////////////////////////

// echo resized image tag
function msp_the_resized_attachment( $attach_id = null, $width = null , $height = null, $crop = null , $quality = 100 ) {
    echo msp_get_the_resized_attachment( $attach_id, $width , $height, $crop, $quality );
}

    // return resized image tag
    function msp_get_the_resized_attachment( $attach_id = null, $width = null , $height = null, $crop = null , $quality = 100 ) {
        $image_src = msp_get_the_resized_attachment_src( $attach_id, $width , $height, $crop, $quality );
        
        return $image_src ? '<img src="'.$image_src.'" alt="" />': '';
    }

        function msp_get_the_resized_attachment_src( $attach_id = null, $width = null , $height = null, $crop = null , $quality = 100 ) {
            if( is_null( $attach_id ) ) return '';
            
            $img_url = wp_get_attachment_url( $attach_id ,'full'); //get img URL                     
            return ! empty( $img_url ) ? msp_aq_resize( $img_url, $width, $height, $crop, $quality ) : false;
        }

// get resized image featured by post id //////////////////////////////////////////////


// echo resized image tag
function msp_the_post_thumbnail( $post_id = null, $width = null , $height = null, $crop = null , $quality = 100 ) {
    echo msp_get_the_post_thumbnail( $post_id, $width , $height, $crop, $quality);
}

    if( ! function_exists( 'msp_get_the_post_thumbnail' ) ){
        
        // return resized image tag
        function msp_get_the_post_thumbnail( $post_id = null, $width = null , $height = null, $crop = null , $quality = 100 ) {
            $image_src = msp_get_the_post_thumbnail_src( $post_id, $width , $height, $crop, $quality);
            return $image_src ? '<img src="'.$image_src.'" alt="" />' : '';
        }
    
    }

        if( ! function_exists( 'msp_get_the_post_thumbnail_src' ) ){
        
            function msp_get_the_post_thumbnail_src( $post_id = null, $width = null , $height = null, $crop = null , $quality = 100 ) {
                $post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
                $post_thumbnail_id = get_post_thumbnail_id( $post_id );
                
                $img_url = wp_get_attachment_url( $post_thumbnail_id, 'full' ); //get img URL
                
                $resized_img = $post_thumbnail_id ? aq_resize( $img_url, $width, $height, $crop, $quality ) : false;
                return apply_filters( 'msp_get_the_post_thumbnail_src', $resized_img, $img_url, $width, $height, $crop, $quality );
            }
        
        }

        if( ! function_exists( 'msp_get_the_post_thumbnail_full_src' ) ){
        
            function msp_get_the_post_thumbnail_full_src( $post_id = null ) {
                $post_id = is_null( $post_id ) ? get_the_ID() : $post_id;
                $post_thumbnail_id = get_post_thumbnail_id( $post_id );
                
                return wp_get_attachment_url( $post_thumbnail_id, 'full' ); //get img URL
            }
        
        }

        if( ! function_exists( 'msp_get_auto_post_thumbnail_src' ) ){
        
            function msp_get_auto_post_thumbnail_src( $post_id = null, $image_from = 'auto' ) {

                $post = get_post( $post_id );
                $img_src = '';

                if( ! isset( $post ) ) return '';

				if ( 'auto' == $image_from ) {
					$img_src = has_post_thumbnail( $post->ID ) ? msp_get_the_post_thumbnail_full_src( $post->ID ) : '';

					if( empty( $img_src ) ) {
						$content   = get_the_content();
						$img_src = msp_get_first_image_src_from_content( $content );
					}

				} elseif( 'featured' == $image_from ) {
					$img_src = has_post_thumbnail( $post->ID ) ? msp_get_the_post_thumbnail_full_src( $post->ID ) : '';

				} elseif ( 'first' == $image_from ) {

					$content = get_the_content();
					$img_src = msp_get_first_image_src_from_content( $content );
				}
                
                return $img_src;
            }
        
        }

///// extract image from content ////////////////////////////////////////////////////

function msp_get_first_image_from_content( $content ){
    $images = msp_get_content_images( $content );
    return ( $images && count( $images[0]) ) ? $images[0][0] : '';
}

function msp_get_first_image_src_from_content( $content ){
    $images = msp_get_content_images( $content );
    return ( $images && count( $images[1]) ) ? $images[1][0] : '';
}

    if( ! function_exists( 'msp_get_content_images' ) ){
        
        function msp_get_content_images( $content ){
            preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $matches );
            return isset( $matches ) && count( $matches[0] ) ? $matches : false;
        }
    
    }

/*-----------------------------------------------------------------------------------*/


/**
 * Get list of created slider IDs and names in an array
 * 
 * @param  bool    $id_as_key   If <code>true</code> returns slider ID as array key and slider name as value , reverse on <code>false</code>
 * @param  int     $limit       Maximum number of sliders to return - 0 means no limit
 * @param  int     $offset      The offset of the first row to return
 * @param  string  $orderby     The field name to order results by
 * @param  string  $sort        The sort type. 'DESC' or 'DESC'
 * 
 * @return array   An array containing sliders ID as array key and slider name as value
 *
 * @example   $id_as_key = true :
 *            array(
 *                '12' => 'Slider sample title 1', 
 *                '13' => 'Slider sample title 2'
 *            )
 *
 *            $id_as_key = false :
 *            array(
 *                'Slider sample title 1' => '12', 
 *                'Slider sample title 2' => '13' 
 *            )
 */
function get_masterslider_names( $id_as_key = true, $limit = 0, $offset  = 0, $orderby = 'ID', $sort = 'DESC' ){
    global $mspdb;

    // replace 0 with max numbers od records you need
    if ( $sliders_data = $mspdb->get_sliders_list( $limit = 0, $offset  = 0, $orderby = 'ID', $sort = 'DESC' ) ) {
        // stores sliders 'ID' and 'title'
        $sliders_name_list = array();

        foreach ( $sliders_data as $slider_data ) {
            if( $id_as_key )
                $sliders_name_list[ $slider_data['ID'] ]    = $slider_data['title'];
            else
                $sliders_name_list[ $slider_data['title'] ] = $slider_data['ID'];
        }

        return $sliders_name_list;
    }

    return array();
}


/**
 * Get an array containing row results (unserialized) from sliders table (with all slider table fields)
 * 
 * @param  int $limit       Maximum number of records to return
 * @param  int $offset      The offset of the first row to return
 * @param  string $orderby  The field name to order results by
 * @param  string $sort     The sort type. 'DESC' or 'ASC'
 * @param  string $where    The sql filter to get results by
 * @return array            Slider data in array
 */
function get_mastersliders( $limit = 0, $offset = 0, $orderby = 'ID', $sort = 'DESC', $where = "status='published'" ) {
    global $mspdb;

    $sliders_array = $mspdb->get_sliders( $limit, $offset, $orderby, $sort, $where );
    return is_null( $sliders_array ) ? array() : $sliders_array;
}


/**
 * Get option value
 * 
 * @param   string  $option_name a unique name for option
 * @param   string  $default_value  a value to return by function if option_value not found
 * @return  string  option_value or default_value
 */
function msp_get_option( $option_name, $default_value = '' ) {
    global $mspdb;
    return $mspdb->get_option( $option_name, $default_value );
}


/**
 * Update option value in options table, if option_name does not exist then insert new option
 * 
 * @param   string $option_name a unique name for option
 * @param   string $option_value the option value
 *                  
 * @return int|false ID number for new inserted row or false if the option can not be updated.
 */
function msp_update_option( $option_name, $option_value = '' ) {
    global $mspdb;
    return $mspdb->update_option( $option_name, $option_value );
}


/**
 * Remove a specific option name from options table
 * 
 * @param   string $option_name a unique name for option
 * @return bool True, if option is successfully deleted. False on failure.
 */
function msp_delete_option( $option_name ) {
    global $mspdb;
    return $mspdb->delete_option( $option_name );
}


/**
 * Get the value of a settings field
 *
 * @param string  $option  settings field name
 * @param string  $section the section name this field belongs to
 * @param string  $default default text if it's not found
 * @return string
 */
function msp_get_setting( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

/*-----------------------------------------------------------------------------------*/
/*  Get trimmed string
/*-----------------------------------------------------------------------------------*/

function msp_the_trimmed_string($string, $max_length = 1000, $more = " ..."){
    echo msp_get_trimmed_string( $string, $max_length, $more );
}
    
    if(!function_exists("msp_get_trimmed_string")){
        function msp_get_trimmed_string( $string, $max_length = 1000, $more = " ..." ){
            return function_exists("mb_strimwidth")?mb_strimwidth( $string, 0, $max_length, $more ):substr($string, 0, $max_length).$more;
        }
    }

/*-----------------------------------------------------------------------------------*/
/*  Shortcode enabled excerpts trimmed by character length
/*-----------------------------------------------------------------------------------*/

function msp_the_trim_excerpt( $post_id = null, $char_length = null, $exclude_strip_shortcode_tags = null ){
    echo msp_get_the_trim_excerpt( $post_id, $char_length, $exclude_strip_shortcode_tags );
}

    if( ! function_exists("msp_get_the_trim_excerpt") ){
        
        // make shortcodes executable in excerpt
        function msp_get_the_trim_excerpt( $post_id = null, $char_length = null, $exclude_strip_shortcode_tags = null ) {
            $post = get_post( $post_id );
            if( ! isset( $post ) ) return "";
            
    
            $excerpt = $post->post_content;
            $raw_excerpt = $excerpt;
            $excerpt = apply_filters( 'the_content', $excerpt );
            // If char length is defined use it, otherwise use default char length
            $char_length  = empty( $char_length ) ? apply_filters( 'masterslider_excerpt_char_length', 250 ) : $char_length;
            $excerpt_more = apply_filters('excerpt_more', ' ...');
            // Clean post content
            $excerpt = strip_tags( msp_strip_shortcodes( $excerpt, $exclude_strip_shortcode_tags ) );
            $text = msp_get_trimmed_string( $excerpt, $char_length, $excerpt_more );

            return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
        }
        
    }

/*-----------------------------------------------------------------------------------*/
/*  Remove just shortcode tags from the given content but remain content of shortcodes
/*-----------------------------------------------------------------------------------*/

function msp_strip_shortcodes($content, $exclude_strip_shortcode_tags = null) {
    if(!$content) return $content;
    
    if(!$exclude_strip_shortcode_tags)
        $exclude_strip_shortcode_tags = msp_exclude_strip_shortcode_tags();
    
    if( empty($exclude_strip_shortcode_tags) || !is_array($exclude_strip_shortcode_tags) )
        return preg_replace('/\[[^\]]*\]/', '', $content); //preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', $content);
    
    $exclude_codes = join('|', $exclude_strip_shortcode_tags);
    return preg_replace("~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $content);
}


/*-----------------------------------------------------------------------------------*/
/*  The list of shortcode tags that should not be removed in msp_strip_shortcodes
/*-----------------------------------------------------------------------------------*/

function msp_exclude_strip_shortcode_tags(){
    return apply_filters( 'msp_exclude_strip_shortcode_tags', array() );
}


function msp_get_custom_post_types(){
	$custom_post_types = get_post_types( array( '_builtin' => false ), 'objects' );
	return apply_filters( 'masterslider_get_custom_post_types', $custom_post_types );
}

function msp_get_post_slider_class() {
    include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/classes/class-msp-post-sliders.php' );
    
    global $msp_post_slider;
    if ( is_null( $msp_post_slider ) )
        $msp_post_slider = new MSP_Post_Slider();
    
    return $msp_post_slider;
}

function msp_is_plugin_active( $plugin_basename ){
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    return is_plugin_active( $plugin_basename );
}

function msp_get_wc_slider_class() {
    include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/classes/class-msp-wc-product-slider.php' );
    
    global $msp_wc_slider;
    if ( is_null( $msp_wc_slider ) )
        $msp_wc_slider = new MSP_WC_Product_Slider();
    
    return $msp_wc_slider;
}


function msp_get_template_tag_value( $tag_name, $post = null, $args = null ){
	$post  = get_post( $post );
	$value = '{{' . $tag_name . '}}';
	
	switch ( $tag_name ) {

		case 'title':
			$value = $post->post_title;
			break;

		case 'content':
			$value = $post->post_content;
			break;

		case 'excerpt':
			$value = $post->post_excerpt;
			if ( empty( $value ) ) {
				$excerpt_length = isset( $args['excerpt_length'] ) ? (int)$args['excerpt_length'] : 80;
				$value = msp_get_the_trim_excerpt( $value, $excerpt_length );
			}
			break;

		case 'permalink':
			$value = $post->guid;
			break;

		case 'author':
			$value = get_the_author_meta( 'display_name', (int)$post->post_author );
			break;

		case 'post_id':
			$value = $post->ID;
			break;

		case 'image':
			$value = msp_get_auto_post_thumbnail_src( $post, 'featured' );

			if( ! empty( $value ) )
				$value = sprintf( '<img src="%s" alt="%s" />', $value, $post->post_title );
			break;

		case 'image-url':
        case 'slide-image-url':
            $value = msp_get_auto_post_thumbnail_src( $post, 'auto' );
            break;

		case 'year':
			$value = strtotime( $post->post_date );
			$value = date_i18n( 'Y', $value );
			break;

		case 'daynum':
			$value = strtotime( $post->post_date );
			$value = date_i18n( 'j', $value );
			break;

		case 'day':
			$value = strtotime( $post->post_date );
			$value = date_i18n( 'l', $value );
			break;

		case 'monthnum':
			$value = strtotime( $post->post_date );
			$value = date_i18n( 'm', $value );
			break;

		case 'month':
			$value = strtotime( $post->post_date );
			$value = date_i18n( 'F', $value );
			break;

		case 'time':
			$value = strtotime( $post->post_date );
			$value = date_i18n( 'g:i A', $value );
			break;

		case 'date-published':
			$value = $post->post_date;
			break;

		case 'date-modified':
			$value = $post->post_modified;
			break;

		case 'commentnum':
			$value = $post->comment_count;
			break;

		case 'wc_price':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = wc_format_decimal( $product->get_price(), 2 );
			break;

		case 'wc_regular_price':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = wc_format_decimal( $product->get_regular_price(), 2 );
			break;

		case 'wc_sale_price':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = $product->get_sale_price() ? wc_format_decimal( $product->get_sale_price(), 2 ) : '';
			break;

		case 'wc_stock_status':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = $product->is_in_stock() ? __( 'In Stock', MSWP_TEXT_DOMAIN ) : __( 'Out of Stock', MSWP_TEXT_DOMAIN );
			break;

		case 'wc_stock_quantity':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = (int) $product->get_stock_quantity();
			break;

		case 'wc_weight':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = $product->get_weight() ? wc_format_decimal( $product->get_weight(), 2 ) : '';
			break;

		case 'wc_product_cats':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = wp_get_post_terms( $product->id, 'product_cat', array( 'fields' => 'names' ) );
			break;

		case 'wc_product_tags':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = wp_get_post_terms( $product->id, 'product_tag', array( 'fields' => 'names' ) );
			break;

		case 'wc_total_sales':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = metadata_exists( 'post', $product->id, 'total_sales' ) ? (int) get_post_meta( $product->id, 'total_sales', true ) : 0;
			break;

		case 'wc_average_rating':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = wc_format_decimal( $product->get_average_rating(), 2 );
			break;

		case 'wc_rating_count':
			if ( ! msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) break;
			$product = get_product( $post );
			$value = (int) $product->get_rating_count();
			break;

		default:
            if( metadata_exists( 'post', $post->ID, $tag_name ) ){
                $value = get_post_meta(  $post->ID, $tag_name, true );
            }
			break;
	}

	return apply_filters( 'masterslider_get_template_tag_value', $value, $tag_name, $post, $args );
}


function msp_maybe_base64_decode ( $data ) {
    $decoded_data = base64_decode( $data );
    return base64_encode( $decoded_data ) === $data ? $decoded_data : $data;
}


function msp_maybe_base64_encode ( $data ) {
    $encoded_data = base64_encode( $data );
    return base64_decode( $encoded_data ) === $data ? $encoded_data : $data;
}


function msp_escape_tag( $tag_name ){
    return tag_escape( $tag_name ); 
}





function msp_is_true($value) {
	return strtolower($value) === 'true' ? 'true' : 'false';
}


function msp_is_true_e($value) {
	echo msp_is_true($value);
}


function msp_is_key_true( $array, $key, $default = 'true' ) {
    if( isset( $array[ $key ] ) ) {
        return $array[ $key ] ? 'true' : 'false';
    } else {
        return $default;
    }
}
