<?php
   /*
   Plugin Name: UCFBands Custom
   Plugin URI: http://UCFBands.com/
   Description: Contains functions for CPTs and Meta Boxes?
   Version: 1.0
   Author: JpakMedia
   Author URI: http://JpakMedia.com/
   License: GPL2
   */



//---------------------------//
// REGISTER CUSTOM NAV MENUS //
//---------------------------//
function ucfbands_section_menus() {

	$locations = array(
		//'Section Menu' => __( 'Section Menu', 'text_domain' )
		'Wind Ensemble' => __( 'Wind Ensemble', 'text_domain' ),
		'Symphonic Band' => __( 'Symphonic Band', 'text_domain' ),
		'Concert Band' => __( 'Concert Band', 'text_domain' ),
		'Marching Knights ' => __( 'Marching Knights', 'text_domain' ),
		'Jammin\' Knights' => __( 'Jammin\' Knights', 'text_domain' ),
	);
	register_nav_menus( $locations );

}

// Hook into the 'init' action
add_action( 'init', 'ucfbands_section_menus' );




//-----------------------//
// BASIC BLOCK SHORTCODE //
//-----------------------//
function shortcode_block( $atts , $content = null ) {

	// ATTRIBUTES //
	extract( shortcode_atts(
		array(
			'featured' => '',
			'width'	   => '4',
			'title'   => '',
			'icon'	   => '',
		), $atts )
	);
	
	
	// BLOCK CLASS //
	if( $featured == 'yes' )
		$block_class = 'block block-featured';
		
	else
		$block_class = 'block';
	
	
	// ICON (Favicon //
	if( $icon != '' )
		$icon_output = '<i class="fa ' . $icon . '"></i> ';
	
	else
		$icon_output = '';
	
	
	// TITLE / HEADER //
	if( $title == '' )
		$header_output = '';
		
	else
		$header_output = '<h2>' . $icon_output . $title . '</h2>';
	

	// RETURN CODE
	return '
		
		<div class="col-lg-' . $width . '">
			<div class="' . $block_class . '">
			
				' . $header_output . '
				
				' . do_shortcode($content) . '
				
			</div>
		</div>
				
	';
}
add_shortcode( 'block', 'shortcode_block' );


//---------------//
// ROW SHORTCODE //
//---------------//
function shortcode_row( $atts , $content = null ) {

	// Code
	return '
		<div class="row">
			
			' . do_shortcode($content) . '
			
		</div>
	';
}
add_shortcode( 'row', 'shortcode_row' );


//------------------//
// COLUMN SHORTCODE //
//------------------//
function shortcode_col( $atts , $content = null ) {

	// ATTRIBUTES //
	extract( shortcode_atts(
		array(
			'width'	   => '3',
		), $atts )
	);
	

	// RETURN CODE
	return '
		<div class="col-lg-' . $width . '">
			
			' . do_shortcode($content) . '
			
		</div>
	';
}
add_shortcode( 'col', 'shortcode_col' );




