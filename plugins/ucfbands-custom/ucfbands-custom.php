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
		'wind-ensemble' 	=> __( 'Wind Ensemble', 'text_domain' ),
		'symphonic-band' 	=> __( 'Symphonic Band', 'text_domain' ),
		'concert-band'	 	=> __( 'Concert Band', 'text_domain' ),
		'marching-knights' 	=> __( 'Marching Knights', 'text_domain' ),
		'jammin-knights' 	=> __( 'Jammin Knights', 'text_domain' ),
		'mk-armory' 		=> __( 'MK Armory', 'text_domain' )
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



//---------------------------//
// UPCOMING EVENTS SHORTCODE //
//---------------------------//
function shortcode_events( $atts ) {

	// ATTRIBUTES //
	extract( shortcode_atts(
		array(
			'ensemble'	  => 'all-ensembles',
			'archive'	  => 'no',
			'num'		  => 3,
			'width'		  => 4,
			'title'		  => 'Upcoming Events',
			'block'		  => 'yes'
		), $atts )
	);
	
	
	// ARCHIVE: Set num to -1 for unlimited results
	if( $archive == 'yes' )
		$num = -1;

	
	// ARCHIVE: Put UL in Row
	if( $archive == 'yes' )
		$return_string = '<div class="row" style="margin-top: 15px;"><ul class="timeline">';	
	
	

	// NOT Archive: Put UL in column & add title + button
	else
	{
		
		if( $block == 'yes' )
			$return_string = '<div class="col-lg-' . $width . '">
				<h2><i class="fa fa-calendar"></i> ' . $title . ' <a class="btn btn-default btn-xs" href="http://ucfbands.com/?page_id=812">View All</a></h2>
				<ul class="timeline">';
				
		else
			$return_string = '
				<h2><i class="fa fa-calendar"></i> ' . $title . ' <a class="btn btn-default btn-xs" href="http://ucfbands.com/?page_id=812">View All</a></h2>
				<ul class="timeline" style="padding-top: 10px;">';
	}



/* BACKUP FROM TEMPORARY
	// NOT Archive: Put UL in column & add title + button
	else
	{
		
		if( $block == 'yes' )
			$return_string = '<div class="col-lg-' . $width . '">
				<h2><i class="fa fa-calendar"></i> ' . $title . ' <a class="btn btn-default btn-xs" href="' . get_site_url() . '/events">View All</a></h2>
				<ul class="timeline">';
				
		else
			$return_string = '
				<h2><i class="fa fa-calendar"></i> ' . $title . ' <a class="btn btn-default btn-xs" href="' . get_site_url() . '/events">View All</a></h2>
				<ul class="timeline" style="padding-top: 10px;">';
	}
*/


	// Time Testing
	//echo 'MySQL Time: ' . current_time( 'mysql' );	
	//date_default_timezone_set('America/New_York');
	//echo '<br>' . date_default_timezone_get();
	//echo ': ' . date('Y-m-d H:i:s');



	// Preparing the query for events
	// Only get events that haven't passed!
	$meta_quer_args = array(
		'relation'	=>	'AND',
		array(
			'key'		=>	'_ucfbands_event_end_datetime_timestamp',
			'value'		=>	time(),
			'compare'	=>	'>'
		)
	);

		
	// Query Options
	$events_selection = array(
		'post_type'		=> 'events',
		'category_name'	=> $ensemble,
		'fields' 		=> 'ids', // This is so only the ID is returned instead of the WHOLE post object (Performance)
		'meta_key'		=> '_ucfbands_event_start_datetime_timestamp',
		'orderby' 		=> 'meta_value_num',
		'order' 		=> 'ASC',
		'post_count'	=> $num,
		'posts_per_page'=> $num,
		'meta_query'	=> $meta_quer_args
	);
	
	
	// GET POSTS //
	$events = new WP_Query( $events_selection );
			
	
	// If there are no results
	if(($events->have_posts()) == false)
	{
		$return_string .= '<li><div class="timeline-badge primary"><i class="fa fa-calendar"></i></div><div class="timeline-panel"><div class="timeline-heading">There are no upcoming events found for this ensemble.</div></div></li>';
	}



	// Get Posts associated with events        
	$events = $events->get_posts();

	
                                                                
	// LOOP THROUGH THE IDS OF EACH EVENT
	foreach($events as $event)
	{
		
		// GET POST CONTENT //
		$content_post = get_post($event);
		$content = $content_post->post_content;
		
		
		
		// GET POST META //
		$event_icon_bgcolor		= get_post_meta( $event, '_ucfbands_event_icon_bgcolor', true );
		$event_venue			= get_post_meta( $event, '_ucfbands_event_venue', true );
		$event_start_timestamp  = get_post_meta( $event, '_ucfbands_event_start_datetime_timestamp', true );
		$event_end_timestamp	= get_post_meta( $event, '_ucfbands_event_end_datetime_timestamp', true );
		$event_all_day_event	= get_post_meta( $event, '_ucfbands_event_all_day_event', true );
		$event_show_start_time	= get_post_meta( $event, '_ucfbands_event_show_start_time', true );
		$event_show_end_time	= get_post_meta( $event, '_ucfbands_event_show_end_time', true );
		
		
		
		// CONVERT TO TIME STRINGS //
		$event_start_month	= date('M', $event_start_timestamp);
		$event_start_day	= date('j', $event_start_timestamp);
		$event_start_time 	= date('g:i A', $event_start_timestamp);
		
		$event_end_month	= date('M', $event_end_timestamp);
		$event_end_day	= date('j', $event_end_timestamp);
		$event_end_time 	= date('g:i A', $event_end_timestamp);
		
		
		
		// CHANGE TIME & VENUE FLAGS TO TEXT //
		if( $event_show_start_time == 'no' )
			$event_start_time = 'TBA';
			
		if( $event_all_day_event == true )
			$event_start_time = 'Daily';
			
		
		
		// Determine if columns are needed
		if( $archive == 'yes' )
			$li_class = 'col-lg-4';
			
		else if( $block == 'no' )
			$li_class = '';
		
		else
			$li_class = '';
		
		
		
		// Add extra bottom margin if archive
		if( $archive == 'yes')
			$li_margin = 'style="margin-bottom: 35px" ';
		
		else
			$li_margin = '';
		
		
		// Start List Item. Enter bottom margin if archive
		$return_string .= '<li class="' . $li_class . '" ' . $li_margin . '>';			
			
			
			// TIMELINE PANEL //
			if( $block == 'yes' )
				$return_string .= '<div class="timeline-panel">';
			
			
				// Date "Icon"
				$return_string .= '<div class="timeline-date ' . $event_icon_bgcolor . '">';
					
					
					// Month
					if( $event_start_month == $event_end_month )
						$return_string .= '<h5 class="timeline-month">' . $event_start_month . '<br>';
						
					else
						$return_string .= '<h5 class="timeline-month">' . $event_start_month . ' / ' . $event_end_month . '<br>';
					
					
					// Day
					if( $event_start_day == $event_end_day )
						$return_string .= '<span>' . $event_start_day . '</span></h5>';
						
					else
						$return_string .= '<span class="event-multi-day">' . $event_start_day . ' - ' . $event_end_day . '</span></h5>';
					
								
				// End Timeline Date
				$return_string .= '</div>';
			
				
				// Timeline content (to keep stuff to the right)
				$return_string .= '<div class="timeline-content">';
				
				
					// TIMELINE HEADING //
					$return_string .= '<div class="timeline-heading">';
					
					
					
						// EVENT TITLE //
						$return_string .= '<h4 class="timeline-title">' . get_the_title($event) . '</h4>';
						
						
						
						// EVENT DATE/TIME/VENUE //
						$return_string .= '<p><small><b>'; 
													
							
						// TIME //
						$return_string .= '<i class="fa fa-clock-o"></i> ' . $event_start_time;
													
						
						// If end time is desired and start time isn't TBA
						if( ($event_show_end_time == 'yes') && ($event_start_time != 'TBA') )
							$return_string .= ' - ' . date('g:i A', $event_end_timestamp);
						
						
						// Spacer & Venue Icon
						$return_string .= ' &nbsp;|&nbsp; <i class="fa fa-map-marker"></i> ';
						
						
						
						// Place/Venue
						if( $event_venue != '')
							$return_string .= $event_venue;
							
						else // Empty, so TBA
							$return_string .= 'Venue TBA';
						
						
						
						// End Date/Time/Place Syling
						$return_string .= '</b></small></p>';
						
						
						
					// End Timeline Heading
					$return_string .= '</div>';
					
					
					
					// TIMELINE BODY //
					$return_string .= '<div class="timeline-body" style="">' . $content . '</div>';
			
			
			
				// End Timeline Content
				$return_string .= '</div>';
			
			
			
			// End Timeline Panel, unless there's no block.
			if( $block == 'yes' )
				$return_string .= '</div>';
			
			else
				$return_string .= '<div style="clear:both; width: 100%; height: 1px;"></div><hr>';
			
		
		// Close List Item
		$return_string .= '</li>';
		
				
	} // Loop
	
	
	// Close UL
	$return_string .= '</ul>';
	
	
	// Close div (whether it's an archive or not there is one!, unless there's no block!)
	if( $block == 'yes' )
		$return_string .= '</div>';
	

	// RETURN CODE
	return $return_string;
}
add_shortcode( 'events', 'shortcode_events' );



