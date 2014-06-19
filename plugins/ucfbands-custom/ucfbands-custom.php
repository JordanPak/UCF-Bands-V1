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


//----------------//
// STAFF META BOX //
//----------------//
function be_sample_metaboxes( $meta_boxes ) {
    $prefix = '_ucfbands_staff_'; // Prefix for all fields
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
				'id'      => $prefix . 'is_faculty',
				'type'    => 'radio',
				'options' => array(
					'not_faculty' => __( 'Is NOT Faculty', 'cmb' ),
					'is_faculty'   => __( 'Is Faculty', 'cmb' ),
				),
			),
            array(
                'name' => 'Position / Job',
                'desc' => 'What is he/she? A band director?',
                'id' => $prefix . 'position',
                'type' => 'text_medium'
            ),
			array(
				'name' => 'Email Address',
				'desc' => '@ucf.edu Email Address',
				'default' => '@ucf.edu',
				'id' => $prefix . 'email',
				'type' => 'text_email'
			),
			array(
				'name' => 'Phone Number',
				'desc' => 'UCF VOIP Number',
				'default' => '(407) 823-',
				'id' => $prefix . 'phone',
				'type' => 'text_small'
			),
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'be_sample_metaboxes' );



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
		'taxonomies'          => array( 'category', 'post_tag' ),
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



//-----------------------//
// ANNOUNCEMENT META BOX //
//-----------------------//
/*function be_sample_metaboxes( $meta_boxes ) {
    $prefix = '_ucfbands_announcement_'; // Prefix for all fields
    $meta_boxes['ucfbands_announcement'] = array(
        'id' => 'ucfbands_announcement',
        'title' => 'Announcement Details',
        'pages' => array('ucfbands_announcement'), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
        ),
    );

    return $meta_boxes;
}
add_filter( 'cmb_meta_boxes', 'be_sample_metaboxes' );
*/



//---------------------//
// INITALIZE METABOXES //
//---------------------//
add_action( 'init', 'be_initialize_cmb_meta_boxes', 9999 );
function be_initialize_cmb_meta_boxes() {
    if ( !class_exists( 'cmb_Meta_Box' ) ) {
        require_once( 'lib/metabox/init.php' );
    }
}