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
		'mk-armory' 		=> __( 'MK Armory', 'text_domain' ),
		'mkmc'				=> __( 'MKMC Docs', 'text_domain' ),
		'jk-dungeon'		=> __( 'JK Dungeon', 'text_domain' ),
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
				<h2><i class="fa fa-calendar"></i> ' . $title . ' <a class="btn btn-default btn-xs" href="' . get_site_url() . '/index.php/events">View All</a></h2>
				<ul class="timeline">';
				
		else
			$return_string = '
				<h2><i class="fa fa-calendar"></i> ' . $title . ' <a class="btn btn-default btn-xs" href="' . get_site_url() . '/index.php/events">View All</a></h2>
				<ul class="timeline" style="padding-top: 10px;">';
	}



/*
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




//---------------------//
// REHEARSAL SHORTCODE //
//---------------------//
function shortcode_rehearsals( $atts ) {

	// ATTRIBUTES //
	extract( shortcode_atts(
		array(
			'band'	      => 'marching-knights',
			'width'		  => 4,
			'title'		  => 'Rehearsal Schedules',
			'block'		  => 'no'
		), $atts )
	);	
	
	
	// String for return
	$return_string;
	
	
	// Start columm & Title
	$return_string = '<div class="col-lg-' . $width . '">';
	

	// If a block is requested
	if( $block == 'yes' )
		$return_string .= '<div class="block">';


		// Block Header
		$return_string .= '<h2 style="margin-bottom: 17px;"><i class="fa fa-list"></i> ' . $title . '</h2>';
		

	
		// GET REHEARSAL DATA //
	
		// Preparing the query for rehearsals
		// Only get rehearsals that haven't passed!
		$meta_quer_args = array(
			'relation'	=>	'AND',
			array(
				'key'		=>	'_ucfbands_rehearsal_date_timestamp',
				'value'		=>	time(),
				'compare'	=>	'>'
			)
		);
	
			
		// Query Options
		$rehearsal_selection = array(
			'post_type'		=> 'rehearsal',
			//'category_name'	=> $band, Do this at next level since there aren't cats technically involved.
			'fields' 		=> 'ids', // This is so only the ID is returned instead of the WHOLE post object (Performance)
			'meta_key'		=> '_ucfbands_rehearsal_date_timestamp',
			'orderby' 		=> 'meta_value_num',
			'order' 		=> 'ASC',
			'post_count'	=> 3,
			'posts_per_page'=> 3,
			'meta_query'	=> $meta_quer_args
		);
		
		
		// GET POSTS //
		$rehearsals = new WP_Query( $rehearsal_selection );
			
				
		
		// If there are no results
		if(($rehearsals->have_posts()) == false)
		{
			$return_string .= 'There are no upcoming rehearsal schedules found for this band.<br><br>Please Note: This <b>does not</b> mean any upcoming rehearsals are cancelled.';
		}
	
		// If there ARE results
		else {
	
			// Start Accordion
			$return_string .= '<div class="panel-group" id="accordion">';
			
		
			// Get Posts associated with rehearsals      
			$rehearsals = $rehearsals->get_posts();
		
			
			// Counter for Panel Collapse IDs
			$collapse_num = 1;
			
	
			// LOOP THROUGH THE IDS OF EACH REHEARSALS
			foreach($rehearsals as $rehearsal)
			{
				
				// GET POST CONTENT //
				$content_post = get_post($rehearsal);
				$content = $content_post->post_content;
				
				
				// GET POST META //
				$rehearsal_date_timestamp  		  = get_post_meta( $rehearsal, '_ucfbands_rehearsal_date_timestamp', true );
				$rehearsal_schedule		          = get_post_meta( $rehearsal, '_ucfbands_rehearsal_schedule', true );
				$rehearsal_announcements          = get_post_meta( $rehearsal, '_ucfbands_rehearsal_announcements', true );
				$rehearsal_cancelled	          = get_post_meta( $rehearsal, '_ucfbands_rehearsal_cancelled', true );
				$rehearsal_cancelled_announcement = get_post_meta( $rehearsal, '_ucfbands_rehearsal_cancelled_announcement', true );
				$rehearsal_band			          = get_post_meta( $rehearsal, '_ucfbands_rehearsal_band', true );
				
				
				// CONVERT TO DATE STRINGS //
				$rehearsal_week_day	= date('l', $rehearsal_date_timestamp);
				$rehearsal_date		= date('n/j', $rehearsal_date_timestamp);
				
								
				
				// Start Collapsible Group
				$return_string .= '<div class="panel panel-default">';
				
					
					// Panel Heading
					$return_string .= '<a data-toggle="collapse" data-parent="#accordion" href="#collapse' . $collapse_num . '"><div class="panel-heading">';
					
					
						// Panel Title
						$return_string .= '<h3 class="panel-title" style="font-size: 1.25em;">';
						
						
							// Toggle
						 	$return_string .= $rehearsal_week_day . ', ' . $rehearsal_date;
					
						
						// End Panel Title
						$return_string .= '</h3>';
					
					
					// End Panel Heading
					$return_string .= '</div></a>';
					
				
					// Panel "Collapse"
					$return_string .= '<div id="collapse' . $collapse_num . '" class="panel-collapse collapse in">';
					
					
						// Panel Body
						$return_string .= '<div class="panel-body" style="height: auto;">';
							
							
							// Check if rehearsal is cancelled
							if( $rehearsal_cancelled == true )
							{
								
								// Display Notice
								$return_string .= '<h4><i class="fa fa-exclamation-triangle"></i> Rehearsal Cancelled</h4>';
								
								
								// Rehearsal Cancelled announcement
								if( $rehearsal_cancelled_announcement != '' )
									$return_string .= $rehearsal_cancelled_announcement;
								
								
							} // If rehearsal cancelled
							
							else {
							
								// Schedule Title
								$return_string .= '<h4 style="font-size: 1em;">Schedule</h4>';
								
	
								// Schedule
								$return_string .= '<ul>' . $rehearsal_schedule . '</ul>';
								
												
								
								// If there are announcements, show them
								if( $rehearsal_announcements != '' )
								{
									
									// Divider
									$return_string .= '<hr style="margin-top: 20px; margin-bottom: 20px;">';

									
									// Announcements Title
									$return_string .= '<h4 style="font-size: 1em;">Announcements</h4>';
									
									// Announcements
									$return_string .= '<ul class="red">' . $rehearsal_announcements . '</ul>';
									
								} // End if announcements
						
							
						
							} // Else (cancelled rehearsal)
						
						
						// End Panel Body & Collapse
						$return_string .= '</div></div>';
						
						
					// End Panel
					$return_string .= '</div>';
				
				
				// Increase ID num
				$collapse_num++;
	
	
			} // End foreach rehearsal in rehearsals


			// End Collapsible Group
			$return_string .= '</div>';

		
		} // End else (if there are results from query)
	
	// End Block (if applicable)
	if( $block == 'yes' )
		$return_string .= '</div>';
		
	
	// End col
	$return_string .= '</div>';
	

	// Return the String
	return $return_string;
	
}
add_shortcode( 'rehearsals', 'shortcode_rehearsals' );





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



//--------------------//
// REGISTER REHEARSAL //
//--------------------//
function ucfbands_rehearsal() {

	$labels = array(
		'name'                => _x( 'Rehearsals', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Rehearsal', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Rehearsals', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Rehearsals', 'text_domain' ),
		'view_item'           => __( 'View Rehearsal', 'text_domain' ),
		'add_new_item'        => __( 'Add New Rehearsal', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Rehearsal', 'text_domain' ),
		'update_item'         => __( 'Update Rehearsal', 'text_domain' ),
		'search_items'        => __( 'Search Rehearsals', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'ucfbands_rehearsal', 'text_domain' ),
		'description'         => __( 'Rehearsal Schedule with Details', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', /*'editor',*/ 'revisions', 'page-attributes', ),
		'taxonomies'          => array( /*'category'*/ ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-list-view',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'rehearsal', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ucfbands_rehearsal', 0 );