//---------------------------//
// FACULTY & STAFF SHORTCODE //
//---------------------------//
function shortcode_staff( $atts ) {

	// ATTRIBUTES //
	extract( shortcode_atts(
		array(
		), $atts )
	);
	 
	
	$return_string = '';
	

	$return_string .= '<div class="row">';


	// SHOW FACULTY FIRST //

		
		// Post Variable
		global $post;
				
	
		// FACULTY CHECK //
		if( get_post_meta( $post->ID, '_ucfbands_staff_is_faculty', true ) == 'is_faculty' ) {
		 
				
		
		// STAFF MEMEBER //
		$return_string .= '<div class="col-lg-6"><div class="block block-staff">';
				
				
					//-------------------//
					// SHOW FACULTY INFO //
					//-------------------//	
					
					// Display Portrait / Featured Image
					the_post_thumbnail('medium');
					
					
					// Get staff info
					$staff_position = get_post_meta( $post->ID, '_ucfbands_staff_position', true );
					$staff_email	= get_post_meta( $post->ID, '_ucfbands_staff_email', true );
					$staff_phone	= get_post_meta( $post->ID, '_ucfbands_staff_phone', true );
					
					
					// Display staff name
					$return_string .= '<h3>' . get_the_title() . '</h3>';
					
					
					// Display staff position
					$return_string .= '<b><i>' . $staff_position . '</i></b>';
					
					
					// Do not display spacers if not needed
					if( $staff_email != '' && $staff_phone != '')
					{
					
						// Spacer
						$return_string .= '<br><br>';
						
						
						// Email (If available)
						if( $staff_email != '' )
						{	
							// Icon
							$return_string .= '<i class="fa fa-envelope"></i> ';
							
							// Mailto link
							$return_string .= '<a href="mailto:' . $staff_email . '">' . $staff_email . '</a>';
						}
						
						
						// Phone (If available) 
						if( ($staff_phone != '') && ($staff_phone != '(407) 823-') )
						{	
							// Break, then Icon
							$return_string .= '<br><i class="fa fa-phone"></i> ';
							
							// Number
							$return_string .= $staff_phone;
						}
					
					} // If no email & phone
						
					// Spacer
					$return_string .= '<br><br>';
					
					
					// Staff Bio
					the_content();
													
				
			$return_string .= '</div><!-- /.staff --></div><!-- /.col-lg-6 (Staff)';
		

		
			} // Faculty Checl
		
			
						
	$return_string .= '</div><!-- /.row -->	<br><br>';



	// STAFF TITLE //
	$return_string .= '<h1 class="page-title" id="ucfbands-staff">Staff</h1>';


	$return_string .= '<div class="row">';


		
		// Post Variable
		global $post;
				
	
		// FACULTY CHECK //
		if( get_post_meta( $post->ID, '_ucfbands_staff_is_faculty', true ) == 'not_faculty' ) {
		 				
		
		// STAFF MEMEBER //
		$return_string .= '<div class="col-lg-6">
			
			<div class="block block-staff">';
				
				
					//-------------------//
					// SHOW FACULTY INFO //
					//-------------------//	
					
					// Display Portrait / Featured Image
					the_post_thumbnail('medium');
					
					
					// Get staff info
					$staff_position = get_post_meta( $post->ID, '_ucfbands_staff_position', true );
					$staff_email	= get_post_meta( $post->ID, '_ucfbands_staff_email', true );
					$staff_phone	= get_post_meta( $post->ID, '_ucfbands_staff_phone', true );
					
					
					// Display staff name
					$return_string .= '<h3>' . get_the_title() . '</h3>';
					
					
					// Display staff position
					$return_string .= '<b><i>' . $staff_position . '</i></b>';


					// Do not display spacers if not needed
					if( $staff_email != '' && $staff_phone != '')
					{
					
						// Spacer
						$return_string .= '<br><br>';
						
						
						// Email (If available)
						if( $staff_email != '' )
						{	
							// Icon
							$return_string .= '<i class="fa fa-envelope"></i> ';
							
							// Mailto link
							$return_string .= '<a href="mailto:' . $staff_email . '">' . $staff_email . '</a>';
						}
						
						
						// Phone (If available) 
						if( ($staff_phone != '') && ($staff_phone != '(407) 823-') )
						{	
							// Break, then Icon
							$return_string .= '<br><i class="fa fa-phone"></i> ';
							
							// Number
							$return_string .= $staff_phone;
						}
						
						
					} // if no email and phone

						
					// Spacer
					$return_string .= '<br><br>';
														
					
					// Staff Bio
					the_content();
													
				
			$return_string .= '</div><!-- /.staff -->
			
		</div><!-- /.col-lg-6 (Staff) -->';
		

		
			} // Faculty Checl
		

						
	$return_string .= '</div><!-- /.row -->';



	// RETURN STRING
	return $return_string;

	
}
add_shortcode( 'staff', 'shortcode_staff' );



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
		'slug'                => 'events',
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
	register_post_type( 'events', $args );

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
        'pages' => array('events'), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
			array(
				'name' => 'Start Date & Time',
				'desc' => '<b>Both Date and time are required.</b>',
				'id'   => $prefix . 'event_start_datetime_timestamp',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => 'End Date & Time',
				'desc' => '<b>Both Date and Time are required.</b><br><br>End dates do not show if they are the same as the start date.',
				'id'   => $prefix . 'event_end_datetime_timestamp',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => 'All-Day Event',
				'desc' => '<br><br>Checking this will display "Daily" instead of the start/end time or "TBA".<br><b>Start and end times must still be set so the item stays on the website for the appropriate time!',
				'id' => $prefix . 'event_all_day_event',
				'type' => 'checkbox'
			),
			array(
				'name'    => 'Show Start Time',
				'id'      => $prefix . 'event_show_start_time',
				'type'    => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes'	=> __( 'Yes', 'cmb' ),
					'no'   => __( 'No (Time TBA)', 'cmb' ),
				),
			),			
			array(
				'name'    => 'Show End Time',
				'id'      => $prefix . 'event_show_end_time',
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes'	=> __( 'Yes', 'cmb' ),
					'no'   => __( 'No', 'cmb' ),
				),
			),			
            array(
                'name' => 'Location/Venue',
                'desc' => 'Leave empty for "TBA"',
                'id' => $prefix . 'event_venue',
                'type' => 'text_medium'
            ),
			array(
				'name'    => 'Icon Background Color',
				'id'      => $prefix . 'event_icon_bgcolor',
				'type'    => 'radio',
				'default' => 'ucf-gray',
				'options' => array(
					'ucf-gray'	=> __( '<b>Concert Ensemble:</b> UCF Gray', 'cmb' ),
					'gold'   	=> __( '<b>Athletic:</b> Gold', 'cmb' ),
					'success'	=> __( '<b>General/Clinic:</b> Green', 'cmb' ),
					'danger'	=> __( '<b>Audition:</b> Red', 'cmb' ),
					'primary'	=> __( 'Blue', 'cmb' ),
					'warning'	=> __( 'Orange', 'cmb' ),
					'default'	=> __( 'Gray', 'cmb' )
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