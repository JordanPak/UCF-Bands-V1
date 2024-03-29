<?php
/**
 * Essential Grid.
 *
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */

/**
 * @package Essential_Grid_Admin
 * @author  ThemePunch <info@themepunch.com>
 */
class Essential_Grid_Admin extends Essential_Grid_Base {

	const ROLE_ADMIN = "admin";
	const ROLE_EDITOR = "editor";
	const ROLE_AUTHOR = "author";
	
	const VIEW_START = "grid";
	const VIEW_OVERVIEW = "grid-overview";
	const VIEW_GRID_CREATE = "grid-create";
	const VIEW_GRID = "grid-details";
	const VIEW_META_BOX = "grid-meta-box";
	const VIEW_ITEM_SKIN_EDITOR = "grid-item-skin-editor";
	const VIEW_GOOGLE_FONTS = "themepunch-google-fonts";
	const VIEW_IMPORT_EXPORT = "grid-import-export";
	const VIEW_WIDGET_AREAS = "grid-widget-areas";
	
	const VIEW_SUB_ITEM_SKIN_OVERVIEW = "grid-item-skin";
	const VIEW_SUB_CUSTOM_META = "grid-custom-meta";
	const VIEW_SUB_CUSTOM_META_AJAX = "custom-meta";
	const VIEW_SUB_WIDGET_AREA_AJAX = "widget-areas";
	
	protected static $view;
	
	/**
	 * Instance of this class.
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	private static $menuRole = self::ROLE_ADMIN;
	
	
	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 */
	public function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = Essential_Grid::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		
		self::addAllSettings();
		
		$role = get_option('tp_eg_role', self::ROLE_ADMIN);
		
		self::setMenuRole($role); //set to setting that user chose
		
		// Load admin style sheet and JavaScript.
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_language'));
		
		// Add the options page and menu item.
		add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
		
		// Add the meta box to post/pages
		add_action('registered_post_type', array($this, 'prepare_add_plugin_meta_box'), 10, 2);
		add_action('save_post', array($this, 'add_plugin_meta_box_save'));
		
		add_action('wp_ajax_Essential_Grid_request_ajax', array($this, 'on_ajax_action'));
		
		$validated = get_option('tp_eg_valid', 'false');
		$notice = get_option('tp_eg_valid-notice', 'true');
		
		
		if($validated === 'false' && $notice === 'true'){
			add_action('admin_notices', array($this, 'add_activate_notification'));	
		}
		
		$upgrade = new Essential_Grid_Update( Essential_Grid::VERSION );

		$upgrade->_retrieve_version_info();
		
