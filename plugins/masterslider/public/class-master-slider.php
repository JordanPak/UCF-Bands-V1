<?php
/**
 * Master Slider.
 *
 * @package   MasterSlider
 * @author    averta [averta.net]
 * @license   LICENSE.txt
 * @link      http://masterslider.com
 * @copyright Copyright © 2014 averta
 */

if ( ! class_exists( 'Master_Slider' ) ) :

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 */
class Master_Slider {

	/**
	 * Unique identifier for your plugin.
	 *
	 * The variable name is used as the text domain when internationalizing strings of text.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'masterslider';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;


	/**
	 * Instance of Master_Slider_Admin class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	public $admin = null;



	/**
	 * Initialize the plugin
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$this->includes();

		add_action( 'init', array( $this, 'init' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Loaded action
		do_action( 'masterslider_loaded' );
	}


	/**
	 * 
	 * @return [type] [description]
	 */
	private function includes() {

		// load common functionalities
		include_once( MSWP_AVERTA_INC_DIR . '/index.php' );
			

		// Dashboard and Administrative Functionality
		if ( is_admin() ) {

			// Load AJAX spesific codes on demand 
			if ( defined('DOING_AJAX') && DOING_AJAX ){
				include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/classes/class-msp-admin-ajax.php');
				include_once( MSWP_AVERTA_ADMIN_DIR . '/includes/msp-admin-functions.php');
			}
			
			// Load admin spesific codes 
			else {
				$this->admin = include( MSWP_AVERTA_ADMIN_DIR . '/class-master-slider-admin.php' );
			}

		// Load Frontend Functionality
		} else {

			include_once( 'includes/class-msp-frontend-assets.php' );
		}

	}


