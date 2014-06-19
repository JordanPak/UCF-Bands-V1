<?php
/**
 * Essential Grid.
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */

class Essential_Grid {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 */
	const VERSION = '1.0.7';
	const TABLE_GRID = 'eg_grids';
	const TABLE_ITEM_SKIN = 'eg_item_skins';
	const TABLE_ITEM_ELEMENTS = 'eg_item_elements';
	const TABLE_NAVIGATION_SKINS = 'eg_navigation_skins';
	const TABLE_GRID_VERSION = '1.0.1';
	
	private $grid_serial = 0;
    
	private $grid_id = null;
	private $grid_name = null;
	private $grid_handle = null;
	private $grid_params = array();
	private $grid_postparams = array();
	private $grid_layers = array();
	
	//other changings
	private $filter_by_ids = array();
	private $load_more_post_array = array();
        
	/**
	 * Unique identifier for the plugin.
	 * The variable name is used as the text domain when internationalizing strings of text.
	 */
	protected $plugin_slug = 'essential-grid';

	
	/**
	 * Instance of this class.
	 */
	protected static $instance = null;

	
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'register_custom_post_type' ) );
		
		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_action('wp_ajax_Essential_Grid_Front_request_ajax', array($this, 'on_front_ajax_action'));
		add_action('wp_ajax_nopriv_Essential_Grid_Front_request_ajax', array($this, 'on_front_ajax_action')); //for not logged in users
		
	}

	
	/**
	 * Return the plugin slug.
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	
	/**
	 * Return an instance of this class.
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
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters('plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	
	/**
	 * Register and enqueue public-facing style sheet.
	 */
	public function enqueue_styles() {
		wp_register_style($this->plugin_slug . '-plugin-settings', EG_PLUGIN_URL . 'public/assets/css/settings.css', array(), self::VERSION);
		wp_enqueue_style($this->plugin_slug . '-plugin-settings' );
		//wp_add_inline_style( $this->plugin_slug . '-plugin-settings', '.aatest { display: block; }' );
		
		$font = new ThemePunch_Fonts();
		$font->register_fonts();
		
		wp_register_style('themepunchboxextcss', EG_PLUGIN_URL . 'public/assets/css/lightbox.css', array(), self::VERSION);
		
	}

	
	/**
	 * Register and enqueues public-facing JavaScript files.
	 */
	public function enqueue_scripts() {
		wp_register_script( 'themepunchboxext', EG_PLUGIN_URL . 'public/assets/js/lightbox.js', array('jquery'), self::VERSION);
		
		wp_enqueue_script( 'themepunchtools', EG_PLUGIN_URL . 'public/assets/js/jquery.themepunch.plugins.min.js', array( 'jquery', 'themepunchboxext' ), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-essential-grid-script', EG_PLUGIN_URL . 'public/assets/js/jquery.themepunch.essential.min.js', array( 'jquery', 'themepunchtools' ), self::VERSION );
		
	}
	
	
	/**
	 * Register Shortcode
	 */
	public static function register_shortcode($args){
		
		$grid = new Essential_Grid;
		extract(shortcode_atts(array('alias' => ''), $args, 'ess_grid'));
        $eg_alias = ($alias != '') ? $alias : implode(' ', $args);
		
		$eg_alias = implode(' ', $args);
		ob_start();
		$grid->output_essential_grid_by_alias($eg_alias);
		$content = ob_get_contents();
		ob_clean();
		ob_end_clean();
		$output_protection = get_option('tp_eg_output_protection', 'none');
		
		//handle output types
		switch($output_protection){
			case 'compress':
				$content = str_replace("\n", '', $content);
				$content = str_replace("\r", '', $content);
				return($content);
			break;
			case 'echo':
				echo $content;		//bypass the filters
			break;
			default: //normal output
				return($content);
			break;
		}
		
	}
	
	public static function fix_shortcodes($content){
		$columns = array("ess_grid");
		$block = join("|",$columns);

		// opening tag
		$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);

		// closing tag
		$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/","[/$2]",$rep);

		return $rep;
	}
	
	/**
	 * Register Custom Post Type & Taxonomy
	 */
	public function register_custom_post_type() {
		$postType = "essential_grid";
		$taxonomy = "essential_grid_category";
		
		$taxArgs = array();
		$taxArgs["hierarchical"] = true;
		$taxArgs["label"] = __("Custom Categories", EG_TEXTDOMAIN);
		$taxArgs["singular_label"] = __("Custom Categorie", EG_TEXTDOMAIN);
		$taxArgs["rewrite"] = true;
		$taxArgs["public"] = true;
		$taxArgs["show_admin_column"] = true;
		
		register_taxonomy($taxonomy,array($postType),$taxArgs); 
		
		$postArgs = array();
		$postArgs["label"] = __("Ess. Grids", EG_TEXTDOMAIN);
		$postArgs["singular_label"] = __("Ess. Grid", EG_TEXTDOMAIN);
		$postArgs["public"] = true;
		$postArgs["capability_type"] = "post";
		$postArgs["hierarchical"] = false;
		$postArgs["show_ui"] = true;
		$postArgs["show_in_menu"] = true;
		$postArgs["supports"] = array('title', 'editor', 'thumbnail', 'author', 'comments', 'excerpt');			
		$postArgs["show_in_admin_bar"] = false;			
		$postArgs["taxonomies"] = array($taxonomy, 'post_tag');
		
		$postArgs["rewrite"] = array("slug"=>$postType,"with_front"=>true);
		
		register_post_type($postType,$postArgs);
		
	}
	
	
	/**
	 * Create/Update Database Tables
	 */
	public static function create_tables(){
		global $wpdb;
		
		//Create/Update Grids Database
		$grid_ver = get_option("tp_eg_grids_version", 0.99);
		
		if(version_compare($grid_ver, 1, '<')){
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			$table_name = $wpdb->prefix . self::TABLE_GRID;
			$sql = "CREATE TABLE $table_name (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  name VARCHAR(255) NOT NULL,
				  handle VARCHAR(255) NOT NULL,
				  postparams TEXT NOT NULL,
				  params TEXT NOT NULL,
				  layers TEXT NOT NULL,
				  UNIQUE KEY id (id),
				  UNIQUE (handle)
				  );";
				  
			dbDelta($sql);
			
			$table_name = $wpdb->prefix . self::TABLE_ITEM_SKIN;
			$sql = "CREATE TABLE $table_name (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  name VARCHAR(255) NOT NULL,
				  handle VARCHAR(255) NOT NULL,
				  params TEXT NOT NULL,
				  layers TEXT NOT NULL,
				  settings TEXT,
				  UNIQUE KEY id (id),
				  UNIQUE (name),
				  UNIQUE (handle)
				  );";
			
			dbDelta($sql);
			
			$table_name = $wpdb->prefix . self::TABLE_ITEM_ELEMENTS;
			$sql = "CREATE TABLE $table_name (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  name VARCHAR(255) NOT NULL,
				  handle VARCHAR(255) NOT NULL,
				  settings TEXT NOT NULL,
				  UNIQUE KEY id (id),
				  UNIQUE (handle)
				  );";
				  
			dbDelta($sql);
			
			$table_name = $wpdb->prefix . self::TABLE_NAVIGATION_SKINS;
			$sql = "CREATE TABLE $table_name (
				  id mediumint(9) NOT NULL AUTO_INCREMENT,
				  name VARCHAR(255) NOT NULL,
				  handle VARCHAR(255) NOT NULL,
				  css TEXT NOT NULL,
				  UNIQUE KEY id (id),
				  UNIQUE (handle)
				  );";
			
			dbDelta($sql);

			update_option('tp_eg_grids_version', 1);
		}
		
		//Change database on certain release? No Problem, use the following:
		/*if($grid_ver < 1.01){
			
			//$sql = "";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		 
			update_option('tp_eg_grids_version', 1.01);
		}*/
	}
	
	
	/**
	 * Register Custom Sidebars, created in Grids
	 * @since 1.0.6
	 */
	public static function register_custom_sidebars(){
	
		// Register custom Sidebars
		$sidebars = get_option('esg-widget-areas', false);
		
		if(is_array($sidebars) && !empty($sidebars)){
			foreach($sidebars as $handle => $name){
				register_sidebar(
					array (
						'name'          => $name,
						'id'            => 'eg-'.$handle,
						'before_widget' => '',
						'after_widget'  => ''
					)
				);
			}
		}
	}
	
	
	/**
	 * Get all Grids in Database
	 */
	public static function get_essential_grids(){
		global $wpdb;
		
		$table_name = $wpdb->prefix . self::TABLE_GRID;
		$grids = $wpdb->get_results("SELECT * FROM $table_name");
		
		return $grids;
	}
	
	
	/**
	 * Get Grid by ID from Database
	 */
	public static function get_essential_grid_by_id($id = 0){
		global $wpdb;
		
		$id = intval($id);
		if($id == 0) return false;
		
		$table_name = $wpdb->prefix . self::TABLE_GRID;
		
		$grid = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
		
		if(!empty($grid)){
			$grid['postparams'] = @json_decode($grid['postparams'], true);
			$grid['params'] = @json_decode($grid['params'], true);
			$grid['layers'] = @json_decode($grid['layers'], true);
		}
		
		return $grid;
	}
	
	
	/**
	 * get array of id -> title
	 */		
	public static function get_grids_short($exceptID = null){
		$arrGrids = self::get_essential_grids();
		
		$arrShort = array();
		foreach($arrGrids as $grid){
			$id = $grid->id;
			$title = $grid->name;
			
			//filter by except
			if(!empty($exceptID) && $exceptID == $id)
				continue;
				
			$arrShort[$id] = $title;
		}
		
		return($arrShort);
	}
	
	
	/**
	 * get array of id -> handle
	 * @since 1.0.6
	 */		
	public static function get_grids_short_widgets($exceptID = null){
		$arrGrids = self::get_essential_grids();
		
		$arrShort = array();
		
		foreach($arrGrids as $grid){
			
			//filter by except
			if(!empty($exceptID) && $exceptID == $grid->id)
				continue;
				
			$arrShort[$grid->id] = $grid->handle;
		}
		
		return($arrShort);
	}
	
	
	/**
	 * get array of id -> title
	 */		
	public static function get_grids_short_vc($exceptID = null){
		$arrGrids = self::get_essential_grids();
		
		$arrShort = array();
		
		foreach($arrGrids as $grid){
			$alias = $grid->handle;
			$title = $grid->name;
			
			//filter by except
			if(!empty($exceptID) && $exceptID == $grid->id)
				continue;
				
			$arrShort[$title] = $alias;
		}
		
		return($arrShort);
	}
	
	
	/**
	 * Output Essential Grid in Page by alias
	 */		
	public function output_essential_grid_by_alias($eg_alias){
		global $wpdb;
		
		$table_name = $wpdb->prefix . self::TABLE_GRID;
		
		$grid = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s", $eg_alias), ARRAY_A);
		
		if(!empty($grid)){
			$this->output_essential_grid($grid['id']);
		}else{ return false; }
		
	}
	
	
    /**
	 * Init essential data by id
	 */	
    public function init_by_id($grid_id){
        global $wpdb;
		
		$table_name = $wpdb->prefix . self::TABLE_GRID;
		
		$grid = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $grid_id), ARRAY_A);
		
		if(empty($grid)) return false;
		
		$this->grid_id = @$grid['id'];
		$this->grid_name = @$grid['name'];
		$this->grid_handle = @$grid['handle'];
		$this->grid_postparams = @json_decode($grid['postparams'], true);
		$this->grid_params = @json_decode($grid['params'], true);
		if(!empty($grid['layers'])){
			$orig_layers = $grid['layers'];
			$grid['layers'] = @json_decode(stripslashes($orig_layers), true);
			if(empty($grid['layers']) || !is_array($grid['layers'])) $grid['layers'] = @json_decode($orig_layers, true);

			if(!empty($grid['layers'])){
				foreach($grid['layers'] as $key => $layer){
					$orig_layers_cur = $grid['layers'][$key];
					$grid['layers'][$key] = @json_decode($orig_layers_cur, true);
					if(empty($grid['layers'][$key]) || !is_array($grid['layers'][$key])) $grid['layers'][$key] = @json_decode(stripslashes($orig_layers_cur), true);
				}
			}
		}
        $this->grid_layers = @$grid['layers'];
		
		return true;
    }
	
	
    /**
	 * Init essential data by given data
	 */	
    public function init_by_data($grid_data){
        
		$this->grid_id = @$grid_data['id'];
		$this->grid_name = @$grid_data['name'];
		$this->grid_handle = @$grid_data['handle'];
        $this->grid_postparams = @$grid_data['postparams'];
        $this->grid_params = @$grid_data['params'];
		
		if(!empty($grid_data['layers'])){
			foreach($grid_data['layers'] as $key => $layer){
				$grid_data['layers'][$key] = @json_decode(stripslashes($grid_data['layers'][$key]), true);
			}
		}
        $this->grid_layers = @$grid_data['layers'];
		
		return true;
    }
	
	
    /**
	 * Init essential data by id
	 */	
    public function set_loading_ids($ids){
        
		$this->filter_by_ids = $ids;
		
    }
	
	
    /**
	 * Init essential data by id
	 */	
    public function is_custom_grid(){
        
		if(isset($this->grid_postparams['source-type']) && $this->grid_postparams['source-type'] == 'custom')
			return true;
		else
			return false;
		
    }
    
    
	/**
	 * Output Essential Grid in Page
	 */		
	public function output_essential_grid($grid_id, $data = array(), $grid_preview = false){
		try{
		
			if($grid_preview){
				$data['id'] = $grid_id;
				$init = $this->init_by_data($data);
				if(!$init) return false; //be silent
			}else{
				$init = $this->init_by_id($grid_id);
				if(!$init) return false; //be silent
				Essential_Grid_Global_Css::output_global_css_styles_wrapped();
			}
			
			switch($this->grid_postparams['source-type']){
				case 'post':
					$this->output_by_posts($grid_preview);
				break;
				case 'custom':
					$this->output_by_custom($grid_preview);
				break;
				case 'streams':
				break;
			}
		
		}catch(Exception $e){
			$message = $e->getMessage();
			echo $message;
		}
	}
    
	
	/**
	 * Output by custom grid
	 */
	public function output_by_custom($grid_preview = false){
		$post_limit = 99999;
		
		$base = new Essential_Grid_Base();
		$navigation_c = new Essential_Grid_Navigation();
		$item_skin = new Essential_Grid_Item_Skin();
		$item_skin->grid_id = $this->grid_id;
		$item_skin->set_grid_type($base->getVar($this->grid_params, 'layout','even'));
		
		$m = new Essential_Grid_Meta();
		
		$skins_html = '';
		$skins_css = '';
		$filters = array();
		
		$rows_unlimited = $base->getVar($this->grid_params, 'rows-unlimited', 'on');
		$load_more = $base->getVar($this->grid_params, 'load-more', 'none');
		$load_more_start = $base->getVar($this->grid_params, 'load-more-start', 3, 'i');
		
		if($rows_unlimited == 'on' && $load_more !== 'none' && $grid_preview == false){ //grid_preview means disable load more in preview
			$post_limit = $load_more_start;
		}
		
		$nav_filters = array();
		
		$nav_layout = $base->getVar($this->grid_params, 'navigation-layout', array());
		$nav_skin = $base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
		$hover_animation = $base->getVar($this->grid_params, 'hover-animation', 'fade');
		$filter_allow = $base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_all_text = $base->getVar($this->grid_params, 'filter-all-text', __('Filter - All', EG_TEXTDOMAIN));
		
		$nav_type = $base->getVar($this->grid_params, 'nagivation-type', 'internal');
		$do_nav = ($nav_type == 'internal') ? true : false;
		
		$order_by = explode(',', $base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if(!is_array($order_by)) $order_by = array($order_by);
		$order_by_start = $base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		
		$module_spacings = $base->getVar($this->grid_params, 'module-spacings', '5');
		
		$top_1_align = $base->getVar($this->grid_params, 'top-1-align', 'center');
		$top_2_align = $base->getVar($this->grid_params, 'top-2-align', 'center');
		$bottom_1_align = $base->getVar($this->grid_params, 'bottom-1-align', 'center');
		$bottom_2_align = $base->getVar($this->grid_params, 'bottom-2-align', 'center');
		
		$top_1_margin = $base->getVar($this->grid_params, 'top-1-margin-bottom', 0, 'i');
		$top_2_margin = $base->getVar($this->grid_params, 'top-2-margin-bottom', 0, 'i');
		$bottom_1_margin = $base->getVar($this->grid_params, 'bottom-1-margin-top', 0, 'i');
		$bottom_2_margin = $base->getVar($this->grid_params, 'bottom-2-margin-top', 0, 'i');
		
		$left_margin = $base->getVar($this->grid_params, 'left-margin-left', 0, 'i');
		$right_margin = $base->getVar($this->grid_params, 'right-margin-right', 0, 'i');
		
		$nav_styles['top-1'] = array('margin-bottom' => $top_1_margin.'px', 'text-align' => $top_1_align);
		$nav_styles['top-2'] = array('margin-bottom' => $top_2_margin.'px', 'text-align' => $top_2_align);
		$nav_styles['left'] = array('margin-left' => $left_margin.'px');
		$nav_styles['right'] = array('margin-right' => $right_margin.'px');
		$nav_styles['bottom-1'] = array('margin-top' => $bottom_1_margin.'px', 'text-align' => $bottom_1_align);
		$nav_styles['bottom-2'] = array('margin-top' => $bottom_2_margin.'px', 'text-align' => $bottom_2_align);
		
		if($do_nav){ //only do if internal is selected
			$navigation_c->set_filter_text($filter_all_text);
			$navigation_c->set_specific_styles($nav_styles);
			
			$navigation_c->set_layout($nav_layout); //set the layout
			
			$navigation_c->set_orders($order_by); //set order of filter
			$navigation_c->set_orders_start($order_by_start); //set order of filter
		}
        $item_skin->init_by_id($base->getVar($this->grid_params, 'entry-skin', 0, 'i'));
		
		$lazy_load = $base->getVar($this->grid_params, 'lazy-loading', 'off');
		if($lazy_load == 'on')
			$item_skin->set_lazy_load(true);
		
        $default_media_source_order = $base->getVar($this->grid_postparams, 'media-source-order', '');
		$item_skin->set_default_media_source_order($default_media_source_order);
		
        $default_lightbox_source_order = $base->getVar($this->grid_params, 'lb-source-order', '');
		$item_skin->set_default_lightbox_source_order($default_lightbox_source_order);
		
		$post_media_source_type = $base->getVar($this->grid_postparams, 'image-source-type', 'full');
		
		$default_video_poster_order = $base->getVar($this->grid_params, 'poster-source-order', '');
		$item_skin->set_default_video_poster_order($default_video_poster_order);
		
		$layout = $base->getVar($this->grid_params, 'layout','even');
        $layout_sizing = $base->getVar($this->grid_params, 'layout-sizing', 'boxed');
		
		if($layout_sizing !== 'fullwidth' && $layout == 'masonry'){
			$item_skin->set_poster_cropping(true);
		}
		
		$skins_css = '';
		$skins_html = '';
		
		$found_filter = array();
		$i = 1;
		
		if(!empty($this->grid_layers) && count($this->grid_layers) > 0){
			foreach($this->grid_layers as $key => $entry){
				
				$post_media_source_data = $base->get_custom_media_source_data($entry, $post_media_source_type);
				$post_video_ratios = $m->get_custom_video_ratios($entry);
				$filters = array();
				
				if(is_array($order_by) && !empty($order_by)){
					//$sort = $this->prepare_sorting_array_by_post($post, $order_by);
					//$item_skin->set_sorting($sort);
				}
				if(!empty($entry['custom-filter'])){
					$cats = explode(',', $entry['custom-filter']);
					if(!is_array($cats)) $cats = (array)$cats;
					foreach($cats as $key => $category){
						$filters[sanitize_key($category)] = array('name' => $category, 'slug' => sanitize_key($category));
					}
				}
				
				$found_filter = $found_filter + $filters; //these are the found filters, only show filter that the posts have
				
				if($i > $post_limit){
					$this->load_more_post_array[$key] = $filters; //set for load more, only on elements that will not be loaded from beginning
					continue; //Load only selected numbers of items at start (for load more)
				}
				$i++;
				
				$item_skin->set_filter($filters);
				$item_skin->set_media_sources($post_media_source_data);
				$item_skin->set_media_sources_type($post_media_source_type);
				$item_skin->set_video_ratios($post_video_ratios);
				$item_skin->set_layer_values($entry);
				
				ob_start();
				$item_skin->output_item_skin($grid_preview);
				$skins_html.= ob_get_contents();
				ob_clean();
				ob_end_clean();
				
			}
		}
		
		if($grid_preview !== false){ //add the add more box at the end
			ob_start();
			$item_skin->output_add_more();
			$skins_html.= ob_get_contents();
			ob_clean();
			ob_end_clean();
		}
		
		if($do_nav){ //only do if internal is selected
			$navigation_c->set_filter($found_filter); //set filters $nav_filters $found_filter
			$navigation_c->set_filter_type($filter_allow);
		}
		
		ob_start();
		$item_skin->generate_element_css($grid_preview);
		$skins_css.= ob_get_contents();
		ob_clean();
		ob_end_clean();
		
		if($do_nav){ //only do if internal is selected
			$navigation_skin = $base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
			ob_start();
			$navigation_c->output_navigation_skin($navigation_skin);
			$nav_css = ob_get_contents();
			ob_clean();
			ob_end_clean();
			
			echo $nav_css;
		}
		echo $skins_css;
		
		$this->output_wrapper_pre($grid_preview);
		
		if($do_nav){ //only do if internal is selected
			$navigation_c->output_layout('top-1', $module_spacings);
			$navigation_c->output_layout('top-2', $module_spacings);
		}
		
		$this->output_grid_pre();
		
		echo $skins_html;
		
		$this->output_grid_post();
		
		if($do_nav){ //only do if internal is selected
			$navigation_c->output_layout('bottom-1', $module_spacings);
			$navigation_c->output_layout('bottom-2', $module_spacings);
			$navigation_c->output_layout('left');
			$navigation_c->output_layout('right');
		}
		
		$this->output_wrapper_post();
		
		$load_lightbox = $item_skin->do_lightbox_loading();
		
		if(!$grid_preview)
			$this->output_grid_javascript($load_lightbox);
	}
	
	
	/**
	 * Output by posts
	 */
	public function output_by_posts($grid_preview = false){
		
		$post_limit = 99999;
		
		$base = new Essential_Grid_Base();
		$navigation_c = new Essential_Grid_Navigation();
		$item_skin = new Essential_Grid_Item_Skin();
		$item_skin->grid_id = $this->grid_id;
		$item_skin->set_grid_type($base->getVar($this->grid_params, 'layout','even'));
		
		$m = new Essential_Grid_Meta();
		
		$skins_html = '';
		$skins_css = '';
		$filters = array();
		
		$rows_unlimited = $base->getVar($this->grid_params, 'rows-unlimited', 'on');
		$load_more = $base->getVar($this->grid_params, 'load-more', 'none');
		$load_more_start = $base->getVar($this->grid_params, 'load-more-start', 3, 'i');
		
		if($rows_unlimited == 'on' && $load_more !== 'none' && $grid_preview == false){ //grid_preview means disable load more in preview
			$post_limit = $load_more_start;
		}
		
		$start_sortby = $base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		$start_sortby_type = $base->getVar($this->grid_params, 'sorting-order-type', 'ASC');
		
		$post_category = $base->getVar($this->grid_postparams, 'post_category');
		$post_types = $base->getVar($this->grid_postparams, 'post_types');
		$page_ids = explode(',', $base->getVar($this->grid_postparams, 'selected_pages', '-1'));
		
		$additional_query = $base->getVar($this->grid_postparams, 'additional-query', '');
		if($additional_query !== '')
			$additional_query = wp_parse_args($additional_query);
		
		$cat_tax = Essential_Grid_Base::getCatAndTaxData($post_category);
		
		$posts = Essential_Grid_Base::getPostsByCategory($cat_tax['cats'], $post_types, $cat_tax['tax'], $page_ids, $start_sortby, $start_sortby_type, -1, $additional_query);
		
		$nav_filters = array();
		
		$taxes = array('post_tag');
		if(!empty($cat_tax['tax']))
			$taxes = explode(',', $cat_tax['tax']);
			
		if(!empty($cat_tax['cats'])){
			$cats = explode(',', $cat_tax['cats']);
			
			foreach($cats as $key => $id){
				$cat = get_category($id);
				if(is_object($cat))	$nav_filters[$id] = array('name' => $cat->cat_name, 'slug' => sanitize_key($cat->slug));
				
				foreach($taxes as $custom_tax){
					$term = get_term_by('id', $id, $custom_tax);
					if(is_object($term)) $nav_filters[$id] = array('name' => $term->name, 'slug' => sanitize_key($term->slug));
				}
			}
			
			asort($nav_filters);
		}
		
		$nav_layout = $base->getVar($this->grid_params, 'navigation-layout', array());
		$nav_skin = $base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
		$hover_animation = $base->getVar($this->grid_params, 'hover-animation', 'fade');
		$filter_allow = $base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_all_text = $base->getVar($this->grid_params, 'filter-all-text', __('Filter - All', EG_TEXTDOMAIN));
		
		$nav_type = $base->getVar($this->grid_params, 'nagivation-type', 'internal');
		$do_nav = ($nav_type == 'internal') ? true : false;
		
		$order_by = explode(',', $base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if(!is_array($order_by)) $order_by = array($order_by);
		$order_by_start = $base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		
		$module_spacings = $base->getVar($this->grid_params, 'module-spacings', '5');
		
		$top_1_align = $base->getVar($this->grid_params, 'top-1-align', 'center');
		$top_2_align = $base->getVar($this->grid_params, 'top-2-align', 'center');
		$bottom_1_align = $base->getVar($this->grid_params, 'bottom-1-align', 'center');
		$bottom_2_align = $base->getVar($this->grid_params, 'bottom-2-align', 'center');
		
		$top_1_margin = $base->getVar($this->grid_params, 'top-1-margin-bottom', 0, 'i');
		$top_2_margin = $base->getVar($this->grid_params, 'top-2-margin-bottom', 0, 'i');
		$bottom_1_margin = $base->getVar($this->grid_params, 'bottom-1-margin-top', 0, 'i');
		$bottom_2_margin = $base->getVar($this->grid_params, 'bottom-2-margin-top', 0, 'i');
		
		$left_margin = $base->getVar($this->grid_params, 'left-margin-left', 0, 'i');
		$right_margin = $base->getVar($this->grid_params, 'right-margin-right', 0, 'i');
		
		$nav_styles['top-1'] = array('margin-bottom' => $top_1_margin.'px', 'text-align' => $top_1_align);
		$nav_styles['top-2'] = array('margin-bottom' => $top_2_margin.'px', 'text-align' => $top_2_align);
		$nav_styles['left'] = array('margin-left' => $left_margin.'px');
		$nav_styles['right'] = array('margin-right' => $right_margin.'px');
		$nav_styles['bottom-1'] = array('margin-top' => $bottom_1_margin.'px', 'text-align' => $bottom_1_align);
		$nav_styles['bottom-2'] = array('margin-top' => $bottom_2_margin.'px', 'text-align' => $bottom_2_align);
		
		if($do_nav){ //only do if internal is selected
			$navigation_c->set_filter_text($filter_all_text);
			$navigation_c->set_specific_styles($nav_styles);
			
			$navigation_c->set_layout($nav_layout); //set the layout
			
			$navigation_c->set_orders($order_by); //set order of filter
			$navigation_c->set_orders_start($order_by_start); //set order of filter
		}
		
        $item_skin->init_by_id($base->getVar($this->grid_params, 'entry-skin', 0, 'i'));
		
		$lazy_load = $base->getVar($this->grid_params, 'lazy-loading', 'off');
		if($lazy_load == 'on')
			$item_skin->set_lazy_load(true);
		
        $default_media_source_order = $base->getVar($this->grid_postparams, 'media-source-order', '');
		$item_skin->set_default_media_source_order($default_media_source_order);
		
        $default_lightbox_source_order = $base->getVar($this->grid_params, 'lb-source-order', '');
		$item_skin->set_default_lightbox_source_order($default_lightbox_source_order);
		
		$post_media_source_type = $base->getVar($this->grid_postparams, 'image-source-type', 'full');
		
		$default_video_poster_order = $base->getVar($this->grid_params, 'poster-source-order', '');
		$item_skin->set_default_video_poster_order($default_video_poster_order);
		
		$layout = $base->getVar($this->grid_params, 'layout','even');
        $layout_sizing = $base->getVar($this->grid_params, 'layout-sizing', 'boxed');
		
		if($layout_sizing !== 'fullwidth' && $layout == 'masonry'){
			$item_skin->set_poster_cropping(true);
		}
		
		$skins_css = '';
		$skins_html = '';
		
		$found_filter = array();
		$i = 1;
		
		if(!empty($posts) && count($posts) > 0){
			foreach($posts as $key => $post){
				if($grid_preview == false){
					//check if post should be visible or if its invisible on current grid settings
					$is_visible = $this->check_if_visible($post['ID'], $this->grid_id);
					if($is_visible == false) continue; // continue if invisible
				}
				
				$post_media_source_data = $base->get_post_media_source_data($post['ID'], $post_media_source_type);
				$post_video_ratios = $m->get_post_video_ratios($post['ID']);
				$filters = array();
				
				//$categories = get_the_category($post['ID']);
				$categories = $base->get_custom_taxonomies_by_post_id($post['ID']);
				//$tags = wp_get_post_terms($post['ID']);
				$tags = get_the_tags($post['ID']);
				
				if(!empty($categories)){
					foreach($categories as $key => $category){
						$filters[$category->term_id] = array('name' => $category->name, 'slug' => sanitize_key($category->slug));
					}
				}
				
				if(!empty($tags)){
					foreach($tags as $key => $taxonomie){
						$filters[$taxonomie->term_id] = array('name' => $taxonomie->name, 'slug' => sanitize_key($taxonomie->slug));
					}
				}
				
				if(is_array($order_by) && !empty($order_by)){
					$sort = $this->prepare_sorting_array_by_post($post, $order_by);
					$item_skin->set_sorting($sort);
				}
				
				$found_filter = $found_filter + $filters; //these are the found filters, only show filter that the posts have
				
				if($i > $post_limit){
					$this->load_more_post_array[$post['ID']] = $filters; //set for load more, only on elements that will not be loaded from beginning
					continue; //Load only selected numbers of items at start (for load more)
				}
				$i++;
				
				$item_skin->set_filter($filters);
				$item_skin->set_media_sources($post_media_source_data);
				$item_skin->set_media_sources_type($post_media_source_type);
				$item_skin->set_video_ratios($post_video_ratios);
				$item_skin->set_post_values($post);
				
				ob_start();
				$item_skin->output_item_skin($grid_preview);
				$skins_html.= ob_get_contents();
				ob_clean();
				ob_end_clean();
				
			}
		}else{
			return false;
		}
		
		$remove_filter = array_diff_key($nav_filters, $found_filter); //check if we have filter that no post has (comes through multilanguage)
		if(!empty($remove_filter)){
			foreach($remove_filter as $key => $rem){ //we have, so remove them from the filter list before setting the filter list
				unset($nav_filters[$key]);
			}
		}
		
		if($do_nav){ //only do if internal is selected
			$navigation_c->set_filter($nav_filters); //set filters $nav_filters $found_filter
			$navigation_c->set_filter_type($filter_allow);
		}
		
		ob_start();
		$item_skin->generate_element_css();
		$skins_css.= ob_get_contents();
		ob_clean();
		ob_end_clean();
		
		if($do_nav){ //only do if internal is selected
			$navigation_skin = $base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
			ob_start();
			$navigation_c->output_navigation_skin($navigation_skin);
			$nav_css = ob_get_contents();
			ob_clean();
			ob_end_clean();
			
			echo $nav_css;
		}
		
		echo $skins_css;
		
		$this->output_wrapper_pre($grid_preview);
		if($do_nav){ //only do if internal is selected
			$navigation_c->output_layout('top-1', $module_spacings);
			$navigation_c->output_layout('top-2', $module_spacings);
		}
		
		$this->output_grid_pre();
		
		echo $skins_html;
		
		$this->output_grid_post();
		if($do_nav){ //only do if internal is selected
			$navigation_c->output_layout('bottom-1', $module_spacings);
			$navigation_c->output_layout('bottom-2', $module_spacings);
			$navigation_c->output_layout('left');
			$navigation_c->output_layout('right');
		}
		
		$this->output_wrapper_post();
		
		$load_lightbox = $item_skin->do_lightbox_loading();
		
		if(!$grid_preview)
			$this->output_grid_javascript($load_lightbox);
	}
    
	
	/**
	 * Output by specific posts for load more
	 */
	public function output_by_specific_posts(){

		$base = new Essential_Grid_Base();
		$item_skin = new Essential_Grid_Item_Skin();
		$item_skin->grid_id = $this->grid_id;
		$item_skin->set_grid_type($base->getVar($this->grid_params, 'layout','even'));
		
		$m = new Essential_Grid_Meta();
		
		if(!empty($this->filter_by_ids)){
			$posts = Essential_Grid_Base::get_posts_by_ids($this->filter_by_ids);
		}else{
			return false;
		}
		
        $item_skin->init_by_id($base->getVar($this->grid_params, 'entry-skin', 0, 'i'));
		$order_by = explode(',', $base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if(!is_array($order_by)) $order_by = array($order_by);
		
		
		$lazy_load = $base->getVar($this->grid_params, 'lazy-loading', 'off');
		if($lazy_load == 'on')
			$item_skin->set_lazy_load(true);
		
        $default_media_source_order = $base->getVar($this->grid_postparams, 'media-source-order', '');
		$item_skin->set_default_media_source_order($default_media_source_order);
		
		$default_lightbox_source_order = $base->getVar($this->grid_params, 'lb-source-order', '');
		$item_skin->set_default_lightbox_source_order($default_lightbox_source_order);
		
		$post_media_source_type = $base->getVar($this->grid_postparams, 'image-source-type', 'full');
		
		$default_video_poster_order = $base->getVar($this->grid_params, 'poster-source-order', '');
		$item_skin->set_default_video_poster_order($default_video_poster_order);
		
		$layout = $base->getVar($this->grid_params, 'layout','even');
        $layout_sizing = $base->getVar($this->grid_params, 'layout-sizing', 'boxed');
		
		if($layout_sizing !== 'fullwidth' && $layout == 'masonry'){
			$item_skin->set_poster_cropping(true);
		}
		
		$skins_html = '';
		
		if(!empty($posts) && count($posts) > 0){
			foreach($posts as $key => $post){
				//check if post should be visible or if its invisible on current grid settings
				$is_visible = $this->check_if_visible($post['ID'], $this->grid_id);
				
				if($is_visible == false) continue; // continue if invisible
				
				$post_media_source_data = $base->get_post_media_source_data($post['ID'], $post_media_source_type);
				$post_video_ratios = $m->get_post_video_ratios($post['ID']);
				
				$filters = array();
				
				//$categories = get_the_category($post['ID']);
				$categories = $base->get_custom_taxonomies_by_post_id($post['ID']);
				//$tags = wp_get_post_terms($post['ID']);
				$tags = get_the_tags($post['ID']);
				
				if(!empty($categories)){
					foreach($categories as $key => $category){
						$filters[$category->term_id] = array('name' => $category->name, 'slug' => sanitize_key($category->slug));
					}
				}
				
				if(!empty($tags)){
					foreach($tags as $key => $taxonomie){
						$filters[$taxonomie->term_id] = array('name' => $taxonomie->name, 'slug' => sanitize_key($taxonomie->slug));
					}
				}
				
				if(is_array($order_by) && !empty($order_by)){
					$sort = $this->prepare_sorting_array_by_post($post, $order_by);
					$item_skin->set_sorting($sort);
				}
				
				$item_skin->set_filter($filters);
				$item_skin->set_media_sources($post_media_source_data);
				$item_skin->set_media_sources_type($post_media_source_type);
				$item_skin->set_video_ratios($post_video_ratios);
				$item_skin->set_post_values($post);
				$item_skin->set_load_more();
				
				
				ob_start();
				$item_skin->output_item_skin();
				$skins_html.= ob_get_contents();
				ob_clean();
				ob_end_clean();
				
			}
		}else{
			return false;
		}
		
		return $skins_html;
		
	}
    
	
	/**
	 * Output by specific ids for load more custom grid
	 */
	public function output_by_specific_ids(){
		
		$base = new Essential_Grid_Base();
		$navigation_c = new Essential_Grid_Navigation();
		$item_skin = new Essential_Grid_Item_Skin();
		$item_skin->grid_id = $this->grid_id;
		$item_skin->set_grid_type($base->getVar($this->grid_params, 'layout','even'));
		
		$m = new Essential_Grid_Meta();
		
		$filters = array();
		
		$rows_unlimited = $base->getVar($this->grid_params, 'rows-unlimited', 'on');
		$load_more = $base->getVar($this->grid_params, 'load-more', 'none');
		$load_more_start = $base->getVar($this->grid_params, 'load-more-start', 3, 'i');
		
		
		$nav_filters = array();
		
		$nav_layout = $base->getVar($this->grid_params, 'navigation-layout', array());
		$nav_skin = $base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
		$hover_animation = $base->getVar($this->grid_params, 'hover-animation', 'fade');
		$filter_allow = $base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_all_text = $base->getVar($this->grid_params, 'filter-all-text', __('Filter - All', EG_TEXTDOMAIN));
		
		$order_by = explode(',', $base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if(!is_array($order_by)) $order_by = array($order_by);
		$order_by_start = $base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		
		$module_spacings = $base->getVar($this->grid_params, 'module-spacings', '5');
		
		$top_1_align = $base->getVar($this->grid_params, 'top-1-align', 'center');
		$top_2_align = $base->getVar($this->grid_params, 'top-2-align', 'center');
		$bottom_1_align = $base->getVar($this->grid_params, 'bottom-1-align', 'center');
		$bottom_2_align = $base->getVar($this->grid_params, 'bottom-2-align', 'center');
		
		$top_1_margin = $base->getVar($this->grid_params, 'top-1-margin-bottom', 0, 'i');
		$top_2_margin = $base->getVar($this->grid_params, 'top-2-margin-bottom', 0, 'i');
		$bottom_1_margin = $base->getVar($this->grid_params, 'bottom-1-margin-top', 0, 'i');
		$bottom_2_margin = $base->getVar($this->grid_params, 'bottom-2-margin-top', 0, 'i');
		
		$left_margin = $base->getVar($this->grid_params, 'left-margin-left', 0, 'i');
		$right_margin = $base->getVar($this->grid_params, 'right-margin-right', 0, 'i');
		
		$nav_styles['top-1'] = array('margin-bottom' => $top_1_margin.'px', 'text-align' => $top_1_align);
		$nav_styles['top-2'] = array('margin-bottom' => $top_2_margin.'px', 'text-align' => $top_2_align);
		$nav_styles['left'] = array('margin-left' => $left_margin.'px');
		$nav_styles['right'] = array('margin-right' => $right_margin.'px');
		$nav_styles['bottom-1'] = array('margin-top' => $bottom_1_margin.'px', 'text-align' => $bottom_1_align);
		$nav_styles['bottom-2'] = array('margin-top' => $bottom_2_margin.'px', 'text-align' => $bottom_2_align);
		
		
        $item_skin->init_by_id($base->getVar($this->grid_params, 'entry-skin', 0, 'i'));
		
		$lazy_load = $base->getVar($this->grid_params, 'lazy-loading', 'off');
		if($lazy_load == 'on')
			$item_skin->set_lazy_load(true);
		
        $default_media_source_order = $base->getVar($this->grid_postparams, 'media-source-order', '');
		$item_skin->set_default_media_source_order($default_media_source_order);
		
        $default_lightbox_source_order = $base->getVar($this->grid_params, 'lb-source-order', '');
		$item_skin->set_default_lightbox_source_order($default_lightbox_source_order);
		
		$post_media_source_type = $base->getVar($this->grid_postparams, 'image-source-type', 'full');
		
		$default_video_poster_order = $base->getVar($this->grid_params, 'poster-source-order', '');
		$item_skin->set_default_video_poster_order($default_video_poster_order);
		
		$layout = $base->getVar($this->grid_params, 'layout','even');
        $layout_sizing = $base->getVar($this->grid_params, 'layout-sizing', 'boxed');
		
		if($layout_sizing !== 'fullwidth' && $layout == 'masonry'){
			$item_skin->set_poster_cropping(true);
		}
		
		$skins_html = '';
		
		$found_filter = array();
		
		if(!empty($this->grid_layers) && count($this->grid_layers) > 0){
			foreach($this->grid_layers as $key => $entry){
				
				if(!in_array($key, $this->filter_by_ids)) continue;
			
				$post_media_source_data = $base->get_custom_media_source_data($entry, $post_media_source_type);
				$post_video_ratios = $m->get_custom_video_ratios($entry);
				$filters = array();
				
				if(is_array($order_by) && !empty($order_by)){
					//$sort = $this->prepare_sorting_array_by_post($post, $order_by);
					//$item_skin->set_sorting($sort);
				}
				if(!empty($entry['custom-filter'])){
					$cats = explode(',', $entry['custom-filter']);
					if(!is_array($cats)) $cats = (array)$cats;
					foreach($cats as $key => $category){
						$filters[sanitize_key($category)] = array('name' => $category, 'slug' => sanitize_key($category));
					}
				}
				
				$found_filter = $found_filter + $filters; //these are the found filters, only show filter that the posts have
				
				$item_skin->set_filter($filters);
				$item_skin->set_media_sources($post_media_source_data);
				$item_skin->set_media_sources_type($post_media_source_type);
				$item_skin->set_video_ratios($post_video_ratios);
				$item_skin->set_layer_values($entry);
				$item_skin->set_load_more();
				
				ob_start();
				$item_skin->output_item_skin();
				$skins_html.= ob_get_contents();
				ob_clean();
				ob_end_clean();
				
			}
		}else{
			return false;
		}
		
		return $skins_html;
		
	}
	
	
	public function prepare_sorting_array_by_post($post, $order_by){
		$base = new Essential_Grid_Base();
		
		$sorts = array();
		foreach($order_by as $order){
			switch($order){
				case 'date':
					$sorts['date'] = strtotime($base->getVar($post, 'post_date'));
				break;
				case 'title':
					$sorts['title'] = substr($base->getVar($post, 'post_title', ''), 0, 10);
				break;
				case 'excerpt':
					$sorts['excerpt'] = substr(strip_tags($base->getVar($post, 'post_excerpt', '')), 0, 10);
				break;
				case 'id':
					$sorts['id'] = $base->getVar($post, 'ID');
				break;
				case 'slug':
					$sorts['slug'] = $base->getVar($post, 'post_name');
				break;
				case 'author':
					$authorID = $base->getVar($post, 'post_author');
					$sorts['author'] = get_the_author_meta('display_name', $authorID);
				break;
				case 'last-modified':
					$sorts['last-modified'] = strtotime($base->getVar($post, 'post_modified'));
				break;
				case 'number-of-comments':
					$sorts['number-of-comments'] = $base->getVar($post, 'comment_count');
				break;
				case 'random':
					$sorts['random'] = rand(0,9999);
				break;
			}
		}
		
		//add woocommerce sortings
		if(Essential_Grid_Woocommerce::is_woo_exists()){
			$product = get_product($post['ID']);
			if(!empty($product)){
				foreach($order_by as $order){
					switch($order){
						case 'meta_num_total_sales':
							$sorts['total-sales'] = get_post_meta($post['ID'],$order,true);
						break;
						case 'meta_num__regular_price':
							$sorts['regular-price'] = $product->get_price();
						break;
						//case 'meta_num__sale_price':
						//	$sorts['sale-price'] = $product->get_sale_price();
						//break;
						case 'meta__featured':
							$sorts['featured'] = ($product->is_featured()) ? '1' : '0';
						break;
						case 'meta__sku':
							$sorts['sku'] = $product->get_sku();
						break;
						case 'meta_num_stock':
							$sorts['in-stock'] = $product->get_stock_quantity();
						break;
					}
				}
			}
		}
		
		return $sorts;
	}
	
	
    public function output_wrapper_pre($grid_preview = false){
        
        $base = new Essential_Grid_Base();
        
        $this->grid_serial++;
        
		$grid_id = ($grid_preview !== false) ? 'esg-preview-grid' : 'esg-grid-'.$this->grid_id.'-'.$this->grid_serial;
		$article_id = ($grid_preview !== false) ? ' id="esg-preview-skinlevel"' : '';
		
        $hide_markup_before_load = $base->getVar($this->grid_params, 'hide-markup-before-load', 'on');
        $background_color = $base->getVar($this->grid_params, 'main-background-color', 'transparent');
        $navigation_skin = $base->getVar($this->grid_params, 'navigation-skin', 'minimal-light');
        $paddings = $base->getVar($this->grid_params, 'grid-padding', 0);
		
		$pad_style = '';
		
		if(is_array($paddings) && !empty($paddings)){
			$pad_style = 'padding: ';
			foreach($paddings as $size){
				$pad_style .= $size.'px ';
			}
			$pad_style .= ';';
			
			$pad_style .= ' box-sizing:border-box;';
			$pad_style .= ' -moz-box-sizing:border-box;';
			$pad_style .= ' -webkit-box-sizing:border-box;';
		}
		
		$div_style = ' style="';
		$div_style.= 'background-color: '.$background_color.';';
		$div_style.= $pad_style;
		if($hide_markup_before_load == 'on')
			$div_style.= ' display: none;';
			
		$div_style.= '"';
        
        echo '<!-- THE ESSENTIAL GRID '. self::VERSION .' -->'."\n\n";
        
		echo '<!-- GRID WRAPPER FOR CONTAINER SIZING - HERE YOU CAN SET THE CONTAINER SIZE AND CONTAINER SKIN -->'."\n";
		echo '<article class="myportfolio-container '.$navigation_skin.'"'.$article_id.'>'."\n\n"; //fullwidthcontainer-with-padding 
        
        echo '    <!-- THE GRID ITSELF WITH FILTERS, PAGINATION, SORTING ETC... -->'."\n";
		echo '    <div id="'.$grid_id.'" class="esg-grid"'.$div_style.'>'."\n";
        
    }
    
    
    public function output_wrapper_post(){
        
        echo '    </div><!-- END OF THE GRID -->'."\n\n";

		echo '</article>'."\n";
		echo '<!-- END OF THE GRID WRAPPER -->'."\n\n";

		echo '<div class="clear"></div>'."\n";
        
    }
    
    
    public function output_grid_pre(){
        
        echo '<!-- ############################ -->'."\n";
        echo '<!-- THE GRID ITSELF WITH ENTRIES -->'."\n";
        echo '<!-- ############################ -->'."\n";
        echo '<ul>'."\n";
        
    }
    
    
    public function output_grid_post(){
        
        echo '</ul>'."\n";
        echo '<!-- ############################ -->'."\n";
        echo '<!--      END OF THE GRID         -->'."\n";
        echo '<!-- ############################ -->'."\n";
        
    }
    
    
    public function output_grid_javascript($load_lightbox = false){
        
        $base = new Essential_Grid_Base();
        
        $layout = $base->getVar($this->grid_params, 'layout','even');
        $force_full_width = $base->getVar($this->grid_params, 'force_full_width', 'off');
        
        $rows_unlimited = $base->getVar($this->grid_params, 'rows-unlimited', 'on');
        $load_more_type = $base->getVar($this->grid_params, 'load-more', 'on');
		$rows = $base->getVar($this->grid_params, 'rows', 4, 'i');
		
        $columns = $base->getVar($this->grid_params, 'columns', '');
        $columns = $base->set_basic_colums($columns);
        
        $columns_advanced = $base->getVar($this->grid_params, 'columns-advanced', 'off');
        if($columns_advanced == 'on')
            $columns_width = $base->getVar($this->grid_params, 'columns-width', '');
        else
            $columns_width = array(); //get defaults
        
        $columns_width = $base->set_basic_colums_width($columns_width);
        
        $space = $base->getVar($this->grid_params, 'spacings', 0, 'i');
        $page_animation = $base->getVar($this->grid_params, 'grid-animation', 'scale');
        $anim_speed = $base->getVar($this->grid_params, 'grid-animation-speed', 800, 'i');
        $delay_basic = $base->getVar($this->grid_params, 'grid-animation-delay', 1, 'i');
        $delay_hover = $base->getVar($this->grid_params, 'hover-animation-delay', 1, 'i');
		
        $lightbox_mode = $base->getVar($this->grid_params, 'lightbox-mode', 'single');
		
        $layout_sizing = $base->getVar($this->grid_params, 'layout-sizing', 'boxed');
        $layout_offset_container = $base->getVar($this->grid_params, 'fullscreen-offset-container', '');
        
        $aspect_ratio_x = $base->getVar($this->grid_params, 'x-ratio', 4, 'i');
        $aspect_ratio_y = $base->getVar($this->grid_params, 'y-ratio', 3, 'i');
        
        $lazy_load = $base->getVar($this->grid_params, 'lazy-loading', 'off');
        $lazy_load_color = $base->getVar($this->grid_params, 'lazy-load-color', '#FFFFFF');
		
		$spinner = $base->getVar($this->grid_params, 'use-spinner', '0');
		$spinner_color = $base->getVar($this->grid_params, 'spinner-color', '#FFFFFF');
		
		
		//LIGHTBOX VARIABLES
		$usetwitter = $base->getVar($this->grid_params, 'lightbox-twitter','off');
		$usefacebook = $base->getVar($this->grid_params, 'lightbox-facebook','off');		
		$lightbox_title_type = $base->getVar($this->grid_params, 'lightbox-type', "null");
		$lightbox_position = $base->getVar($this->grid_params, 'lightbox-position', 'bottom');		
		
		$lightbox_effect_open_close = $base->getVar($this->grid_params, 'lightbox-effect-open-close', 'fade');		
		$lightbox_effect_next_prev = $base->getVar($this->grid_params, 'lightbox-effect-next-prev', 'fade');		
		$lightbox_effect_open_close_speed = $base->getVar($this->grid_params, 'lightbox-effect-open-close-speed', 'normal');		
		$lightbox_effect_next_prev_speed = $base->getVar($this->grid_params, 'lightbox-effect-next-prev-speed', 'normal');		
		
		$lightbox_arrows = $base->getVar($this->grid_params, 'lightbox-arrows', 'on'); 
		$lightbox_thumbs = $base->getVar($this->grid_params, 'lightbox-thumbs', 'off');
		$lightbox_thumbs_w = $base->getVar($this->grid_params, 'lbox-thumb-w', '50');		
		$lightbox_thumbs_h = $base->getVar($this->grid_params, 'lbox-thumb-h', '50');
		
		$linebreak = '\'<br />\'';	
		$twitteraddon = '\'<a href="https://twitter.com/share" class="twitter-share-button" data-count="none" data-url="\'+this.href+\'">'.__('Tweet', EG_TEXTDOMAIN).'</a>\'';
		$facebookaddon = '\'<iframe src="//www.facebook.com/plugins/like.php?href=\'+this.href+\'&amp;layout=button_count&amp;show_faces=true&amp;width=500&amp;action=like&amp;font&amp;colorscheme=light&amp;height=23" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:110px; height:23px;" allowTransparency="true"></iframe>\'';

		$lbox_padding = $base->getVar($this->grid_params, 'lbox-padding', array('0','0','0','0'));
		$lbox_inpadding = $base->getVar($this->grid_params, 'lbox-inpadding', array('0','0','0','0'));		
		
		$wait_for_fonts = get_option('tp_eg_wait_for_fonts', 'true');
		
        echo '<script type="text/javascript">'."\n";
        echo 'var essapi_'.$this->grid_id.';'."\n";
        echo 'jQuery(document).ready(function() {'."\n";
        echo '	essapi_'.$this->grid_id.' = jQuery("#esg-grid-'.$this->grid_id.'-'.$this->grid_serial.'").tpessential({'."\n";
        echo '        layout:"'.$layout.'",'."\n";
        echo '        forceFullWidth:"'.$force_full_width.'",'."\n";
        echo '        lazyLoad:"'.$lazy_load.'",'."\n";
		if($lazy_load == 'on')
			echo '        lazyLoadColor:"'.$lazy_load_color.'",'."\n";
		
        if($rows_unlimited == 'on'){
			$load_more		  = $base->getVar($this->grid_params, 'load-more', 'button');
			$load_more_amount = $base->getVar($this->grid_params, 'load-more-amount', 3, 'i');
			
			if($load_more !== 'none'){
				$token = wp_create_nonce('Essential_Grid_Front');
				echo '        gridID:"'.$this->grid_id.'",'."\n";
				echo '        loadMoreAjaxUrl:"'.admin_url('admin-ajax.php').'",'."\n";
				echo '        loadMoreAjaxToken:"'.$token.'",'."\n";
				echo '        loadMoreAjaxAction:"Essential_Grid_Front_request_ajax",'."\n";
				echo '        loadMoreType:"'.$load_more.'",'."\n";
				echo '        loadMoreAmount:'.$load_more_amount.','."\n";
				echo '        loadMoreTxt:"'.__('Load More', EG_TEXTDOMAIN).'",'."\n";
				echo '        loadMoreEndTxt:"'.__('No More Item for Selected Filter', EG_TEXTDOMAIN).'",'."\n";   
				echo '        loadMoreItems:';
				$this->output_load_more_list();
				echo ','."\n";
			}
			echo '        row:9999,'."\n";
        }else{
			echo '        row:'.$rows.','."\n";
		}
        echo '        space:'.$space.','."\n";
        echo '        pageAnimation:"'.$page_animation.'",'."\n";
		
        echo '        spinner:"spinner'.$spinner.'",'."\n";
		
		if($spinner != '0' && $spinner != '5')
			echo '        spinnerColor:"'.$spinner_color.'",'."\n";
		
        if($layout_sizing == 'fullwidth'){
			echo '        forceFullWidth:"on",'."\n";
		}elseif($layout_sizing == 'fullscreen'){
			echo '        forceFullScreen:"on",'."\n";
			if($layout_offset_container !== ''){
				echo '        fullScreenOffsetContainer:"'.$layout_offset_container.'",'."\n";
			}
		}
		
        echo '        lightBoxMode:"'.$lightbox_mode.'",'."\n";
		
        echo '        animSpeed:'.$anim_speed.','."\n";
        echo '        delayBasic:'.$delay_basic.','."\n";
        echo '        mainhoverdelay:'.$delay_hover.','."\n";
		
		if($wait_for_fonts === 'true'){
			$tf_fonts = new ThemePunch_Fonts();
			$fonts = $tf_fonts->get_all_fonts();
			if(!empty($fonts)){
				$first = true;
				$font_string = '[';
				foreach($fonts as $font){
					if($first === false) $font_string.= ',';
					$font_string.= "'".esc_attr($font['url'])."'";
					$first = false;
				}
				$font_string.= ']';
				echo '        googleFonts:'.$font_string.','."\n";
			}
		}
		
        if($layout != 'masonry'){
            echo '        aspectratio:"'.$aspect_ratio_x.':'.$aspect_ratio_y.'",'."\n";
        }
        echo '        responsiveEntries: ['."\n";
        echo '						{ width:'.$columns_width['0'].',amount:'.$columns['0'].'},'."\n";
        echo '						{ width:'.$columns_width['1'].',amount:'.$columns['1'].'},'."\n";		
		echo '						{ width:'.$columns_width['2'].',amount:'.$columns['2'].'},'."\n";
		echo '						{ width:'.$columns_width['3'].',amount:'.$columns['3'].'},'."\n";
		echo '						{ width:'.$columns_width['4'].',amount:'.$columns['4'].'},'."\n";
		echo '						{ width:'.$columns_width['5'].',amount:'.$columns['5'].'},'."\n";
		echo '						{ width:'.$columns_width['6'].',amount:'.$columns['6'].'}'."\n";
        echo '						]';
		
		if($columns_advanced == 'on')
			$this->output_ratio_list();
		
		echo "\n";
		
		
		

        echo '	});'."\n\n";
		
		//check if lightbox is active
		if($load_lightbox) {
			echo '	jQuery("#esg-grid-'.$this->grid_id.'-'.$this->grid_serial.' .esgbox").esgbox({'."\n";
			echo '		padding : ['.$lbox_padding[0].','.$lbox_padding[1].','.$lbox_padding[2].','.$lbox_padding[3].'],'."\n";
			echo '		beforeShow : function () { '."\n";
			echo '			this.title = jQuery(this.element).attr(\'lgtitle\')'."\n";
			echo '			if (this.title) {'."\n";
			if ($lightbox_title_type=="null") 
				echo '				this.title="";'."\n";
			if ($usetwitter=="on" || $usefacebook=="on")
				echo '				this.title += '.$linebreak.';'."\n";
			if ($usetwitter=="on")
				echo '				this.title += '.$twitteraddon.';'."\n";
			if ($usefacebook=="on")
				echo '				this.title += '.$facebookaddon.';'."\n";
			
			echo '   		this.title =  \'<div style="padding:'.$lbox_inpadding[0].'px '.$lbox_inpadding[1].'px '.$lbox_inpadding[2].'px '.$lbox_inpadding[3].'px">\'+this.title+\'</div>\';'."\n";										
			echo '			}'."\n";															

			echo '		},'."\n";
			
			echo '		afterShow : function() {'."\n";			
			
			if ($usetwitter=="on")
				echo '			twttr.widgets.load();'."\n";
			echo '		},'."\n";
			echo '		openEffect : \''.$lightbox_effect_open_close.'\','."\n";		
			echo '		closeEffect : \''.$lightbox_effect_open_close.'\','."\n";		
			echo '		nextEffect : \''.$lightbox_effect_next_prev.'\','."\n";		
			echo '		prevEffect : \''.$lightbox_effect_next_prev.'\','."\n";											
			echo '		openSpeed : \''.$lightbox_effect_open_close_speed.'\','."\n";		
			echo '		closeSpeed : \''.$lightbox_effect_open_close_speed.'\','."\n";		
			echo '		nextSpeed : \''.$lightbox_effect_next_prev_speed.'\','."\n";		
			echo '		prevSpeed : \''.$lightbox_effect_next_prev_speed.'\','."\n";	
			if ($lightbox_arrows=="off")
				echo '		arrows : false,'."\n";													
			echo '		helpers : {'."\n";
			echo '			media : {},'."\n";
			if ($lightbox_thumbs == "on") {
				echo '			thumbs: {'."\n";
				echo '				width : '.$lightbox_thumbs_w.','."\n";
				echo '				height : '.$lightbox_thumbs_h."\n";			
				echo '			},'."\n";			
			}
			echo '		    title : {'."\n";
			if ($lightbox_title_type!="null") 
				echo '				type:"'.$lightbox_title_type.'",'."\n";
			else
				echo '				type:""'."\n";
			if ($lightbox_title_type!="null") 
				echo '				position:"'.$lightbox_position.'",'."\n";			
			echo '			}'."\n";
			
			echo '		}'."\n";
			echo '});'."\n"."\n";
		}		
		
		//output custom javascript if any is set
		$custom_javascript = stripslashes($base->getVar($this->grid_params, 'custom-javascript', ''));
		if($custom_javascript !== ''){
			echo $custom_javascript;
		}
		echo '});'."\n";
		echo '</script>'."\n";
		
        
    }
	
	
	/**
	 * Output the Load More list of posts
	 */
	public function output_load_more_list(){
		
		if(!empty($this->load_more_post_array)){
			$wrap_first = true;
			echo '[';
			
			foreach($this->load_more_post_array as $id => $filter){
				echo (!$wrap_first) ? ','."\n" : "\n";
				
				echo '				['.$id.', [-1, ';
				
				if(!empty($filter)){
					$slug_first = true;
					foreach($filter as $slug_id => $slug){
						echo (!$slug_first) ? ', ' : '';
						
						echo $slug_id;
						
						$slug_first = false;
					}
				}
				
				echo ']]';
				
				$wrap_first = false;
			}
			
			echo ']';
		}else{
			echo '[]';
		}
	}
	
	
	/**
	 * Output the custom row sizes if its set
	 */
	public function output_ratio_list(){
		$base = new Essential_Grid_Base;
		
		$columns = $base->getVar($this->grid_params, 'columns', ''); //this is the first line
        $columns = $base->set_basic_colums($columns);
		
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-0', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-1', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-2', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-3', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-4', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-5', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-6', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-7', '');
		$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-8', '');
		//$columns_advanced[] = $base->getVar($this->grid_params, 'columns-advanced-rows-9', '');
		
		$found_rows = 0;
		foreach($columns_advanced as $adv_key => $adv){
			if(empty($adv)) continue;
			$found_rows++;
		}
		
		if($found_rows > 0){
			echo ','."\n";
			echo '		rowItemMultiplier: ['."\n";
			
			echo '						[';
			echo $columns[0].',';
			echo $columns[1].',';
			echo $columns[2].',';
			echo $columns[3].',';
			echo $columns[4].',';
			echo $columns[5].',';
			echo $columns[6];
			echo ']';
			
			foreach($columns_advanced as $adv_key => $adv){
				if(empty($adv)) continue;
				
				echo ','."\n";
				echo '						[';
				
				$entry_first = true;
				foreach($adv as $val){
					echo (!$entry_first) ? ',' : '';
					echo $val;
					$entry_first = false;
				}
				
				echo ']';
			}
			
			echo "\n".'						]';
		}
	}
	
	
	/**
	 * check if post is visible in grid
	 */
	public function check_if_visible($post_id, $grid_id){
		$pr_visibility = json_decode(get_post_meta($post_id, 'eg_visibility', true), true);
		
		$is_visible = true;
		
		if(!empty($pr_visibility) && is_array($pr_visibility)){ //check if element is visible in grid
			foreach($pr_visibility as $pr_grid => $pr_setting){
				if($pr_grid == $grid_id){
					if($pr_setting == false)
						$is_visible = false;
					else
						$is_visible = true;
					break;
				}
			}
		}
		
		return $is_visible;
	}
	
	
	/**
	 * Output Filter from current Grid (used for Widgets)
	 * @since 1.0.6
	 */
	public function output_grid_filter(){
		
		switch($this->grid_postparams['source-type']){
			case 'post':
				$this->output_filter_by_posts();
			break;
			case 'custom':
				$this->output_filter_by_custom();
			break;
			case 'streams':
			break;
		}
		
	}
	
	
	/**
	 * Output Sorting from current Grid (used for Widgets)
	 * @since 1.0.6
	 */
	public function output_grid_sorting(){
		
		switch($this->grid_postparams['source-type']){
			case 'post':
				$this->output_sorting_by_posts();
			break;
			case 'custom':
				$this->output_sorting_by_custom();
			break;
			case 'streams':
			break;
		}
		
	}
	
	
	/**
	 * Output Sorting from post based
	 * @since 1.0.6
	 */
	public function output_sorting_by_posts(){
		$this->output_sorting_by_all_types();
	}
	
	
	/**
	 * Output Sorting from custom grid
	 * @since 1.0.6
	 */
	public function output_sorting_by_custom(){
		$this->output_sorting_by_all_types();
	}
	
	
	/**
	 * Output Sorting from custom grid
	 * @since 1.0.6
	 */
	public function output_sorting_by_all_types(){
		$base = new Essential_Grid_Base();
		$nav = new Essential_Grid_Navigation();

		$order_by = explode(',', $base->getVar($this->grid_params, 'sorting-order-by', 'date'));
		if(!is_array($order_by)) $order_by = array($order_by);

		$order_by_start = $base->getVar($this->grid_params, 'sorting-order-by-start', 'none');

		$nav->set_orders($order_by); //set order of filter
		$nav->set_orders_start($order_by_start); //set order of filter
		
		$nav->output_sorting();
	}
	
	
	/**
	 * Output Filter from post based
	 * @since 1.0.6
	 */
	public function output_filter_by_posts(){
		$base = new Essential_Grid_Base();
		$nav = new Essential_Grid_Navigation();
		
		$filter_allow = $base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_all_text = $base->getVar($this->grid_params, 'filter-all-text', __('Filter - All', EG_TEXTDOMAIN));

		$nav->set_filter_text($filter_all_text);

		$start_sortby = $base->getVar($this->grid_params, 'sorting-order-by-start', 'none');
		$start_sortby_type = $base->getVar($this->grid_params, 'sorting-order-type', 'ASC');

		$post_category = $base->getVar($this->grid_postparams, 'post_category');
		$post_types = $base->getVar($this->grid_postparams, 'post_types');
		$page_ids = explode(',', $base->getVar($this->grid_postparams, 'selected_pages', '-1'));

		$additional_query = $base->getVar($this->grid_postparams, 'additional-query', '');
		if($additional_query !== '')
			$additional_query = wp_parse_args($additional_query);

		$cat_tax = Essential_Grid_Base::getCatAndTaxData($post_category);

		$posts = Essential_Grid_Base::getPostsByCategory($cat_tax['cats'], $post_types, $cat_tax['tax'], $page_ids, $start_sortby, $start_sortby_type, -1, $additional_query);

		$nav_filters = array();

		$taxes = array('post_tag');
		if(!empty($cat_tax['tax']))
			$taxes = explode(',', $cat_tax['tax']);
			
		if(!empty($cat_tax['cats'])){
			$cats = explode(',', $cat_tax['cats']);
			
			foreach($cats as $key => $id){
				$cat = get_category($id);
				if(is_object($cat))	$nav_filters[$id] = array('name' => $cat->cat_name, 'slug' => sanitize_key($cat->slug));
				
				foreach($taxes as $custom_tax){
					$term = get_term_by('id', $id, $custom_tax);
					if(is_object($term)) $nav_filters[$id] = array('name' => $term->name, 'slug' => sanitize_key($term->slug));
				}
			}
			
			asort($nav_filters);
		}


		$found_filter = array();
		if(!empty($posts) && count($posts) > 0){
			foreach($posts as $key => $post){
				//check if post should be visible or if its invisible on current grid settings
				$is_visible = $this->check_if_visible($post['ID'], $this->grid_id);
				if($is_visible == false) continue; // continue if invisible
				
				$filters = array();
				
				//$categories = get_the_category($post['ID']);
				$categories = $base->get_custom_taxonomies_by_post_id($post['ID']);
				//$tags = wp_get_post_terms($post['ID']);
				$tags = get_the_tags($post['ID']);
				
				if(!empty($categories)){
					foreach($categories as $key => $category){
						$filters[$category->term_id] = array('name' => $category->name, 'slug' => sanitize_key($category->slug));
					}
				}
				
				if(!empty($tags)){
					foreach($tags as $key => $taxonomie){
						$filters[$taxonomie->term_id] = array('name' => $taxonomie->name, 'slug' => sanitize_key($taxonomie->slug));
					}
				}
				
				$found_filter = $found_filter + $filters; //these are the found filters, only show filter that the posts have
			}
		}

		$remove_filter = array_diff_key($nav_filters, $found_filter); //check if we have filter that no post has (comes through multilanguage)
		if(!empty($remove_filter)){
			foreach($remove_filter as $key => $rem){ //we have, so remove them from the filter list before setting the filter list
				unset($nav_filters[$key]);
			}
		}

		$nav->set_filter($nav_filters); //set filters $nav_filters $found_filter
		$nav->set_filter_type($filter_allow);
		
		
		$nav->output_filter();
		
	}
	
	
	/**
	 * Output Filter from custom grid
	 * @since 1.0.6
	 */
	public function output_filter_by_custom(){
		$base = new Essential_Grid_Base();
		$nav = new Essential_Grid_Navigation();
		
		$filter_allow = $base->getVar($this->grid_params, 'filter-arrows', 'single');
		$filter_all_text = $base->getVar($this->grid_params, 'filter-all-text', __('Filter - All', EG_TEXTDOMAIN));

		$nav->set_filter_text($filter_all_text);

		$found_filter = array();

		if(!empty($this->grid_layers) && count($this->grid_layers) > 0){
			foreach($this->grid_layers as $key => $entry){
				$filters = array();
				
				if(!empty($entry['custom-filter'])){
					$cats = explode(',', $entry['custom-filter']);
					if(!is_array($cats)) $cats = (array)$cats;
					foreach($cats as $key => $category){
						$filters[sanitize_key($category)] = array('name' => $category, 'slug' => sanitize_key($category));
						
						$found_filter = $found_filter + $filters; //these are the found filters, only show filter that the posts have
						
					}
				}
			}
		}
		
		$nav->set_filter($found_filter); //set filters $nav_filters $found_filter
		$nav->set_filter_type($filter_allow);
		
		$nav->output_filter();
		
	}
	
	
	/**
	 * Handle Ajax Requests
	 */
	public static function on_front_ajax_action(){
	
		$base = new Essential_Grid_Base();
		
		$token = $base->getPostVar("token", false);
		
		//verify the token
		$isVerified = wp_verify_nonce($token, 'Essential_Grid_Front');
		
		$error = false;
		
		if($isVerified){
			$data = $base->getPostVar('data', false);
			//client_action: load_more_items
			switch($base->getPostVar('client_action', false)){
				case 'load_more_items':
					$gridid = $base->getPostVar('gridid', 0, 'i');
					if(!empty($data) && $gridid > 0){
						$grid = new Essential_Grid();
						
						$result = $grid->init_by_id($gridid);
						if(!$result){
							$error = __('Grid not found', EG_TEXTDOMAIN);
						}else{
							$grid->set_loading_ids($data); //set to only load choosen items
							$html = false;
							//check if we are custom grid
							if($grid->is_custom_grid()){
								$html = $grid->output_by_specific_ids();
							}else{
								$html = $grid->output_by_specific_posts();
							}
							
							
							if($html !== false){
								self::ajaxResponseData($html);
							}else{
								$error = __('Items Not Found', EG_TEXTDOMAIN);
							}
						}
					}else{
						$error = __('No Data Received', EG_TEXTDOMAIN);
					}
				break;
			}
			
		}else{
			$error = true;
		}
		
		if($error !== false){
			$showError = __('Loading Error', EG_TEXTDOMAIN);
			if($error !== true)
				$showError = __('Loading Error: ', EG_TEXTDOMAIN).$error;
			
			self::ajaxResponseError($showError, false);
		}
		exit();
	}
	
	
	
	/**
	 * echo json ajax response
	 */
	public static function ajaxResponse($success,$message,$arrData = null){
		
		$response = array();
		$response["success"] = $success;				
		$response["message"] = $message;

		if(!empty($arrData)){
			
			if(gettype($arrData) == "string" || gettype($arrData) == "boolean")
				$arrData = array("data"=>$arrData);				
			
			$response = array_merge($response,$arrData);
		}
			
		$json = json_encode($response);
		
		echo $json;
		exit();
	}

	
	/**
	 * echo json ajax response, without message, only data
	 */
	public static function ajaxResponseData($arrData){
		if(gettype($arrData) == "string")
			$arrData = array("data"=>$arrData);
		
		self::ajaxResponse(true,"",$arrData);
	}
	
	
	/**
	 * echo json ajax response
	 */
	public static function ajaxResponseError($message,$arrData = null){
		
		self::ajaxResponse(false,$message,$arrData,true);
	}
	
	
	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccess($message,$arrData = null){
		
		self::ajaxResponse(true,$message,$arrData,true);
		
	}
	
	
	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccessRedirect($message,$url){
		$arrData = array("is_redirect"=>true,"redirect_url"=>$url);
		
		self::ajaxResponse(true,$message,$arrData,true);
	}
	
}