		if($validated === 'true') {
			$upgrade->add_update_checks();
		}
		
	}
	
	
	/**
	 * show notification message if plugin is not activated
	 */
	public function add_activate_notification(){
		$token = wp_create_nonce('Essential_Grid_actions');
		$base = new Essential_Grid();
		?>
		<div class="updated below-h2 eg-update-notice-wrap" style="margin-left: 0;" id="message"><a href="javascript:void(0);" style="float: right;" id="eg-dismiss-notice">×</a><p><?php _e('Hi! Please activate your copy of the Essential Grid to receive automatic updates & get premium support.', EG_TEXTDOMAIN); ?></p></div>
		<script type="text/javascript">
			jQuery('#eg-dismiss-notice').click(function(){
				var objData = {
					action: 'Essential_Grid_request_ajax',
					client_action: 'dismiss_notice',
					token: '<?php echo $token; ?>',
					data: ''
				};
				
				jQuery.ajax({
					type:'post',
					url:ajaxurl,
					dataType:'json',
					data:objData
				});
				
				jQuery('.eg-update-notice-wrap').hide();
			});
		</script>
		<?php
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
	 * Register and enqueue admin-specific style sheet.
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset($this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if(in_array($screen->id, $this->plugin_screen_hook_suffix)) {
			wp_enqueue_style(array('wp-jquery-ui', 'wp-jquery-ui-core', 'wp-jquery-ui-dialog', 'wp-color-picker'));
			
			wp_enqueue_style($this->plugin_slug .'-admin-styles', EG_PLUGIN_URL . 'admin/assets/css/admin.css', array(), Essential_Grid::VERSION );
            
			wp_enqueue_style($this->plugin_slug .'-codemirror-styles', EG_PLUGIN_URL . 'admin/assets/css/codemirror.css', array(), Essential_Grid::VERSION );

			wp_enqueue_style($this->plugin_slug .'-tooltipser-styles', EG_PLUGIN_URL . 'admin/assets/css/tooltipster.css', array(), Essential_Grid::VERSION );			
            
			wp_register_style($this->plugin_slug . '-plugin-settings', EG_PLUGIN_URL . 'public/assets/css/settings.css', array(), Essential_Grid::VERSION);
			wp_enqueue_style($this->plugin_slug . '-plugin-settings' );
			
			wp_register_style('themepunchboxextcss', EG_PLUGIN_URL . 'public/assets/css/lightbox.css', array(), Essential_Grid::VERSION);
			
			$font = new ThemePunch_Fonts();
			$font->register_fonts();
		}
		
		wp_enqueue_style($this->plugin_slug .'-global-styles', EG_PLUGIN_URL . 'admin/assets/css/global.css', array(), Essential_Grid::VERSION );

	}

	
	/**
	 * Register and enqueue admin-specific JavaScript.
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		
		if ( ! isset($this->plugin_screen_hook_suffix ) ) {
			return;
		}
		
		$screen = get_current_screen();
		if(in_array($screen->id, $this->plugin_screen_hook_suffix)) {
			wp_enqueue_script(array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-slider', 'jquery-ui-autocomplete', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-ui-tabs', 'wp-color-picker'));
			
			wp_register_script( 'themepunchboxext', EG_PLUGIN_URL . 'public/assets/js/lightbox.js', array('jquery'), Essential_Grid::VERSION);
			
			wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__ ), array('jquery', 'wp-color-picker'), Essential_Grid::VERSION );
            
			wp_enqueue_script($this->plugin_slug . '-codemirror-script', plugins_url('assets/js/codemirror.js', __FILE__ ), array('jquery'), Essential_Grid::VERSION );			
			wp_enqueue_script($this->plugin_slug . '-codemirror-css-script', plugins_url('assets/js/mode/css.js', __FILE__ ), array('jquery', $this->plugin_slug . '-codemirror-script'), Essential_Grid::VERSION );
			wp_enqueue_script($this->plugin_slug . '-codemirror-js-script', plugins_url('assets/js/mode/javascript.js', __FILE__ ), array('jquery', $this->plugin_slug . '-codemirror-script'), Essential_Grid::VERSION );
			
			wp_enqueue_script($this->plugin_slug . '-tooltipser-script', plugins_url('assets/js/jquery.tooltipster.min.js', __FILE__ ), array('jquery'), Essential_Grid::VERSION );			
			
			wp_enqueue_script( 'themepunchtools', plugins_url( '../public/assets/js/jquery.themepunch.plugins.min.js', __FILE__ ), array('jquery'), Essential_Grid::VERSION );
			wp_enqueue_script( $this->plugin_slug . '-essential-grid-script', plugins_url( '../public/assets/js/jquery.themepunch.essential.min.js', __FILE__ ), array('jquery'), Essential_Grid::VERSION );
			
			wp_enqueue_media();
		}
		
		//enqueue in all pages / posts in backend
		$post_types = get_post_types( '', 'names' ); 
		foreach($post_types as $post_type) {
			if($post_type == $screen->id) wp_enqueue_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/admin.js', __FILE__ ), array('jquery', 'wp-color-picker'), Essential_Grid::VERSION );
			if($post_type == $screen->id) wp_enqueue_script($this->plugin_slug . '-tooltipser-script', plugins_url('assets/js/jquery.tooltipster.min.js', __FILE__ ), array('jquery'), Essential_Grid::VERSION );
			if($post_type == $screen->id) wp_enqueue_media();
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript Language.
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts_language() {
		if ( ! isset($this->plugin_screen_hook_suffix ) ) {
			return;
		}
		
		$screen = get_current_screen();
		if(in_array($screen->id, $this->plugin_screen_hook_suffix)) {
			wp_localize_script($this->plugin_slug . '-admin-script', 'eg_lang', self::get_javascript_multilanguage()); //Load multilanguage for JavaScript
		}
		
		//enqueue in all pages / posts in backend
		$post_types = get_post_types( '', 'names' ); 
		foreach($post_types as $post_type)
			if($post_type == $screen->id) wp_localize_script($this->plugin_slug . '-admin-script', 'eg_lang', self::get_javascript_multilanguage()); //Load multilanguage for JavaScript

	}

	
	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function add_plugin_admin_menu() {

		$role = self::getPluginPermission();
		switch(self::$menuRole){
			case self::ROLE_AUTHOR:
				$role = "edit_published_posts";
			break;
			case self::ROLE_EDITOR:
				$role = "edit_pages";
			break;		
			default:		
			case self::ROLE_ADMIN:
				$role = "manage_options";
			break;
		}
		
		$this->plugin_screen_hook_suffix[] = add_menu_page(__('Essential Grid', EG_TEXTDOMAIN ),__('Ess. Grid', EG_TEXTDOMAIN ),$role,$this->plugin_slug,array($this, 'display_plugin_admin_page'),'dashicons-screenoptions');
		
		if(!isset($GLOBALS['admin_page_hooks']['themepunch-google-fonts'])) //only add if menu is not already registered
			$this->plugin_screen_hook_suffix[] = add_menu_page(__('Punch Fonts', EG_TEXTDOMAIN), __('Punch Fonts', EG_TEXTDOMAIN), $role, 'themepunch-google-fonts', array($this, 'display_plugin_submenu_page_google_fonts'), 'dashicons-editor-textcolor');
		
		$this->plugin_screen_hook_suffix[] = add_submenu_page($this->plugin_slug, __('Item Skin Editor', EG_TEXTDOMAIN), __('Item Skin Editor', EG_TEXTDOMAIN), $role, $this->plugin_slug.'-item-skin', array($this, 'display_plugin_submenu_page_item_skin'));
		$this->plugin_screen_hook_suffix[] = add_submenu_page($this->plugin_slug, __('Custom Meta', EG_TEXTDOMAIN), __('Custom Meta', EG_TEXTDOMAIN), $role, $this->plugin_slug.'-custom-meta', array($this, 'display_plugin_submenu_page_custom_meta'));
		
		/* //ToDo Widget part
		$this->plugin_screen_hook_suffix[] = add_submenu_page($this->plugin_slug, __('Widget Areas', EG_TEXTDOMAIN), __('Widget Areas', EG_TEXTDOMAIN), $role, $this->plugin_slug.'-widget-areas', array($this, 'display_plugin_submenu_page_widget_areas'));
		*/
		
		$this->plugin_screen_hook_suffix[] = add_submenu_page($this->plugin_slug, __('Import/Export', EG_TEXTDOMAIN), __('Import/Export', EG_TEXTDOMAIN), $role, $this->plugin_slug.'-import-export', array($this, 'display_plugin_submenu_page_import_export'));

	}
	
	
	/**
	 * prepare the meta box inclusion if right post_type (includes all custom post types
	 */
	public static function prepare_add_plugin_meta_box($post_type){
		if($post_type !== 'attachment' &&
		   $post_type !== 'revision' &&
		   $post_type !== 'nav_menu_item'
		   ){
			add_action('add_meta_boxes', array(self::$instance, 'add_plugin_meta_box'), $post_type, 1);
		}
	}
	
	
	/**
	 * Register the meta box in post / pages
	 */
	public function add_plugin_meta_box($post_type) {
		add_meta_box('eg-meta-box', __('Essential Grid Custom Settings', EG_TEXTDOMAIN), array(self::$instance, 'display_plugin_meta_box'), $post_type, 'normal', 'high');
	}
	
	
	/**
	 * Display the meta box
	 */
	public static function display_plugin_meta_box($post){
		require_once('views/elements/'.self::VIEW_META_BOX.'.php');
	}
	
	
	/**
	 * Register the meta box save in post / pages
	 */
	public function add_plugin_meta_box_save($post_id) {
	
		// Bail if we're doing an auto save
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		self::custom_meta_box_save($post_id, $_POST);
		
	}
	
	
	/**
	 * Include wanted page
	 */
	public static function custom_meta_box_save($post_id, $metas, $ajax = false){
		
		// if our nonce isn't there, or we can't verify it, bail
		if(!isset($metas['essential_grid_meta_box_nonce']) || !wp_verify_nonce($metas['essential_grid_meta_box_nonce'], 'eg_meta_box_nonce')) return;
		
		if(isset($metas['eg_sources_html5_mp4']))
			update_post_meta($post_id, 'eg_sources_html5_mp4', esc_attr($metas['eg_sources_html5_mp4']));
			
		if(isset($metas['eg_sources_html5_ogv']))
			update_post_meta($post_id, 'eg_sources_html5_ogv', esc_attr($metas['eg_sources_html5_ogv']));
			
		if(isset($metas['eg_sources_html5_webm']))
			update_post_meta($post_id, 'eg_sources_html5_webm', esc_attr($metas['eg_sources_html5_webm']));
			
		if(isset($metas['eg_sources_youtube']))
			update_post_meta($post_id, 'eg_sources_youtube', esc_attr($metas['eg_sources_youtube']));
			
		if(isset($metas['eg_sources_vimeo']))
			update_post_meta($post_id, 'eg_sources_vimeo', esc_attr($metas['eg_sources_vimeo']));
			
		if(isset($metas['eg_sources_image']))
			update_post_meta($post_id, 'eg_sources_image', esc_attr($metas['eg_sources_image']));
			
		if(isset($metas['eg_sources_iframe']))
			update_post_meta($post_id, 'eg_sources_iframe', esc_attr($metas['eg_sources_iframe']));
		
		if(isset($metas['eg_sources_soundcloud']))
			update_post_meta($post_id, 'eg_sources_soundcloud', esc_attr($metas['eg_sources_soundcloud']));
			
		if(isset($metas['eg_settings_type']))
			update_post_meta($post_id, 'eg_settings_type', esc_attr($metas['eg_settings_type']));
			
		if(isset($metas['eg_settings_custom_display']))
			update_post_meta($post_id, 'eg_settings_custom_display', esc_attr($metas['eg_settings_custom_display']));
			
		if(isset($metas['eg_vimeo_ratio']))
			update_post_meta($post_id, 'eg_vimeo_ratio', esc_attr($metas['eg_vimeo_ratio']));
		
		if(isset($metas['eg_youtube_ratio']))
			update_post_meta($post_id, 'eg_youtube_ratio', esc_attr($metas['eg_youtube_ratio']));
		
		if(isset($metas['eg_html5_ratio']))
			update_post_meta($post_id, 'eg_html5_ratio', esc_attr($metas['eg_html5_ratio']));
		
		if(isset($metas['eg_soundcloud_ratio']))
			update_post_meta($post_id, 'eg_soundcloud_ratio', esc_attr($metas['eg_soundcloud_ratio']));
		
		if($ajax === false){ //only update these if we are in post, not at ajax that comes from the plugin in preview mode
			/**
			 * Save Custom Meta Things that Modify Skins
			 **/
			if(isset($metas['eg-custom-meta-skin']))
				update_post_meta($post_id, 'eg_settings_custom_meta_skin', $metas['eg-custom-meta-skin']);
			else
				update_post_meta($post_id, 'eg_settings_custom_meta_skin', '');
				
			if(isset($metas['eg-custom-meta-element']))
				update_post_meta($post_id, 'eg_settings_custom_meta_element', $metas['eg-custom-meta-element']);
			else
				update_post_meta($post_id, 'eg_settings_custom_meta_element', '');
				
			if(isset($metas['eg-custom-meta-setting']))
				update_post_meta($post_id, 'eg_settings_custom_meta_setting', $metas['eg-custom-meta-setting']);
			else
				update_post_meta($post_id, 'eg_settings_custom_meta_setting', '');
				
			if(isset($metas['eg-custom-meta-style']))
				update_post_meta($post_id, 'eg_settings_custom_meta_style', $metas['eg-custom-meta-style']);
			else
				update_post_meta($post_id, 'eg_settings_custom_meta_style', '');
		
		}
		
		/**
		 * Save Custom Meta from Custom Meta Submenu
		 */
		$m = new Essential_Grid_Meta();
		
		$cmetas = $m->get_all_meta();
		
		if(!empty($cmetas)){
			foreach($cmetas as $meta){
				if(isset($metas['eg-'.$meta['handle']])){
					update_post_meta($post_id, 'eg-'.$meta['handle'], $metas['eg-'.$meta['handle']]);
				}
			}
		}
		
		if($ajax !== false) return true;
	}
	
	
	/**
	 * Include wanted page
	 */
	public function display_plugin_admin_page() {
		//set view
		self::$view = self::getGetVar("view");
		if(empty(self::$view))
			self::$view = self::VIEW_OVERVIEW;

        $add_folder = '';
		//require styles by view
		switch(self::$view){
			case self::VIEW_OVERVIEW:
			case self::VIEW_GRID_CREATE:
			case self::VIEW_GRID:
			break;
			case self::VIEW_ITEM_SKIN_EDITOR:
                $add_folder = 'elements/';
            break;
			default: //go back to default
				self::$view = self::VIEW_OVERVIEW; 
		}
		
		try{
			require_once('views/header.php');
			require_once('views/'.$add_folder.self::$view.'.php');
			require_once('views/footer.php');
		}catch (Exception $e){
			echo "<br><br>View ($view) Error: <b>".$e->getMessage()."</b>";			
		}
		
	}
	
	
	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_item_skin() {
		self::display_plugin_submenu('grid-item-skin');
	}
	
	
	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_custom_meta() {
		self::display_plugin_submenu('grid-custom-meta');
	}
	
	
	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_import_export() {
		self::display_plugin_submenu('grid-import-export');
	}
	
	
	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_google_fonts() {
		self::display_plugin_submenu('themepunch-google-fonts');
	}
	
	
	/**
	 * Include wanted submenu page
	 * Since 1.0.6
	 */
	public function display_plugin_submenu_page_widget_areas() {
		self::display_plugin_submenu('grid-widget-areas');
	}
	
	
	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu($subMenu){
		if(empty($subMenu))
			$subMenu = self::VIEW_SUB_ITEM_SKIN_OVERVIEW;
			
		//require styles by view
		switch($subMenu){
			case self::VIEW_SUB_ITEM_SKIN_OVERVIEW:
			break;
			case self::VIEW_SUB_CUSTOM_META:
			break;
			case self::VIEW_GOOGLE_FONTS:
			break;
			case self::VIEW_IMPORT_EXPORT:
			break;
			case self::VIEW_WIDGET_AREAS:
			break;
			default: //go back to default
				$subMenu = self::VIEW_SUB_ITEM_SKIN_OVERVIEW; 
		}
		
		try{
			require_once('views/header.php');
			require_once('views/'.$subMenu.'.php');
			require_once('views/footer.php');
		}catch (Exception $e){
			echo "<br><br>View ($subMenu) Error: <b>".$e->getMessage()."</b>";			
		}
	}
	
	
	/**
	 * Create Options that we need
	 */
	private function addAllSettings(){		
		add_option('tp_eg_role');
	}
	
	
	/**
	 * Set Menu Role
	 * @param    string    $role    set the role to this string.
	 */
	private function setMenuRole($role){
		self::$menuRole = $role;
		
	}
	
	
	/**
	 * Get Menu Role
	 * @return    string    $role    the current role
	 */
	public static function getPluginPermission(){
		switch(self::$menuRole){
			case self::ROLE_AUTHOR:
				$role = "edit_published_posts";
			break;
			case self::ROLE_EDITOR:
				$role = "edit_pages";
			break;		
			default:		
			case self::ROLE_ADMIN:
				$role = "manage_options";
			break;
		}
		
		return $role;
	}
	
	
	/**
	 * Get Menu Role
	 * @return    string    $role    the current role
	 */
	public static function getPluginPermissionValue(){
		switch(self::$menuRole){
			case self::ROLE_AUTHOR:
			case self::ROLE_EDITOR:
			case self::ROLE_ADMIN:
				break;
			default:		
				return self::ROLE_ADMIN;
				break;
		}
		
		return self::$menuRole;
	}
	
	
	/**
	 * Save Menu Role
	 * @return    boolean	true
	 */
	private static function savePluginPermission($newPermission){
		switch($newPermission){
			case self::ROLE_AUTHOR:
			case self::ROLE_EDITOR:
			case self::ROLE_ADMIN:
				break;
			default:	
				return false;
				break;
		}
		
		$permission = update_option('tp_eg_role', $newPermission);
		
		return true;
	}
	
	
	/**
	 * Allow for VC to use this plugin
	 */
	public static function visual_composer_include(){
		
		if(!function_exists("wpb_map")) return false;
		
		
		add_action( 'init', array('Essential_Grid_Admin', 'add_to_VC' ));
	}
	
	
	public static function add_to_VC() {
	
		$essential_grids_arr = Essential_Grid::get_grids_short_vc();
		
		wpb_map( array(
			'name'		=> __('Essential Grid', EG_TEXTDOMAIN),
			'base'		=> 'ess_grid',
			'class'		=> '',
			'icon'		=> 'icon-wpb-ess-grid',
			'description' => __('MultiPurpose Grid by ThemePunch', EG_TEXTDOMAIN),
			/*
			müssen die class noch mit icon verbinden -> 
			.wpb-layout-element-button .icon-wpb-ess-grid, 
			.wpb_Essential_Grid .wpb_element_wrapper  { background-image: url(../img/icons/money.png)}
			*/
			'category' => __('Content', EG_TEXTDOMAIN),
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => __('Essential Grid', EG_TEXTDOMAIN),
					'param_name' => 'alias',
					'value' => $essential_grids_arr ,
					'admin_label' => true,
					'description' => __('Select your Essential Grid', EG_TEXTDOMAIN)
				)
			)
		) );
	}
	
	
	/**
	 * Update/Create Grid
	 * @return    boolean	true
	 */
	private static function update_create_grid($data){
		global $wpdb;
		
		if(!isset($data['name']) || strlen($data['name']) < 2) return __('Title needs to have at least 2 characters', EG_TEXTDOMAIN);
		if(!isset($data['handle']) || strlen($data['handle']) < 2) return __('Alias needs to have at least 2 characters', EG_TEXTDOMAIN);
		if(!isset($data['params']) || empty($data['params'])) return __('No setting informations received!', EG_TEXTDOMAIN);
		
		if($data['postparams']['source-type'] == 'custom'){
			if(!isset($data['layers']) || empty($data['layers'])) return __('Please add at least one element in Custom Grid mode', EG_TEXTDOMAIN);
		}elseif($data['postparams']['source-type'] == 'post'){
			if(!isset($data['postparams']['post_types']) || empty($data['postparams']['post_types'])) return __('Please select a Post Type', EG_TEXTDOMAIN);
		}
		
		if(!isset($data['layers']) || empty($data['layers'])) $data['layers'] = array(); //this is only set if we are source-type custom
		
		if($data['postparams']['source-type'] == 'post'){
			if(isset($data['postparams']['post_types'])){
				$types = explode(',', $data['postparams']['post_types']);
				if(!in_array('page', (array) $types)){
					if(!isset($data['postparams']['post_category']) || empty($data['postparams']['post_category'])) return __('Please select a Post Categorie', EG_TEXTDOMAIN);
				}
			}
		}
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_GRID;
		
		if(isset($data['id']) && intval($data['id']) > 0){ //update
			//check if entry with handle exists, because this is unique
			$grid = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s AND id != %s ", $data['handle'], $data['id']), ARRAY_A);
			if(!empty($grid)){
				return __('Ess. Grid with chosen alias already exists, please choose a different alias', EG_TEXTDOMAIN);
			}
			
			//check if exists, if yes, update
			$entry = Essential_Grid::get_essential_grid_by_id($data['id']);
			if($entry !== false){
				$response = $wpdb->update($table_name,
											array(
												'name' => $data['name'],
												'handle' => $data['handle'],
												'postparams' => json_encode($data['postparams']),
												'params' => json_encode($data['params']),
												'layers' => json_encode($data['layers'])
												), array('id' => $data['id']));
											
				if($response === false) return __('Ess. Grid could not be changed', EG_TEXTDOMAIN);
				
				return true;
			}
		}
		
		//check if entry with handle exists, because this is unique
		$grid = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s", $data['handle']), ARRAY_A);
		if(!empty($grid)){
			return __('Ess. Grid with chosen alias already exists, please choose a different alias', EG_TEXTDOMAIN);
		}
		
		
		//insert if function did not return yet
		$response = $wpdb->insert($table_name, array('name' => $data['name'], 'handle' => $data['handle'], 'postparams' => json_encode($data['postparams']), 'params' => json_encode($data['params']), 'layers' => json_encode($data['layers'])));
		
		if($response === false) return false;
		
		return true;
	}
	
	
	/**
	 * Delete Grid
	 * @return    boolean	true
	 */
	private static function delete_grid_by_id($data){
		global $wpdb;
		
		if(!isset($data['id']) || intval($data['id']) == 0) return __('Invalid ID', EG_TEXTDOMAIN);
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_GRID;
		
		$response = $wpdb->delete($table_name, array('id' => $data['id']));
		if($response === false) return __('Ess. Grid could not be deleted', EG_TEXTDOMAIN);
		
		return true;
	}
	
	
	/**
	 * Duplicate Grid
	 * @return    boolean	true
	 */
	private static function duplicate_grid_by_id($data){
		global $wpdb;
		
		if(!isset($data['id']) || intval($data['id']) == 0) return __('Invalid ID', EG_TEXTDOMAIN);
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_GRID;
		
		//check if ID exists
		$duplicate = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $data['id']), ARRAY_A);
		
		if(empty($duplicate))
			return __('Ess. Grid could not be duplicated', EG_TEXTDOMAIN);
		
		//get handle that does not exist by latest ID in table and search until handle does not exist
		$result = $wpdb->get_row("SELECT * FROM $table_name ORDER BY id", ARRAY_A);
		
		if(empty($result))
			return __('Ess. Grid could not be duplicated', EG_TEXTDOMAIN);
		
		//check if handle Grid ID + n does exist and get until it does not
		$i = $result['id'] - 1;
		
		do {
			$i++;
			$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s", 'grid-'.$i), ARRAY_A);
			
		} while(!empty($result));

		//now add new Entry
		unset($duplicate['id']);
		$duplicate['name'] = 'Grid '.$i;
		$duplicate['handle'] = 'grid-'.$i;
		
		$response = $wpdb->insert($table_name, $duplicate);
	
		if($response === false) return __('Ess. Grid could not be duplicated', EG_TEXTDOMAIN);
		
		return true;
	}
    
	
	/**
	 * Validate Purchase
	 */
	public static function check_purchase_verification($data){
		global $wp_version;
		
		$response = wp_remote_post('http://updates.themepunch.com/activate.php', array(
			'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
			'body' => array(
				'name' => urlencode($data['username']),
				'api' => urlencode($data['api_key']),
				'code' => urlencode($data['code']),
				'product' => urlencode('essential-grid')
			)
		));
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$version_info = wp_remote_retrieve_body( $response );
		
		if ( $response_code != 200 || is_wp_error( $version_info ) ) {
			return false;
		}
		
		if($version_info == 'valid'){
			update_option('tp_eg_valid', 'true');
			update_option('tp_eg_api-key', $data['api_key']);
			update_option('tp_eg_username', $data['username']);
			update_option('tp_eg_code', $data['code']);
			
			return true;
		}elseif($version_info == 'exist'){
			return __('Purchase Code already registered!', EG_TEXTDOMAIN);
		}else{
			return __('Purchase Code is not valid!', EG_TEXTDOMAIN);
		}
		
	}
	
	
	/**
	 * Handle Ajax Requests
	 */
	public static function do_purchase_deactivation($data){
		global $wp_version;
	
		$key = get_option('tp_eg_api-key', '');
		$name = get_option('tp_eg_username', '');
		$code = get_option('tp_eg_code', '');
		
		$response = wp_remote_post('http://updates.themepunch.com/deactivate.php', array(
			'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url'),
			'body' => array(
				'name' => urlencode($name),
				'api' => urlencode($key),
				'code' => urlencode($code),
				'product' => urlencode('essential-grid')
			)
		));
		
		$response_code = wp_remote_retrieve_response_code( $response );
		$version_info = wp_remote_retrieve_body( $response );
		
		if ( $response_code != 200 || is_wp_error( $version_info ) ) {
			return false;
		}
		
		if($version_info == 'valid'){
			update_option('tp_eg_valid', 'false');
			update_option('tp_eg_api-key', '');
			update_option('tp_eg_username', '');
			update_option('tp_eg_code', '');
			
			return true;
		}else{
			return false;
		}
		
	}
	
	
	/**
	 * Handle Ajax Requests
	 */
	public static function on_ajax_action(){
		try{
			$token = self::getPostVar('token', false);
			
			//verify the token
			$isVerified = wp_verify_nonce($token, 'Essential_Grid_actions');
			
			$error = false;
			if($isVerified){
				$data = self::getPostVar("data", false);
				switch(self::getPostVar("client_action", false)){
					case 'add_google_fonts':
						$f = new ThemePunch_Fonts();
						
						$result = $f->add_new_font($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Font successfully created!", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getFontsUrl()));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'remove_google_fonts':
						if(!isset($data['handle'])) Essential_Grid::ajaxResponseError(__('Font not found', EG_TEXTDOMAIN), false);
						
						$f = new ThemePunch_Fonts();
						
						$result = $f->remove_font_by_handle($data['handle']);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Font successfully removed!", EG_TEXTDOMAIN), array('data' => $result));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'edit_google_fonts':
						if(!isset($data['handle'])) Essential_Grid::ajaxResponseError(__('No handle given', EG_TEXTDOMAIN), false);
						if(!isset($data['url'])) Essential_Grid::ajaxResponseError(__('No parameters given', EG_TEXTDOMAIN), false);
						
						$f = new ThemePunch_Fonts();
						
						$result = $f->edit_font_by_handle($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Font successfully changed!", EG_TEXTDOMAIN), array('data' => $result));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'add_custom_meta':
						$m = new Essential_Grid_Meta();
						
						$result = $m->add_new_meta($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Meta successfully created!", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getSubViewUrl(Essential_Grid_Admin::VIEW_SUB_CUSTOM_META_AJAX)));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'remove_custom_meta':
						if(!isset($data['handle'])) Essential_Grid::ajaxResponseError(__('Meta not found', EG_TEXTDOMAIN), false);
						
						$m = new Essential_Grid_Meta();
						
						$result = $m->remove_meta_by_handle($data['handle']);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Meta successfully removed!", EG_TEXTDOMAIN), array('data' => $result));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'edit_custom_meta':
						if(!isset($data['handle'])) Essential_Grid::ajaxResponseError(__('No handle given', EG_TEXTDOMAIN), false);
						if(!isset($data['name'])) Essential_Grid::ajaxResponseError(__('No name given', EG_TEXTDOMAIN), false);
						
						$m = new Essential_Grid_Meta();
						
						$result = $m->edit_meta_by_handle($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Meta successfully changed!", EG_TEXTDOMAIN), array('data' => $result));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'add_widget_area':
						
						$wa = new Essential_Grid_Widget_Areas();
						
						$result = $wa->add_new_sidebar($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Widget Area successfully created!", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getSubViewUrl(Essential_Grid_Admin::VIEW_SUB_WIDGET_AREA_AJAX)));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'edit_widget_area':
						if(!isset($data['handle'])) Essential_Grid::ajaxResponseError(__('No handle given', EG_TEXTDOMAIN), false);
						if(!isset($data['name'])) Essential_Grid::ajaxResponseError(__('No name given', EG_TEXTDOMAIN), false);
						
						$wa = new Essential_Grid_Widget_Areas();
						
						$result = $wa->edit_widget_area_by_handle($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Widget Area successfully changed!", EG_TEXTDOMAIN), array('data' => $result));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'remove_widget_area':
						if(!isset($data['handle'])) Essential_Grid::ajaxResponseError(__('Widget Area not found', EG_TEXTDOMAIN), false);
						
						$wa = new Essential_Grid_Widget_Areas();
						
						$result = $wa->remove_widget_area_by_handle($data['handle']);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Widget Area successfully removed!", EG_TEXTDOMAIN), array('data' => $result));
						}else{
							Essential_Grid::ajaxResponseError($result, false);
						}
					break;
					case 'get_preview_html_markup':
						$result = Essential_Grid_Base::output_demo_skin_html($data);
						
						Essential_Grid::ajaxResponseData(array("data"=>array('html' => $result['html'], 'preview' => @$result['preview'])));
						
					break;
					case 'update_general_settings':
						$result = self::savePluginPermission($data['permission']);
						
						update_option('tp_eg_output_protection', @$data['protection']);
						update_option('tp_eg_tooltips', @$data['tooltips']);
						update_option('tp_eg_wait_for_fonts', @$data['wait_for_fonts']);
						
						if($result !== true)
							$error = __("Global Settings did not change!", EG_TEXTDOMAIN);
						else
							Essential_Grid::ajaxResponseSuccess(__("Global Settings succesfully saved!", EG_TEXTDOMAIN), $result);
						
					break;
					case 'update_create_grid':
						$result = self::update_create_grid($data);
						
						if($result !== true){
							$error = $result;
						}else{
							if(isset($data['id']) && intval($data['id']) > 0)
								Essential_Grid::ajaxResponseSuccess(__("Grid successfully saved/changed!", EG_TEXTDOMAIN), $result);
							else
								Essential_Grid::ajaxResponseSuccess(__("Grid successfully saved/changed!", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW)));
							
						}
					break;
					case 'delete_grid':
						$result = self::delete_grid_by_id($data);
						if($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(__("Grid deleted", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW)));
						
					break;
					case 'duplicate_grid':
						$result = self::duplicate_grid_by_id($data);
						if($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(__("Grid duplicated", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl(Essential_Grid_Admin::VIEW_OVERVIEW)));
						
					break;
					case 'update_create_item_skin':
						$result = Essential_Grid_Item_Skin::update_save_item_skin($data);
						
						if($result !== true){
							$error = $result;
						}else{
							if(isset($data['id']) && intval($data['id']) > 0)
							  Essential_Grid::ajaxResponseSuccess(__("Item Skin changed", EG_TEXTDOMAIN), array('data' => $result));
							else
							  Essential_Grid::ajaxResponseSuccess(__("Item Skin created/changed", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl("","",'essential-'.Essential_Grid_Admin::VIEW_SUB_ITEM_SKIN_OVERVIEW)));
								
						}
					break;
					case 'update_custom_css':
						
						if(isset($data['global_css'])){
							
							Essential_Grid_Global_Css::set_global_css_styles($data['global_css']);
							Essential_Grid::ajaxResponseSuccess(__("CSS saved!", EG_TEXTDOMAIN), '');
							
						}else{
							$error = __("No CSS Received", EG_TEXTDOMAIN);
						}
					break;
					case 'delete_item_skin':
						$result = Essential_Grid_Item_Skin::delete_item_skin_by_id($data);
						if($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(__("Item Skin deleted", EG_TEXTDOMAIN), array('data' => $result));
						
					break;
					case 'duplicate_item_skin':
						$result = Essential_Grid_Item_Skin::duplicate_item_skin_by_id($data);
						if($result !== true)
							$error = $result;
						else
							Essential_Grid::ajaxResponseSuccess(__("Item Skin duplicated", EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl("","",'essential-'.Essential_Grid_Admin::VIEW_SUB_ITEM_SKIN_OVERVIEW)));
						
					break;
					case 'star_item_skin':
						$result = Essential_Grid_Item_Skin::star_item_skin_by_id($data);
						if($result !== true){
							$error = $result;
						}else{
							Essential_Grid::ajaxResponseSuccess(__("Favorite Changed", EG_TEXTDOMAIN), array('data' => $result));
						}
					break;
					case 'update_create_item_element':
						$result = Essential_Grid_Item_Element::update_create_essential_item_element($data);
						if($result !== true){
							$error = $result;
						}else{
							Essential_Grid::ajaxResponseSuccess(__("Item Element created/changed", EG_TEXTDOMAIN), array('data' => $result));
						}
					break;
					case 'check_item_element_existence':
						$result = Essential_Grid_Item_Element::check_existence_by_handle(@$data['name']);
						if($result === false){
							Essential_Grid::ajaxResponseData(array("data"=>array('existence'=>'false')));
						}elseif($result === true){
							Essential_Grid::ajaxResponseData(array("data"=>array('existence'=>'true')));
						}else{
							Essential_Grid::ajaxResponseData(array("data"=>array('existence'=>$result)));
						}
					
					break;
					case 'get_predefined_elements':
						$elements = Essential_Grid_Item_Element::getElementsForJavascript();
					
						$html_elements = Essential_Grid_Item_Element::prepareDefaultElementsForEditor();
						$html_elements.= Essential_Grid_Item_Element::prepareTextElementsForEditor();
						
						Essential_Grid::ajaxResponseData(array("data"=>array('elements'=>$elements,'html'=>$html_elements)));
					
					break;
					case 'delete_predefined_elements':
						$result = Essential_Grid_Item_Element::delete_element_by_handle($data);
						
						if($result !== true){
							$error = $result;
						}else{
							Essential_Grid::ajaxResponseSuccess(__("Item Element successfully deleted", EG_TEXTDOMAIN), array('data' => $result));
						}
					break;
					case 'update_create_navigation_skin_css':
						$nav = new Essential_Grid_Navigation();
						
						$result = $nav->update_create_navigation_skin_css($data);
						
						if($result !== true){
							$error = $result;
						}else{
							$base = new Essential_Grid_Base();
							$skin_css = Essential_Grid_Navigation::output_navigation_skins();
							$skins = Essential_Grid_Navigation::get_essential_navigation_skins();
							$select = '';
							foreach($skins as $skin){
								$select .= '<option value="'. $skin['handle'] .'">'. $skin['name'].'</option>'."\n";
							}
							
							if(isset($data['sid']) && intval($data['sid']) > 0)
								Essential_Grid::ajaxResponseSuccess(__("Navigation Skin successfully changed!", EG_TEXTDOMAIN), array('css' => $skin_css, 'select' => $select, 'default_skins' => $skins));
							else
								Essential_Grid::ajaxResponseSuccess(__("Navigation Skin successfully created", EG_TEXTDOMAIN), array('css' => $skin_css, 'select' => $select, 'default_skins' => $skins));
							
						}
					break;
					case 'delete_navigation_skin_css':
						$nav = new Essential_Grid_Navigation();
						
						$result = $nav->delete_navigation_skin_css($data);
						
						if($result !== true){
							$error = $result;
						}else{
							$base = new Essential_Grid_Base();
							$skin_css = Essential_Grid_Navigation::output_navigation_skins();
							$skins = Essential_Grid_Navigation::get_essential_navigation_skins();
							$select = '';
							foreach($skins as $skin){
								$select .= '<option value="'. $skin['handle'] .'">'. $skin['name'].'</option>'."\n";
							}
							
							Essential_Grid::ajaxResponseSuccess(__("Navigation Skin successfully deleted!", EG_TEXTDOMAIN), array('css' => $skin_css, 'select' => $select, 'default_skins' => $skins));
						}
					break;
					case 'get_post_meta_html_for_editor':
						if(!isset($data['post_id']) || intval($data['post_id']) == 0){
							Essential_Grid::ajaxResponseError(__('No Post ID/Wrong Post ID!', EG_TEXTDOMAIN), false);
							exit();
						}
						if(!isset($data['grid_id']) || intval($data['grid_id']) == 0){
							Essential_Grid::ajaxResponseError(__('Please save the grid first to use this feature!', EG_TEXTDOMAIN), false);
							exit();
						}
						
						$post = get_post($data['post_id']);
						$disable_advanced = true; //nessecary, so that only normal things can be changed in preview mode
						if(!empty($post)){
							$grid_id = $data['grid_id'];
							ob_start();
							require('views/elements/grid-meta-box.php');
							$content = ob_get_contents();
							ob_clean();
							ob_end_clean();
							
							Essential_Grid::ajaxResponseData(array("data"=>array('html'=>$content)));
						}else{
							Essential_Grid::ajaxResponseError(__('Post not found!', EG_TEXTDOMAIN), false);
							exit();
						}
						
					break;
					case 'update_post_meta_through_editor':
						if(!isset($data['metas']) || !isset($data['metas']['post_id']) || intval($data['metas']['post_id']) == 0){
							Essential_Grid::ajaxResponseError(__('No Post ID/Wrong Post ID!', EG_TEXTDOMAIN), false);
							exit();
						}
						
						if(!isset($data['metas']) || !isset($data['metas']['grid_id']) || intval($data['metas']['grid_id']) == 0){
							Essential_Grid::ajaxResponseError(__('Please save the grid first to use this feature!', EG_TEXTDOMAIN), false);
							exit();
						}
						
						//set the cobbles setting to the post
						$cobbles = json_decode(get_post_meta($data['metas']['grid_id'], 'eg_cobbles', true), true);
						$cobbles[$data['metas']['grid_id']]['cobbles'] = $data['metas']['eg_cobbles_size'];
						$cobbles = json_encode($cobbles);
						update_post_meta($data['metas']['post_id'], 'eg_cobbles', $cobbles);
						
						$result = self::custom_meta_box_save($data['metas']['post_id'], $data['metas'], true);
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__("Post Meta saved!", EG_TEXTDOMAIN), array());
						}else{
							Essential_Grid::ajaxResponseError(__('Post not found!', EG_TEXTDOMAIN), false);
							exit();
						}
						
					break;
					case 'trigger_post_meta_visibility':
						if(!isset($data['post_id']) || intval($data['post_id']) == 0){
							Essential_Grid::ajaxResponseError(__('No Post ID/Wrong Post ID!', EG_TEXTDOMAIN), false);
							exit();
						}
						if(!isset($data['grid_id']) || intval($data['grid_id']) == 0){
							Essential_Grid::ajaxResponseError(__('Please save the grid first to use this feature!', EG_TEXTDOMAIN), false);
							exit();
						}
						
						$visibility = json_decode(get_post_meta($data['post_id'], 'eg_visibility', true), true);
						
						$found = false;
						
						if(!empty($visibility) && is_array($visibility)){
							foreach($visibility as $grid => $setting){
								if($grid == $data['grid_id']){
									if($setting == false)
										$visibility[$grid] = true;
									else
										$visibility[$grid] = false;
										
									$found = true;
									break;
								}
							}
						}
						
						if(!$found){
							$visibility[$data['grid_id']] = false;
						}
						
						$visibility = json_encode($visibility);
						
						update_post_meta($data['post_id'], 'eg_visibility', $visibility);
						
						Essential_Grid::ajaxResponseSuccess(__("Visibility of Post for this Grid changed!", EG_TEXTDOMAIN), array());
						
					break;
					case 'get_image_by_id':
						if(!isset($data['img_id']) || intval($data['img_id']) == 0){
							$error = __('Wrong Image ID given', EG_TEXTDOMAIN);
						}else{
							$img = wp_get_attachment_image_src($data['img_id'], 'full');
							if($img !== false){
								Essential_Grid::ajaxResponseSuccess('', array('url' => $img[0]));
							}else{
								$error = __('Image with given ID does not exist', EG_TEXTDOMAIN);
							}
						}
					break;
					case 'activate_purchase_code':
						$result = false;
						
						if(!empty($data['username']) && !empty($data['api_key']) && !empty($data['code'])){
							$result = Essential_Grid_Admin::check_purchase_verification($data);
						}else{
							$error = __('The API key, the Purchase Code and the Username need to be set!', EG_TEXTDOMAIN);
						}
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__('Purchase Code Successfully Activated', EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl("","",'essential-'.Essential_Grid_Admin::VIEW_START)));
						}else{
							if($result !== false)
								$error = $result;
							else
								$error = __('Purchase Code is invalid', EG_TEXTDOMAIN);
							
							Essential_Grid::ajaxResponseError($error, false);
							exit();
						}
					break; 
					case 'deactivate_purchase_code':
						$result = Essential_Grid_Admin::do_purchase_deactivation($data);
						
						if($result === true){
							Essential_Grid::ajaxResponseSuccess(__('Successfully removed validation', EG_TEXTDOMAIN), array('data' => $result, 'is_redirect' => true, 'redirect_url' => self::getViewUrl("","",'essential-'.Essential_Grid_Admin::VIEW_START)));
						}else{
							if($result !== false)
								$error = $result;
							else
								$error = __('Could not remove Validation!', EG_TEXTDOMAIN);
							
							Essential_Grid::ajaxResponseError($error, false);
							exit();
						}			
					break;
					case 'dismiss_notice':
						update_option('tp_eg_valid-notice', 'false');
						Essential_Grid::ajaxResponseSuccess(__("."));
					break;
					case 'import_default_post_data':
						try{
							require(EG_PLUGIN_PATH.'includes/assets/default-posts.php');
							require(EG_PLUGIN_PATH.'includes/assets/default-grids-meta-fonts.php');
							
							if(isset($json_tax)){
								$import_tax = new PunchPost;
								$import_tax->import_taxonomies($json_tax);
							}
							
							//insert meta, grids & punchfonts
							$im = new Essential_Grid_Import();
							if(isset($tp_grid_meta_fonts)){
								$tp_grid_meta_fonts = json_decode($tp_grid_meta_fonts, true);
								$grids = @$tp_grid_meta_fonts['grids'];
								if(!empty($grids) && is_array($grids)){
									$grids_imported = $im->import_grids($grids);
								}
								
								$custom_metas = @$tp_grid_meta_fonts['custom-meta'];
								if(!empty($custom_metas) && is_array($custom_metas)){
									$custom_metas_imported = $im->import_custom_meta($custom_metas);
								}
								
								$custom_fonts = @$tp_grid_meta_fonts['punch-fonts'];
								if(!empty($custom_fonts) && is_array($custom_fonts)){
									$custom_fonts_imported = $im->import_punch_fonts($custom_fonts);
								}
							}
							
							if(isset($json_posts)){
								$import = new PunchPort;
								$import->set_tp_import_posts($json_posts);
								$import->import_custom_posts();
							}
							
							Essential_Grid::ajaxResponseSuccess(__('Demo data successfully imported', EG_TEXTDOMAIN), array());
							
						}catch(Exception $d){
							$error = __('Something went wrong, please contact the developer', EG_TEXTDOMAIN);
						}
					break;
					case 'export_data':
						$export_grids = self::getPostVar('export-grids-id', false);
						$export_skins = self::getPostVar('export-skins-id', false);
						$export_elements = self::getPostVar('export-elements-id', false);
						$export_navigation_skins = self::getPostVar('export-navigation-skins-id', false);
						$export_global_styles = self::getPostVar('export-global-styles', false);
						$export_custom_meta = self::getPostVar('export-custom-meta-handle', false);
						$export_punch_fonts = self::getPostVar('export-punch-fonts-handle', false);
						
						header( 'Content-Type: text/json' );
						header( 'Content-Disposition: attachment;filename=ess_grid.json');
						ob_start();
						
						$export = array();
						
						$ex = new Essential_Grid_Export();
						
						//export Grids
						if(!empty($export_grids))
							$export['grids'] = $ex->export_grids($export_grids);
						
						//export Skins
						if(!empty($export_skins))
							$export['skins'] = $ex->export_skins($export_skins);
						
						//export Elements
						if(!empty($export_elements))
							$export['elements'] = $ex->export_elements($export_elements);
						
						//export Navigation Skins
						if(!empty($export_navigation_skins))
							$export['navigation-skins'] = $ex->export_navigation_skins($export_navigation_skins);
						
						//export Custom Meta
						if(!empty($export_custom_meta))
							$export['custom-meta'] = $ex->export_custom_meta($export_custom_meta);
						
						//export Punch Fonts
						if(!empty($export_punch_fonts))
							$export['punch-fonts'] = $ex->export_punch_fonts($export_punch_fonts);
						
						//export Global Styles
						if($export_global_styles == 'on')
							$export['global-css'] = $ex->export_global_styles($export_global_styles);
						
						
						echo json_encode($export);
						
						$content = ob_get_contents();
						ob_clean();
						ob_end_clean();
						
						echo $content;
						
						exit();
					break;
					case 'import_data':
						if(!isset($data['imports']) || empty($data['imports'])){
							Essential_Grid::ajaxResponseError(__('No data for import selected', EG_TEXTDOMAIN), false);
							exit();
						}
						try{
							$im = new Essential_Grid_Import();
							
							$temp_d = @$data['imports'];
							unset($temp_d['data-grids']);
							unset($temp_d['data-skins']);
							unset($temp_d['data-elements']);
							unset($temp_d['data-navigation-skins']);
							unset($temp_d['data-global-css']);
							
							$im->set_overwrite_data($temp_d); //set overwrite data global to class
							
							$grids = @$data['imports']['data-grids'];
							if(!empty($grids) && is_array($grids)){
								foreach($grids as $key => $grid){
									$grids[$key] = json_decode(stripslashes($grid), true);
								}
								if(!empty($grids)){
									$grids_ids = @$data['imports']['import-grids-id'];
									$grids_imported = $im->import_grids($grids, $grids_ids);
								}
							}
							
							$skins = @$data['imports']['data-skins'];
							if(!empty($skins) && is_array($skins)){
								foreach($skins as $key => $skin){
									$skins[$key] = json_decode(stripslashes($skin), true);
								}
								if(!empty($skins)){
									$skins_ids = @$data['imports']['import-skins-id'];
									$skins_imported = $im->import_skins($skins, $skins_ids);
								}
							}
							
							$elements = @$data['imports']['data-elements'];
							if(!empty($elements) && is_array($elements)){
								foreach($elements as $key => $element){
									$elements[$key] = json_decode(stripslashes($element), true);
								}
								if(!empty($elements)){
									$elements_ids = @$data['imports']['import-elements-id'];
									$elements_imported = $im->import_elements(@$elements, $elements_ids);
								}
							}
							
							$navigation_skins = @$data['imports']['data-navigation-skins'];
							if(!empty($navigation_skins) && is_array($navigation_skins)){
								foreach($navigation_skins as $key => $navigation_skin){
									$navigation_skins[$key] = json_decode(stripslashes($navigation_skin), true);
								}
								if(!empty($navigation_skins)){
									$navigation_skins_ids = @$data['imports']['import-navigation-skins-id'];
									$navigation_skins_imported = $im->import_navigation_skins(@$navigation_skins, $navigation_skins_ids);
								}
							}
							
							$custom_metas = @$data['imports']['data-custom-meta'];
							if(!empty($custom_metas) && is_array($custom_metas)){
								foreach($custom_metas as $key => $custom_meta){
									$custom_metas[$key] = json_decode(stripslashes($custom_meta), true);
								}
								if(!empty($custom_metas)){
									$custom_metas_handle = @$data['imports']['import-custom-meta-handle'];
									$custom_metas_imported = $im->import_custom_meta($custom_metas, $custom_metas_handle);
								}
							}
							
							$custom_fonts = @$data['imports']['data-punch-fonts'];
							if(!empty($custom_fonts) && is_array($custom_fonts)){
								foreach($custom_fonts as $key => $custom_font){
									$custom_fonts[$key] = json_decode(stripslashes($custom_font), true);
								}
								if(!empty($custom_fonts)){
									$custom_fonts_handle = @$data['imports']['import-punch-fonts-handle'];
									$custom_fonts_imported = $im->import_punch_fonts($custom_fonts, $custom_fonts_handle);
								}
							}
							
							if(@$data['imports']['import-global-styles'] == 'on'){
								$global_css = @$data['imports']['data-global-css'];
								$global_css = stripslashes($global_css);
								$global_styles_imported = $im->import_global_styles($global_css);
							}
							
							Essential_Grid::ajaxResponseSuccess(__('Successfully imported data', EG_TEXTDOMAIN), array('is_redirect' => true, 'redirect_url' => self::getViewUrl("","",'essential-'.Essential_Grid_Admin::VIEW_START)));
							
						}catch(Exception $d){
							$error = __('Something went wrong, please contact the developer', EG_TEXTDOMAIN);
						}
						
					break;
					default:
						$error = true;
					break;
				}
			}else{
				$error = true;
			}
			if($error !== false){
				$showError = __("Wrong Request!", EG_TEXTDOMAIN);
				if($error !== true)
					$showError = __("Ajax Error: ", EG_TEXTDOMAIN).$error;
				
				Essential_Grid::ajaxResponseError($showError, false);
			}
			exit();
		}catch (Exception $e){exit();}
	}
	
}