	/**
	 * Init Masterslider when WordPress Initialises.
	 * 	
	 * @return void
	 */
	public function init(){

		// Before init action
		do_action( 'before_masterslider_init' );

		// Load plugin text domain
		$this->load_plugin_textdomain();

		// Init action
		do_action( 'masterslider_init' );
	}


	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}
		
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

		global $mspdb;
		$mspdb->create_tables();

		// add masterslider custom caps
		self::assign_custom_caps();
		do_action( 'masterslider_activated', get_current_blog_id() );
	}

	/**
	 * Assign masterslider custom capabilities to main roles
	 * @return void
	 */
	public static function assign_custom_caps( $force_update = false ){

		// check if custom capabilities are added before or not
		$is_added = get_option( 'masterslider_capabilities_added', 0 );

		// add caps if they are not already added
		if( ! $is_added || $force_update ) {

			// assign masterslider capabilities to following roles
			$roles = array( 'administrator', 'editor' );

			foreach ( $roles as $role ) {
				$role = get_role( $role );
				$role->add_cap( 'access_masterslider'  ); 
				$role->add_cap( 'publish_masterslider' ); 
				$role->add_cap( 'delete_masterslider'  ); 
				$role->add_cap( 'create_masterslider'  );
				$role->add_cap( 'export_masterslider'  );
				$role->add_cap( 'duplicate_masterslider'  );
			}

			update_option( 'masterslider_capabilities_added', 1 );
		}
	}


	/**
	 * Set default options
	 * @return void
	 */
	public static function set_default_options( $force_update = false ){

		// check if default options are added before or not
		$is_added = get_option( 'masterslider_default_options_added', 0 );

		// add caps if they are not already added
		if( ! $is_added || $force_update ) {

			msp_update_option( 'preset_effect', 'eyJtZXRhIjp7IlByZXNldEVmZmVjdCFpZHMiOiI2LDcsOCw5LDEwLDExLDEyLDEzLDE0LDE1LDE2LDE3LDE4LDE5LDIwLDIxLDIyLDIzLDI0LDI1LDI2LDI3LDI4LDI5LDMwLDMxIiwiUHJlc2V0RWZmZWN0IW5leHRJZCI6MzJ9LCJNU1BhbmVsLlByZXNldEVmZmVjdCI6eyI2Ijoie1wiaWRcIjo2LFwibmFtZVwiOlwiUmlnaHQgc2hvcnRcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6MTUwfSIsIjciOiJ7XCJpZFwiOjcsXCJuYW1lXCI6XCJMZWZ0IHNob3J0XCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWFwiOi0xNTB9IiwiOCI6IntcImlkXCI6OCxcIm5hbWVcIjpcIlRvcCBzaG9ydFwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMTUwfSIsIjkiOiJ7XCJpZFwiOjksXCJuYW1lXCI6XCJCb3R0b20gc2hvcnRcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6MTUwfSIsIjEwIjoie1wiaWRcIjoxMCxcIm5hbWVcIjpcIlJpZ2h0IGxvbmdcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6NTAwfSIsIjExIjoie1wiaWRcIjoxMSxcIm5hbWVcIjpcIkxlZnQgbG9uZ1wiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotNTAwfSIsIjEyIjoie1wiaWRcIjoxMixcIm5hbWVcIjpcIlRvcCBsb25nXCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi01MDB9IiwiMTMiOiJ7XCJpZFwiOjEzLFwibmFtZVwiOlwiQm90dG9tIGxvbmdcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVZXCI6NTAwfSIsIjE0Ijoie1wiaWRcIjoxNCxcIm5hbWVcIjpcIjNEIEZyb250IHNob3J0XCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWlwiOjUwMH0iLCIxNSI6IntcImlkXCI6MTUsXCJuYW1lXCI6XCIzRCBCYWNrIHNob3J0XCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWlwiOi01MDB9IiwiMTYiOiJ7XCJpZFwiOjE2LFwibmFtZVwiOlwiM0QgRnJvbnQgbG9uZ1wiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVpcIjoxNTAwfSIsIjE3Ijoie1wiaWRcIjoxNyxcIm5hbWVcIjpcIjNEIEJhY2sgbG9uZ1wiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVpcIjotMTUwMH0iLCIxOCI6IntcImlkXCI6MTgsXCJuYW1lXCI6XCJSb3RhdGUgMTgwXCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwicm90YXRlXCI6MTgwfSIsIjE5Ijoie1wiaWRcIjoxOSxcIm5hbWVcIjpcIlJvdGF0ZSAzNjBcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJyb3RhdGVcIjozNjB9IiwiMjAiOiJ7XCJpZFwiOjIwLFwibmFtZVwiOlwiUm90YXRlIDkwXCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwicm90YXRlXCI6OTB9IiwiMjEiOiJ7XCJpZFwiOjIxLFwibmFtZVwiOlwiUm90YXRlIC05MFwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInJvdGF0ZVwiOi05MH0iLCIyMiI6IntcImlkXCI6MjIsXCJuYW1lXCI6XCIzRCBSb3RhdGUgbGVmdFwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMjUwLFwicm90YXRlWVwiOjI1MH0iLCIyMyI6IntcImlkXCI6MjMsXCJuYW1lXCI6XCIzRCBSb3RhdGUgcmlnaHRcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVYXCI6MjUwLFwicm90YXRlWVwiOi0yNTB9IiwiMjQiOiJ7XCJpZFwiOjI0LFwibmFtZVwiOlwiM0QgUm90YXRlIHRvcFwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjotMjUwLFwicm90YXRlWFwiOjE1MH0iLCIyNSI6IntcImlkXCI6MjUsXCJuYW1lXCI6XCIzRCBSb3RhdGUgYm90dG9tXCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOjI1MCxcInJvdGF0ZVhcIjotMTUwfSIsIjI2Ijoie1wiaWRcIjoyNixcIm5hbWVcIjpcIlNrZXcgbGVmdFwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjotMjUwLFwic2tld1hcIjotMjV9IiwiMjciOiJ7XCJpZFwiOjI3LFwibmFtZVwiOlwiU2tldyByaWdodFwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVhcIjoyNTAsXCJza2V3WFwiOjI1fSIsIjI4Ijoie1wiaWRcIjoyOCxcIm5hbWVcIjpcIlNrZXcgdG9wXCIsXCJ0eXBlXCI6XCJwcmVzZXRcIixcImZhZGVcIjp0cnVlLFwidHJhbnNsYXRlWVwiOi0yNTAsXCJza2V3WVwiOi0yNX0iLCIyOSI6IntcImlkXCI6MjksXCJuYW1lXCI6XCJTa2V3IGJvdHRvbVwiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVlcIjoyNTAsXCJza2V3WVwiOi0yNX0iLCIzMCI6IntcImlkXCI6MzAsXCJuYW1lXCI6XCJSb3RhdGUgZnJvbnRcIixcInR5cGVcIjpcInByZXNldFwiLFwiZmFkZVwiOnRydWUsXCJ0cmFuc2xhdGVaXCI6MTUwMCxcInJvdGF0ZVwiOjI1MH0iLCIzMSI6IntcImlkXCI6MzEsXCJuYW1lXCI6XCJSb3RhdGUgYmFja1wiLFwidHlwZVwiOlwicHJlc2V0XCIsXCJmYWRlXCI6dHJ1ZSxcInRyYW5zbGF0ZVpcIjotMTUwMCxcInJvdGF0ZVwiOjI1MH0ifX0=' );

			update_option( 'masterslider_default_options_added', 1 );
		}
	}


	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		do_action( 'masterslider_deactivated' );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), MSWP_TEXT_DOMAIN );

		load_textdomain( MSWP_TEXT_DOMAIN, trailingslashit( WP_LANG_DIR ) . MSWP_TEXT_DOMAIN . '/' . MSWP_TEXT_DOMAIN . '-' . $locale . '.mo' );
		load_plugin_textdomain( MSWP_TEXT_DOMAIN, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

}

endif;

function MSP(){ return Master_Slider::get_instance(); } 
MSP();