//-------------------//
// REGISTER PEP BAND //
//-------------------//
function ucfbands_pep_band() {

	$labels = array(
		'name'                => _x( 'Pep Bands', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Pep Band', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Pep Bands', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Pep Bands', 'text_domain' ),
		'view_item'           => __( 'View Pep Band', 'text_domain' ),
		'add_new_item'        => __( 'Add Pep Band', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Pep Band', 'text_domain' ),
		'update_item'         => __( 'Update Pep Band', 'text_domain' ),
		'search_items'        => __( 'Search Pep Band', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'ucfbands_pep_band', 'text_domain' ),
		'description'         => __( 'Pep Band Gig/Event Details', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'revisions', ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-audio',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'pep_band', $args );

}
// Hook into the 'init' action
add_action( 'init', 'ucfbands_pep_band', 0 );




//------------------------//
// REGISTER PHOTO GALLERY //
//------------------------//
function ucfbands_photo_gallery() {

	$labels = array(
		'name'                => _x( 'Photo Galleries', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Photo Gallery', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Photo Galleries', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Gallery', 'text_domain' ),
		'all_items'           => __( 'All Galleries', 'text_domain' ),
		'view_item'           => __( 'View Gallery', 'text_domain' ),
		'add_new_item'        => __( 'Add New Gallery', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Gallery', 'text_domain' ),
		'update_item'         => __( 'Update Gallery', 'text_domain' ),
		'search_items'        => __( 'Search Galleries', 'text_domain' ),
		'not_found'           => __( 'Not Found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                => 'gallery',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => false,
	);
	$args = array(
		'label'               => __( 'ucfbands_photo_gallery', 'text_domain' ),
		'description'         => __( 'Basically a container for a Ess. Grid Custom Gallery', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'thumbnail', ),
		'taxonomies'          => array( 'category' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-format-gallery',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'photo_gallery', $args );

}

// Hook into the 'init' action
add_action( 'init', 'ucfbands_photo_gallery', 0 );



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



	//-- REHEASAL CPT --//
    $meta_boxes['ucfbands_rehearsal'] = array(
        'id' => 'ucfbands_rehearsal',
        'title' => 'Rehearsal Details',
        'pages' => array('rehearsal'), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
			array(
				'name' => 'Date',
				'id'   => $prefix . 'rehearsal_date_timestamp',
				'type' => 'text_date_timestamp',
				// 'timezone_meta_key' => $prefix . 'timezone',
				// 'date_format' => 'l jS \of F Y',
			),
			array(
				'name' => 'Schedule',
				'desc' => '<u>Instructions</u>
- Remove unused list items (the &lt;li&gt; &amp; &lt;/li&gt; tags)
- Common HTML styling tags:
	&lt;b&gt;Bold Text&lt;/b&gt;
	&lt;i&gt;Italics Text&lt;/i&gt;
	
- To put another bulleted list inside the existing bulleted list, add &lt;ul&gt; tags and &lt;li&gt; tags like this:

	&lt;li&gt;&lt;b&gt;6:00 PM: &lt;/b&gt; Existing thing&lt;/li&gt;
	&lt;ul&gt;
		&lt;li&gt;Second-level item&lt;/li&gt;
		&lt;li&gt;Another second-level item&lt;/li&gt;
	&lt;/ul&gt;
	&lt;li&gt;&lt;b&gt;6:00 PM: &lt;/b&gt; Another Existing thing&lt;/li&gt;
	',
				'default' => "<li><b>6:00 PM: </b>Thing</li>
<li><b>:00 PM: </b></li>
<li><b>:00 PM: </b></li>
<li><b>:00 PM: </b></li>
<li><b>:00 PM: </b></li>",
				'id' => $prefix . 'rehearsal_schedule',
				'type' => 'textarea_code'
			),
			
			
			array(
				'name' => 'Announcements',
				'desc' => '<span style="color:#a94442;">Remove default code above if there are no announcements!</span><br>Refer to the schedule field instructions above.',
				'default' => "<li>Thing</li>
<li></li>",
				'id' => $prefix . 'rehearsal_announcements',
				'type' => 'textarea_code'
			),
			array(
				'name' => '<b><span style="color:#a94442;">Cancelled Rehearsal</span></b>',
				'desc' => 'This will not <i>remove</i> the rehearsal, but post a notice!',
				'id' => $prefix . 'rehearsal_cancelled',
				'type' => 'checkbox'
			),
			array(
				'name' => 'Cancelled Rehearsal Announcement',
				'desc' => 'Optional! Displays under "Rehearsal Cancelled" Notice.<br>Only shows if the rehearsal is cancelled and there is an announcement entered.',
				'default' => '',
				'id' => $prefix . 'rehearsal_cancelled_announcement',
				'type' => 'textarea_small'
			),
			array(
				'name'    => 'Band',
				'id'      => $prefix . 'rehearsal_band',
				'type'    => 'radio',
				'default' => 'marching-knights',
				'options' => array(
					'marching-knights' => __( 'Marching Knights', 'cmb' ),
					'jammin-knights'   => __( "Jammin' Knights", 'cmb' ),
					'concert-band'     => __( 'concert-band', 'cmb' ),
					'symphonic-band'   => __( 'symphonic-band', 'cmb' ),
					'wind-ensemble'    => __( 'wind-ensemble', 'cmb' ),
				),
			),
        ),
    );



	//-- PEP BAND CPT --//
    $meta_boxes['ucfbands_pep_band'] = array(
        'id' => 'ucfbands_pep_band',
        'title' => 'Pep Band Details',
        'pages' => array('pep_band'), // post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
        'fields' => array(
			array(
				'name' => 'Start Date & Time',
				'desc' => '<b>Both Date and time are required.</b>',
				'id'   => $prefix . 'pep_band_start_datetime_timestamp',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name' => 'End Date & Time',
				'desc' => '<b>Both Date and Time are required.</b><br><br>End dates do not show if they are the same as the start date.',
				'id'   => $prefix . 'pep_band_end_datetime_timestamp',
				'type' => 'text_datetime_timestamp',
			),
			array(
				'name'    => 'Show Start Time',
				'id'      => $prefix . 'pep_band_show_start_time',
				'type'    => 'radio',
				'default' => 'yes',
				'options' => array(
					'yes'	=> __( 'Yes', 'cmb' ),
					'no'   => __( 'No (Time TBA)', 'cmb' ),
				),
			),			
			array(
				'name'    => 'Show End Time',
				'id'      => $prefix . 'pep_band_show_end_time',
				'type'    => 'radio',
				'default' => 'no',
				'options' => array(
					'yes'	=> __( 'Yes', 'cmb' ),
					'no'   => __( 'No', 'cmb' ),
				),
			),			
			array(
				'name' => 'Schedule',
				'desc' => '<u>Instructions</u>
- Remove unused list items (the &lt;li&gt; &amp; &lt;/li&gt; tags)
- Common HTML styling tags:
	&lt;b&gt;Bold Text&lt;/b&gt;
	&lt;i&gt;Italics Text&lt;/i&gt;
	
- To put another bulleted list inside the existing bulleted list, add &lt;ul&gt; tags and &lt;li&gt; tags like this:

	&lt;li&gt;&lt;b&gt;6:00 PM: &lt;/b&gt; Existing thing&lt;/li&gt;
	&lt;ul&gt;
		&lt;li&gt;Second-level item&lt;/li&gt;
		&lt;li&gt;Another second-level item&lt;/li&gt;
	&lt;/ul&gt;
	&lt;li&gt;&lt;b&gt;6:00 PM: &lt;/b&gt; Another Existing thing&lt;/li&gt;
	',
				'default' => "<li><b>6:00 PM: </b>Thing</li>
<li><b>:00 PM: </b></li>
<li><b>:00 PM: </b></li>
<li><b>:00 PM: </b></li>
<li><b>:00 PM: </b></li>",
				'id' => $prefix . 'pep_band_schedule',
				'type' => 'textarea_code'
			),
            array(
                'name' => 'Location/Venue',
                'desc' => 'Leave empty for "TBA"',
                'id' => $prefix . 'pep_band_venue',
                'type' => 'text_medium'
            ),
			array(
				'name'    => 'Band',
				'id'      => $prefix . 'pep_band_band',
				'type'    => 'radio',
				'default' => 'marching-knights',
				'options' => array(
					'marching-knights' => __( 'Marching Knights', 'cmb' ),
					'jammin-knights'   => __( "Jammin' Knights", 'cmb' ),
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