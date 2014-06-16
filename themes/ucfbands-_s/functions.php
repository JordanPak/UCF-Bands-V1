<?php
/**
 * UCFBands functions and definitions
 *
 * @package UCFBands
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'ucfbands_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function ucfbands_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on UCFBands, use a find and replace
	 * to change 'ucfbands' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'ucfbands', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ucfbands' ),
	) );
	
	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link'
	) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'ucfbands_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // ucfbands_setup
add_action( 'after_setup_theme', 'ucfbands_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function ucfbands_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'ucfbands' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'ucfbands_widgets_init' );




/**
 * LOAD JQUERY
 */
/*function ucfbands_load_jquery() {
    wp_enqueue_script('jquery');
}
add_action( 'wp_enqueue_scripts', 'ucfbands_load_jquery' );
*/

/**
 * Enqueue scripts and styles.
 */
function ucfbands_scripts() {
	wp_enqueue_style( 'ucfbands-style', get_stylesheet_uri() );

	wp_enqueue_script( 'ucfbands-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'ucfbands-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );


	//-- UCFB FOOTER SCRIPTS --//
	
	// Bootstrap
	wp_enqueue_script( 'bootstrap-scripts', get_template_directory_uri() . '/js/bootstrap.min.js', array(), '1', true );
	
	
	// Metis Menu
	wp_enqueue_script( 'metis-menu-script', get_template_directory_uri() . '/js/jquery.metisMenu.js', array(), '1', true );


	// SB Admin
	wp_enqueue_script( 'sb-admin-script', get_template_directory_uri() . '/js/sb-admin.js', array(), '1', true );	
	



	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
	
	//----------------------//
	// LOAD UCFBANDS STYLES //
	//----------------------//

    // Social Buttons Plugin
    wp_enqueue_style( 'social-buttons-style', get_template_directory_uri() . '/css/plugins/social-buttons.css', array(), '1' );
	
	
	// Timeline Plugin
    wp_enqueue_style( 'timeline-style', get_template_directory_uri() . '/css/plugins/timeline.css', array(), '1' );

    
	// Bootstrap style
    wp_enqueue_style( 'bootstrap-style', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '1' );      
		

	// FontAwesome Styles
    wp_enqueue_style( 'fontawesome-style', get_template_directory_uri() . '/font-awesome/css/font-awesome.min.css', array(), '1' );

	
	// SB-Admin Styles
    wp_enqueue_style( 'sb-admin-style', get_template_directory_uri() . '/css/sb-admin.css', array(), '1' );

	
	// UCFBands Theme
    wp_enqueue_style( 'ucfbands-custom-style', get_template_directory_uri() . '/css/ucfbands-theme.css', array(), '1' );	
	

	// Google Fonts
	wp_enqueue_style( 'google-fonts', 'http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic|Droid+Serif:400,400italic,700,700italic', array(), '1');

}

add_action( 'wp_enqueue_scripts', 'ucfbands_scripts' );




/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
