<?php
/**
 * Master Slider Admin Scripts Class.
 *
 * @package   MasterSlider
 * @author    averta [averta.net]
 * @license   LICENSE.txt
 * @link      http://masterslider.com
 * @copyright Copyright © 2014 averta
 */

/**
 *  Class to load and print master slider panel scripts
 */
class MSP_Admin_Assets {
	

	/**
	 * __construct
	 */
	function __construct() {

	}


	public function enqueue (){

		// general assets
		$this->load_general_styles();
		$this->load_panel_styles();

		$this->add_general_variables();
		$this->add_general_script_localizations();
		$this->load_general_scripts();

		// panel spesific assets
		if( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array( 'edit', 'add' ) ) ) {

			$this->load_panel_scripts();
			$this->add_panel_variables();
			$this->add_panel_script_localizations();
		}
		
	}

	
	/**
	 * Load scripts for master slider admin panel
	 * @return void
	 */
	public function load_panel_scripts() {

		// Load wp media uploader
		wp_enqueue_media();

		// Master Slider Panel Scripts
		wp_enqueue_script( MSWP_SLUG . '-handlebars'	 ,	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/js/handlebars.min.js',  			array( 'jquery' ), MSWP_AVERTA_VERSION, true );
		wp_enqueue_script( MSWP_SLUG . '-ember-js'		 , 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/js/ember.min.js',  				array( 'jquery' ), MSWP_AVERTA_VERSION, true );
		wp_enqueue_script( MSWP_SLUG . '-ember-model'	 , 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/js/ember-model.min.js',  			array( 'jquery' ), MSWP_AVERTA_VERSION, true );
		wp_enqueue_script( MSWP_SLUG . '-msp-required'	 , 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/js/msp.required.js',  
			array( 
				'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-draggable', 
				'jquery-ui-sortable', 'jquery-ui-slider', 'jquery-ui-spinner' 
			), 
			MSWP_AVERTA_VERSION, true 
		);

		wp_enqueue_script( MSWP_SLUG . '-masterslider-wp', 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/js/masterslider.wp.js',  			array( MSWP_SLUG . '-msp-required' ), MSWP_AVERTA_VERSION, true );
	}


	/**
	 * Print required variable for master slider panel
	 */
	public function add_panel_variables() {
		
		$slider_skins = array(
	        array( 'class' => 'ms-skin-default', 'label' => 'Default' ),
	        array( 'class' => 'ms-skin-light-2', 'label' => 'Light 2' ),
	        array( 'class' => 'ms-skin-light-3', 'label' => 'Light 3' ),
	        array( 'class' => 'ms-skin-light-4', 'label' => 'Light 4' ),
	        array( 'class' => 'ms-skin-light-5', 'label' => 'Light 5' ),
	        array( 'class' => 'ms-skin-light-6', 'label' => 'Light 6' ),
	        array( 'class' => 'ms-skin-light-6 round-skin', 'label' => 'Light 6 Round' ),

	        array( 'class' => 'ms-skin-contrast', 'label' => 'Contrast' ),
	        array( 'class' => 'ms-skin-black-1' , 'label' => 'Black 1' ),
	        array( 'class' => 'ms-skin-black-2' , 'label' => 'Black 2' ),
	        array( 'class' => 'ms-skin-black-2 round-skin', 'label' => 'Black 2 Round' ),
	        array( 'class' => 'ms-skin-metro'   , 'label' => 'Metro' )
	    );

		wp_localize_script( 'jquery', '__MSP_SKINS', apply_filters( 'masterslider_skins', $slider_skins ) );

		// get and print slider id
		if ( isset( $_REQUEST['slider_id'] ) ) {

			$slider_id  = $_REQUEST['slider_id'];
		
		} else {
			global $mspdb;
			$slider_id = 0;

			if ( isset( $_REQUEST['action'] ) && 'add' == $_REQUEST['action'] ) {
				$slider_id = $mspdb->add_slider( array( 'status' => 'draft' ) );
				wp_localize_script( 'jquery', '__MSP_SLIDER_ID', (string) $slider_id );
			}
		}
		
		// Get and print panel data
		if ( $slider_id ) {

			global $mspdb;
			$slider_data = $mspdb->get_slider( $slider_id );

			$slider_type = isset( $slider_data[ 'type' ] ) ? $slider_data[ 'type' ] : 'custom';
			$slider_type = empty( $slider_type ) ? 'custom' : $slider_type;

			$msp_data = isset( $slider_data[ 'params' ] ) ? $slider_data[ 'params' ] : NULL;
			$msp_data = empty( $slider_data[ 'params' ] ) ? NULL : $slider_data[ 'params' ];

			$msp_preset_style  = msp_get_option( 'preset_style' , NULL );
			$msp_preset_effect = msp_get_option( 'preset_effect', NULL );

			$msp_preset_style  = empty( $msp_preset_style  ) ? NULL : $msp_preset_style;
			$msp_preset_effect = empty( $msp_preset_effect ) ? NULL : $msp_preset_effect;

			wp_localize_script( 'jquery', '__MSP_DATA'			, $msp_data    );
			wp_localize_script( 'jquery', '__MSP_PRESET_STYLE'  , $msp_preset_style  );
			wp_localize_script( 'jquery', '__MSP_PRESET_EFFECT' , $msp_preset_effect );
			wp_localize_script( 'jquery', '__MSP_TYPE'			, $slider_type );
		}


		// print essential variables (types, taxs, terms, template tags) for post slider in admin panel
		// since version 1.7

		if( isset( $slider_type ) && 'post' == $slider_type ) {

			$defined_tags = msp_get_general_post_template_tags();

			$tags  = array();
			foreach ( $defined_tags as $defined_tag ) {

				$tag_type = ( '_general' == $defined_tag[ 'type' ] ) ? 'general' : $defined_tag[ 'type' ];
				
				$tags[ $tag_type ][] = array( 
					'name'	=> $defined_tag['name'],
					'label' => $defined_tag['label']
				);
			}
			// -- get post types, taxes and terms --

			$PS = msp_get_post_slider_class();
			$terms = $PS->get_tax_term_dictionary();

			// -------------------------------------
			// 
			$js_data = array(
				'types_taxs_terms' 	=> $terms,
				'content_tags' 		=> $tags
			);

			wp_localize_script( 'jquery', '__MSP_POST', apply_filters( 'masterslider_post_slider_init_data', $js_data ) );
		}


		// print essential variables (types, taxs, terms, template tags) for woocommerce sliders in admin panel
		// since version 1.8
		
		if( isset( $slider_type ) && 'wc-product' == $slider_type ) {

			
			// if woocommerce is installed and actived
			if ( msp_is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

				// -- template tags --------------------
				
				$defined_tags = msp_get_general_post_template_tags();
				$woocomm_tags = msp_get_woocommerce_template_tags();

				$defined_tags = array_merge( $defined_tags, $woocomm_tags );

				$tags  = array();
				foreach ( $defined_tags as $defined_tag ) {

					$tag_type = ( '_general' == $defined_tag[ 'type' ] ) ? 'general' : $defined_tag[ 'type' ];
					
					$tags[ $tag_type ][] = array( 
						'name'	=> $defined_tag['name'],
						'label' => $defined_tag['label']
					);
				}
				// -- get post types, taxes and terms --

				$WCS = msp_get_wc_slider_class();
				$terms = $WCS->get_tax_term_dictionary();

				// -------------------------------------
				
				$js_data = array(
					'types_taxs_terms' 	=> $terms,
					'content_tags' 		=> $tags
				);
			
			// if woocommerce is not activated 
			} else {
				$js_data = null;
				$wc_installation_url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=600&height=550' );
				wp_localize_script( 'jquery', '__WC_INSTALL_URL', $wc_installation_url );
			}
			
			wp_localize_script( 'jquery', '__MSP_POST', apply_filters( 'masterslider_wc_product_slider_init_data', $js_data ) );
		}

		// define panel directory path
		wp_localize_script( 'jquery', '__MSP_PATH', MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/' );

		do_action( 'masterslider_admin_add_panel_variables', $slider_type );
	}




	/**
	 * Print required variable for master slider admin page
	 */
	public function add_general_variables() {

		$uploads = wp_upload_dir();

		// define admin ajax address and master slider page
		wp_localize_script( 'jquery', '__MS', array(
			'ajax_url'       => admin_url( 'admin-ajax.php' ),
			'msp_menu_page'  => menu_page_url( MSWP_SLUG, false ),
			'msp_plugin_url' => MSWP_AVERTA_URL,
			'upload_dir'     => $uploads['baseurl'],
			'importer' 		 => admin_url( 'admin.php?import=masterslider-importer' )
		));
	}


	/**
	 * Add script localizations 
	 */
	public function add_panel_script_localizations() {

		wp_localize_script( 'jquery', '__MSP_LAN', apply_filters( 'masterslider_admin_localize', array(
				
			// CallbacksController.js
			'cb_001' => __( 'On slide change start', MSWP_TEXT_DOMAIN ),
			'cb_002' => __( 'On slide change end', MSWP_TEXT_DOMAIN ),
			'cb_003' => __( 'On slide timer change', MSWP_TEXT_DOMAIN ),
			'cb_004' => __( 'On slider resize', MSWP_TEXT_DOMAIN ), 
			'cb_005' => __( 'On Youtube/Vimeo video play', MSWP_TEXT_DOMAIN ),
			'cb_006' => __( 'On Youtube/Vimeo video close', MSWP_TEXT_DOMAIN ),
			'cb_007' => __( 'On swipe start', MSWP_TEXT_DOMAIN ),
			'cb_008' => __( 'On swipe move', MSWP_TEXT_DOMAIN ),
			'cb_009' => __( 'On swipe end', MSWP_TEXT_DOMAIN ),
			'cb_010' => __( 'Are you sure you want to remove "%s" callback?', MSWP_TEXT_DOMAIN ),
			'cb_011' => __( 'On slider Init', MSWP_TEXT_DOMAIN ),

			// ControlsController.js
			'cc_001' => __( 'Arrows', MSWP_TEXT_DOMAIN ),
			'cc_002' => __( 'Line Timer', MSWP_TEXT_DOMAIN ),
			'cc_003' => __( 'Bullets', MSWP_TEXT_DOMAIN ),
			'cc_004' => __( 'Circle Timer', MSWP_TEXT_DOMAIN ),
			'cc_005' => __( 'Scrollbar', MSWP_TEXT_DOMAIN ),
			'cc_006' => __( 'Slide Info', MSWP_TEXT_DOMAIN ),
			'cc_007' => __( 'Thumblist/Tabs', MSWP_TEXT_DOMAIN ),

			// EffectsController
			'ec_001' => __( 'Please enter name for new preset effect', MSWP_TEXT_DOMAIN ),
			'ec_002' => __( 'Custom effect', MSWP_TEXT_DOMAIN ),
			
			// LayersController.js
			'lc_001' => __( 'Text Layer', MSWP_TEXT_DOMAIN ),
			'lc_002' => __( 'Image Layer', MSWP_TEXT_DOMAIN ),
			'lc_003' => __( 'Video Layer', MSWP_TEXT_DOMAIN ),
			'lc_004' => __( 'Hotspot', MSWP_TEXT_DOMAIN ),

			// StylesController.js
			'sc_001' => __( 'Please enter name for new preset style', MSWP_TEXT_DOMAIN ),
			'sc_002' => __( 'Custom style', MSWP_TEXT_DOMAIN ),

			//SliderModel.js
			'sm_001' => __( 'Untitled Slider', MSWP_TEXT_DOMAIN ),

			// EffectEditorView.js
			'ee_001' => __( 'Preset Transitions', MSWP_TEXT_DOMAIN ),
			'ee_002' => __( 'Apply transition', MSWP_TEXT_DOMAIN ),
			'ee_003' => __( 'Save as preset', MSWP_TEXT_DOMAIN ),

			// StageView.js
		 	'sv_001' => __( 'Align to stage :', MSWP_TEXT_DOMAIN ),
		 	'sv_002' => __( 'Snapping :', MSWP_TEXT_DOMAIN ),
		 	'sv_003' => __( 'Zoom :', MSWP_TEXT_DOMAIN ),
		 	'sv_010' => __( 'Layer position origin : ', MSWP_TEXT_DOMAIN ),

		 	//StyleEditorView.js
		 	'se_001' => __( 'Apply style', MSWP_TEXT_DOMAIN ),
		 	'se_002' => __( 'Save as preset', MSWP_TEXT_DOMAIN ),
		 	'se_003' => __( 'Preset Styles', MSWP_TEXT_DOMAIN ),
		 	'se_004' => __( 'By deleting preset style it also will be removed from other sliders in your website. Are you sure you want to delete "%s"?', MSWP_TEXT_DOMAIN ),

		 	//TemplatesView.js
		 	'tv_001' => __( 'Master Slider Templates', MSWP_TEXT_DOMAIN ),
		 	'tv_002' => __( 'Changing template will reset all slider controls and will change some slider settings. Continue?', MSWP_TEXT_DOMAIN),
		 	//TimelineView.js
		 	'tl_001' => __( 'Show/Hide all', MSWP_TEXT_DOMAIN ),
		 	'tl_002' => __( 'Solo All', MSWP_TEXT_DOMAIN ),
		 	'tl_003' => __( 'Lock/Unlock all', MSWP_TEXT_DOMAIN ),
		 	'tl_004' => __( 'Exit preview', MSWP_TEXT_DOMAIN ),
		 	'tl_005' => __( 'Preview slide', MSWP_TEXT_DOMAIN ),
		 	'tl_006' => __( 'Show/Hide', MSWP_TEXT_DOMAIN ),
		 	'tl_007' => __( 'Solo', MSWP_TEXT_DOMAIN ),
		 	'tl_008' => __( 'Lock/Unlock', MSWP_TEXT_DOMAIN ),
		 	'tl_009' => __( 'Are you sure you want to remove this layer?', MSWP_TEXT_DOMAIN ),
		 	'tl_010' => __( 'Start delay :', MSWP_TEXT_DOMAIN ),
		 	'tl_011' => __( 'Show duration :', MSWP_TEXT_DOMAIN ),
		 	'tl_012' => __( 'Waiting duration :', MSWP_TEXT_DOMAIN ),
		 	'tl_013' => __( 'Hide duration :', MSWP_TEXT_DOMAIN ),

		 	//UIViews.js
		 	'ui_001' => __( 'Show/Hide slide', MSWP_TEXT_DOMAIN ),
		 	'ui_002' => __( 'Duplicate slide', MSWP_TEXT_DOMAIN ),
		 	'ui_003' => __( 'Remove slide', MSWP_TEXT_DOMAIN ),
		 	'ui_004' => __( 'Are you sure you want to delete this slide?', MSWP_TEXT_DOMAIN ),
		 	'ui_005' => __( 'Open on the same page', MSWP_TEXT_DOMAIN ),
		 	'ui_006' => __( 'Open on new page', MSWP_TEXT_DOMAIN ),
		 	'ui_007' => __( 'Open in parent frame', MSWP_TEXT_DOMAIN ),
		 	'ui_008' => __( 'Open in main frame', MSWP_TEXT_DOMAIN ),
		 	'ui_009' => __( 'Fill', MSWP_TEXT_DOMAIN ),
		 	'ui_010' => __( 'Fit', MSWP_TEXT_DOMAIN ),
		 	'ui_011' => __( 'Center', MSWP_TEXT_DOMAIN ),
		 	'ui_012' => __( 'Stretch', MSWP_TEXT_DOMAIN ),
		 	'ui_013' => __( 'Tile', MSWP_TEXT_DOMAIN ),
		 	'ui_014' => __( 'None', MSWP_TEXT_DOMAIN ),
		 	'ui_015' => __( 'Align top', MSWP_TEXT_DOMAIN ),
		 	'ui_016' => __( 'Align vertical center', MSWP_TEXT_DOMAIN ),
		 	'ui_017' => __( 'Align bottom', MSWP_TEXT_DOMAIN ),
		 	'ui_018' => __( 'Align left', MSWP_TEXT_DOMAIN ),
		 	'ui_019' => __( 'Align horizontal center', MSWP_TEXT_DOMAIN ),
		 	'ui_020' => __( 'Align right', MSWP_TEXT_DOMAIN ),
		 	
		 	// ApplicationController.js
		 	'ap_001' => __( 'Sending data...', MSWP_TEXT_DOMAIN ),
		 	'ap_002' => __( 'An Error accorded, please try again.', MSWP_TEXT_DOMAIN ),
		 	'ap_003' => __( 'Data saved successfully.', MSWP_TEXT_DOMAIN ),

		 	'flk_001' => __( 'Photo title', MSWP_TEXT_DOMAIN ),
		 	'flk_002' => __( 'Photo owner name', MSWP_TEXT_DOMAIN ),
		 	'flk_003' => __( 'Date taken', MSWP_TEXT_DOMAIN ),
		 	'flk_004' => __( 'Photo description', MSWP_TEXT_DOMAIN ),

		 	'fb_001' => __( 'Photo name', MSWP_TEXT_DOMAIN ),
		 	'fb_002' => __( 'Photo owner name', MSWP_TEXT_DOMAIN ),
		 	'fb_003' => __( 'Photo link', MSWP_TEXT_DOMAIN )
		 	
		) ) );
	
	}



	/**
	 * Add general script localizations 
	 */
	public function add_general_script_localizations() {

		wp_localize_script( 'jquery', '__MSP_GEN_LAN', apply_filters( 'masterslider_admin_general_localize', array(
			
			'genl_001' => __( 'The changes you made will be lost if you navigate away from this page. To exit preview mode click on close (X) button.', MSWP_TEXT_DOMAIN ),
			'genl_002' => __( 'Master Slider Preview', MSWP_TEXT_DOMAIN ),
			'genl_003' => __( 'Loading Slider ..', MSWP_TEXT_DOMAIN ),
			'genl_004' => __( 'Creating The Slider ..', MSWP_TEXT_DOMAIN ), 
			'genl_005' => __( 'Select a Starter', MSWP_TEXT_DOMAIN ),
			'genl_006' => __( 'No slider is selected to export.', MSWP_TEXT_DOMAIN ),
			'genl_007' => __( 'Import', MSWP_TEXT_DOMAIN )

		) ) );
	
	}



	/**
	 * Panel spesific styles
	 * 
	 * @return void
	 */
	public function load_panel_styles() {

		// Master Slider Panel styles
		wp_enqueue_style( MSWP_SLUG .'-reset', 			MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/reset.css',  				array(), MSWP_AVERTA_VERSION );
		wp_enqueue_style( MSWP_SLUG .'-jq-ui', 			MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/jquery-ui-1.10.4.min.css', array(), MSWP_AVERTA_VERSION );
		wp_enqueue_style( MSWP_SLUG .'-spectrum', 		MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/spectrum.css',  			array(), MSWP_AVERTA_VERSION );
		wp_enqueue_style( MSWP_SLUG .'-codemirror', 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/codemirror.css', 			array(), MSWP_AVERTA_VERSION );
		wp_enqueue_style( MSWP_SLUG .'-jscrollpane',	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/jquery.jscrollpane.css', 	array(), MSWP_AVERTA_VERSION );
		wp_enqueue_style( MSWP_SLUG .'-main-style', 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/msp-style.css',  			array(), MSWP_AVERTA_VERSION );
		wp_enqueue_style( MSWP_SLUG .'-components', 	MSWP_AVERTA_ADMIN_URL . '/views/slider-panel/css/msp-components.css',  		array(), MSWP_AVERTA_VERSION );

	}

	/**
	 * Master slider general/common styles
	 * 
	 * @return void
	 */
	public function load_general_styles() {
		// gnereal styles for masterslider admin page
		wp_enqueue_style( MSWP_SLUG .'-admin-styles', 	MSWP_AVERTA_ADMIN_URL . '/assets/css/msp-general.css', 						array(), MSWP_AVERTA_VERSION );
	}


	public function load_general_scripts() {
		// disable wp autosave on master slider panel 
		wp_dequeue_script( 'autosave' );
		wp_enqueue_script( MSWP_SLUG .'-admin-scripts', MSWP_AVERTA_ADMIN_URL . '/assets/js/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'), MSWP_AVERTA_VERSION, true );
	}

}