//--------------------//
// REGISTER STAFF CPT //
//--------------------//
function ucfbands_staff() {

	$labels = array(
		'name'                => _x( 'Staff Members', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Staff Member', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Staff', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Staff', 'text_domain' ),
		'view_item'           => __( 'View Staff Member', 'text_domain' ),
		'add_new_item'        => __( 'Add New Staff Member', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Staff Member', 'text_domain' ),
		'update_item'         => __( 'Update Staff Member', 'text_domain' ),
		'search_items'        => __( 'Search Staff', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                => 'staff',
		'with_front'          => true,
		'pages'               => false,
		'feeds'               => false,
	);
	$args = array(
		'label'               => __( 'ucfbands_staff', 'text_domain' ),
		'description'         => __( 'UCF Bands Staff or Faculty Member', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions'),
		'taxonomies'          => array(''),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-id-alt',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'ucfbands_staff', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ucfbands_staff', 0 );



//---------------------------//
// REGISTER ANNOUNCEMENT CPT //
//---------------------------//
function ucfbands_announcement() {

	$labels = array(
		'name'                => _x( 'Announcements', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Announcement', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Announcements', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Announcements', 'text_domain' ),
		'view_item'           => __( 'View Announcement', 'text_domain' ),
		'add_new_item'        => __( 'Add New Announcement', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Announcement', 'text_domain' ),
		'update_item'         => __( 'Update Announcemen', 'text_domain' ),
		'search_items'        => __( 'Search Announcements', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                => 'announcements',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => false,
	);
	$args = array(
		'label'               => __( 'ucfbands_announcement', 'text_domain' ),
		'description'         => __( 'Announcement that can be placed on the home page and optional other pages', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'revisions'),
		'taxonomies'          => array( 'category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-quote',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'announcement', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ucfbands_announcement', 0 );



//-------------------------//
// REGISTER UPCOMING EVENT //
//-------------------------//
function ucfbands_event() {

	$labels = array(
		'name'                => _x( 'Upcoming Events', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Upcoming Event', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Upcoming Events', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Events', 'text_domain' ),
		'view_item'           => __( 'View Event', 'text_domain' ),
		'add_new_item'        => __( 'Add New Event', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Event', 'text_domain' ),
		'update_item'         => __( 'Update Event', 'text_domain' ),
		'search_items'        => __( 'Search Events', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                => 'event',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => false,
	);
	$args = array(
		'label'               => __( 'ucfbands_event', 'text_domain' ),
		'description'         => __( 'UCF Bands Upcoming Event', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', ),
		'taxonomies'          => array( 'category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-calendar',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'event', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ucfbands_event', 0 );




//-------------------//
// CUSTOM META BOXES //
//-------------------//
function be_sample_metaboxes( $meta_boxes ) {
    
	// Prefix for all fields
	$prefix = '_ucfbands_';
    
	
	//-- STAFF CPT --//
	$meta_boxes['ucfbands_staff'] = array(
        'id' => 'ucfbands_staff',
        'title' => 'Staff Attributes',
        'pages' => array('ucfbands_staff'), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
			array(
				'name'    => 'Faculty Member?',
				'id'      => $prefix . 'staff_is_faculty',
				'type'    => 'radio',
				'options' => array(
					'not_faculty' => __( 'Is NOT Faculty', 'cmb' ),
					'is_faculty'   => __( 'Is Faculty', 'cmb' ),
				),
			),
            array(
                'name' => 'Position / Job',
                'desc' => 'What is he/she? A band director?',
                'id' => $prefix . 'staff_position',
                'type' => 'text_medium'
            ),
			array(
				'name' => 'Email Address',
				'desc' => '@ucf.edu Email Address',
				'default' => '@ucf.edu',
				'id' => $prefix . 'staff_email',
				'type' => 'text_email'
			),
			array(
				'name' => 'Phone Number',
				'desc' => 'UCF VOIP Number',
				'default' => '(407) 823-',
				'id' => $prefix . 'staff_phone',
				'type' => 'text_small'
			),
        ),
    );
	
	
	//-- EVENT CPT --//
    $meta_boxes['ucfbands_event'] = array(
        'id' => 'ucfbands_event',
        'title' => 'Event Details',
        'pages' => array('event'), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
			array(
				'name' => 'Date',
				'desc' => "<b>Required:</b> The event's date, not the current date.",
				'id' => $prefix . 'event_date',
				'type' => 'text_date'
			),
			array(
				'name' => 'Time',
                'desc' => 'Leave empty for "TBA"',
				'id' => $prefix . 'event_time',
				'type' => 'text_time'
				// 'time_format' => 'h:i:s A',
			),
            array(
                'name' => 'Location/Venue',
                'desc' => 'Leave empty for "TBA"',
                'id' => $prefix . 'position',
                'type' => 'text_medium'
            ),
			array(
				'name'    => 'Icon',
				'id'      => $prefix . 'event_icon',
				'type'    => 'radio',
				'options' => array(
					'fa-calendar'	=> __( 'Calendar', 'cmb' ),
					'fa-music'   	=> __( 'Music', 'cmb' ),
					'fa-coffee'		=> __( 'Coffee', 'cmb' )
				),
			),			
        ),
    );


    return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'be_sample_metaboxes' );



//---------------------//
// INITALIZE METABOXES //
//---------------------//
add_action( 'init', 'be_initialize_cmb_meta_boxes', 9999 );
function be_initialize_cmb_meta_boxes() {
    if ( !class_exists( 'cmb_Meta_Box' ) ) {
        require_once( 'lib/metabox/init.php' );
    }
}