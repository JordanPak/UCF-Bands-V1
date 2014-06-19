<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
 
class Essential_Grid_Base {
	
	const SORTBY_NONE = 'none';
	const SORTBY_ID = 'ID';
	const SORTBY_AUTHOR = 'author';
	const SORTBY_TITLE = 'title';
	const SORTBY_SLUG = 'name';
	const SORTBY_DATE = 'date';
	const SORTBY_LAST_MODIFIED = 'modified';
	const SORTBY_RAND = 'rand';
	const SORTBY_COMMENT_COUNT = 'comment_count';
	const SORTBY_MENU_ORDER = 'menu_order';
	
	const ORDER_DIRECTION_ASC = 'ASC';
	const ORDER_DIRECTION_DESC = 'DESC';
	
	const THUMB_SMALL = 'thumbnail';
	const THUMB_MEDIUM = 'medium';
	const THUMB_LARGE = 'large';
	const THUMB_FULL = 'full';
	
	const STATE_PUBLISHED = 'publish';
	const STATE_DRAFT = 'draft';
    
	/**
	 * Get $_GET Parameter
	 */
	protected static function getGetVar($key,$default = "",$type=""){
		$val = self::getVar($_GET, $key, $default, $type);
		return($val);
	}
	
	
	/**
	 * Get $_POST Parameter
	 */
	public static function getPostVar($key,$default = "",$type=""){
		$val = self::getVar($_POST, $key, $default, $type);
		return($val);			
	}
	
	
	/**
	 * Get $_POST/$_GET Parameter
	 */
	public static function getVar($arr,$key,$default = "", $type=""){
		$val = $default;
		if(isset($arr[$key])) $val = $arr[$key];
		
		switch($type){
			case 'i': //int
				$val = intval($val);
			break;
			case 'f': //float
				$val = floatval($val);
			break;
		}
		
		return($val);
	}
	
	
	/**
	 * Throw exception
	 */
	public static function throw_error($message,$code=null){
		if(!empty($code))
			throw new Exception($message,$code);
		else
			throw new Exception($message);
	}
	
	
	/**
	 * Sort Array by Value order
	 */
	public static function sort_by_order($a,$b){
        if(!isset($a['order']) || !isset($b['order'])) return 0;
		$a = $a['order'];
		$b = $b['order'];
		return (($a < $b) ? -1 : (($a > $b) ? 1 : 0));
	}
	
    
    /**
	 * change hex to rgba
	 */
    public static function hex2rgba($hex, $transparency = false) {
        if($transparency !== false){
            $transparency = ($transparency > 0) ? $transparency / 100 : 0;
        }else{
            $transparency = 1;
        }
        
        $hex = str_replace("#", "", $hex);
        
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        
        return 'rgba('.$r.', '.$g.', '.$b.', '.$transparency.')';
         
    }
	
	
	/**
	 * strip slashes recursive
	 */
	public static function stripslashes_deep($value){
		$value = is_array($value) ?
			array_map( array('Essential_Grid_Base', 'stripslashes_deep'), $value) :
			stripslashes($value);
		
		return $value;
	}

	
	/**
	 * get text intro, limit by number of words
	 */
	public static function get_text_intro($text, $limit, $type = 'words'){
		
		$intro = $text;
		
		if($type == 'words'){
			$arrIntro = explode(' ', $text, $limit);

			if (count($arrIntro)>=$limit) {
				array_pop($arrIntro);
				$intro = implode(" ",$arrIntro);
				$intro = trim($intro);
				if(!empty($intro))
					$intro .= '...';
			} else {
				$intro = implode(" ",$arrIntro);
			}
		}elseif($type == 'chars'){
			$text = strip_tags($text);
			$intro = substr($text, 0, $limit);
			if(strlen($text) > $limit) $intro .= '...';
		}
		
		$intro = preg_replace('`\[[^\]]*\]`','',$intro);
		
		return($intro);
	}
	
	
	/**
	 * Get all images sizes + custom added sizes
	 * since: 1.0.2
	 */
	public function get_all_image_sizes(){
		$custom_sizes = array();
		$added_image_sizes = get_intermediate_image_sizes();
		if(!empty($added_image_sizes) && is_array($added_image_sizes)){
			foreach($added_image_sizes as $key => $img_size_handle){
				$custom_sizes[$img_size_handle] = ucwords(str_replace('_', ' ', $img_size_handle));
			}
		}
		$img_orig_sources = array(
			'full' => __('Original Size', EG_TEXTDOMAIN),
			'thumbnail' => __('Thumbnail', EG_TEXTDOMAIN),
			'medium' => __('Medium', EG_TEXTDOMAIN),
			'large' => __('Large', EG_TEXTDOMAIN)
		);
		return array_merge($img_orig_sources, $custom_sizes);
	}
	
	
	/**
	 * convert date to the date format that the user chose.
	 */
	public static function convert_post_date($date){
		if(empty($date))
			return($date);
		$date = date_i18n(get_option('date_format'), strtotime($date));
		return($date);
	}
	
	
	/**
	 * Create Multilanguage for JavaScript
	 */
	protected static function get_javascript_multilanguage(){
		
		$lang = array(
			'aj_please_wait' => __('Please wait...', EG_TEXTDOMAIN),
			'aj_ajax_error'   => __('Ajax Error!!!', EG_TEXTDOMAIN),
			'aj_success_must'   => __('The \'success\' param is a must!', EG_TEXTDOMAIN),
			'aj_error_not_found'   => __('ajax error! action not found', EG_TEXTDOMAIN),
			'aj_empty_response'   => __('Empty ajax response!', EG_TEXTDOMAIN),
			'aj_wrong_alias'   => __('wrong alias', EG_TEXTDOMAIN),
			'delete_item_skin'   => __('Really delete choosen Item Skin?', EG_TEXTDOMAIN),
			'delete_grid'   => __('Really delete the Grid?', EG_TEXTDOMAIN),
			'choose_image'   => __('Choose Image', EG_TEXTDOMAIN),
			'select_choose'   => __('--- choose ---', EG_TEXTDOMAIN),
			'new_element'   => __('New Element', EG_TEXTDOMAIN),
			'choose_image'   => __('Choose Image', EG_TEXTDOMAIN),
			'new_element'   => __('New Element', EG_TEXTDOMAIN),
			'bottom_on_hover'   => __('Bottom on Hover', EG_TEXTDOMAIN),
			'top_on_hover'   => __('Top on Hover', EG_TEXTDOMAIN),
			'hidden'   => __('Hidden', EG_TEXTDOMAIN),
			'full_price'   => __('$99 $999', EG_TEXTDOMAIN),
			'regular_price'   => __('$99', EG_TEXTDOMAIN),
			'regular_price_no_cur'   => __('99', EG_TEXTDOMAIN),
			'top'   => __('Top', EG_TEXTDOMAIN),
			'right'   => __('Right', EG_TEXTDOMAIN),
			'bottom'   => __('Bottom', EG_TEXTDOMAIN),
			'left'   => __('Left', EG_TEXTDOMAIN),
			'hide'   => __('Hide', EG_TEXTDOMAIN),
			'single'   => __('Single', EG_TEXTDOMAIN),
			'bulk'   => __('Bulk', EG_TEXTDOMAIN),
			'choose_images'   => __('Choose Images', EG_TEXTDOMAIN),
			'import_demo_post_heavy_loading'   => __('The following demo data will be imported: Grids, Custom Meta, PunchFonts. This can take a while, please do not leave the site until the import is finished', EG_TEXTDOMAIN),
			'save_settings'   => __('Save Settings', EG_TEXTDOMAIN),
			'add_element'   => __('Add Element', EG_TEXTDOMAIN),
			'edit_element'   => __('Edit Element', EG_TEXTDOMAIN),
			'remove_this_element'   => __('Really remove this element?', EG_TEXTDOMAIN),
			'choose_skins'   => __('Choose Skins', EG_TEXTDOMAIN),
			'add_selected'   => __('Add Selected', EG_TEXTDOMAIN),
			'deleting_nav_skin_message'   => __('Deleting a Navigation Skin may result in missing Skins in other Grids. Proceed?', EG_TEXTDOMAIN),
			'add_meta'   => __('Add Meta', EG_TEXTDOMAIN),
			'add_widget_area'   => __('Add Widget Area', EG_TEXTDOMAIN),
			'add_font'   => __('Add Google Font', EG_TEXTDOMAIN),
			'save_post_meta'   => __('Save Post Meta', EG_TEXTDOMAIN),
			'really_change_widget_area_name'   => __('Are you sure the change the Widget Area name?', EG_TEXTDOMAIN),
			'really_delete_widget_area'   => __('Really delete this Widget Area? This can\'t be undone and if may affect existing Posts/Pages that use this Widget Area.', EG_TEXTDOMAIN),
			'really_delete_meta'   => __('Really delete this meta? This can\'t be undone.', EG_TEXTDOMAIN),
			'really_change_meta_effects'   => __('If you change this settings, it may affect current Posts that use this meta, proceed?', EG_TEXTDOMAIN),
			'really_change_font_effects'   => __('If you change this settings, it may affect current Posts that use this Font, proceed?', EG_TEXTDOMAIN),
			'handle_and_name_at_least_3'   => __('The handle and name has to be at least three characters long!', EG_TEXTDOMAIN),
			'layout_settings'   => __('Layout Settings', EG_TEXTDOMAIN),
			'close'   => __('Close', EG_TEXTDOMAIN),
			'create_nav_skin'   => __('Save Navigation Skin', EG_TEXTDOMAIN),
			'apply_changes'   => __('Save Changes', EG_TEXTDOMAIN),
			'new_element_sanitize'   => __('new-element', EG_TEXTDOMAIN),
			'really_delete_element_permanently'   => __('This will delete this element permanently, really proceed?', EG_TEXTDOMAIN),
			'element_name_exists_do_overwrite'   => __('Element with chosen name already exists. Really overwrite the Element?', EG_TEXTDOMAIN),
			'element_was_not_changed'   => __('Element was not created/changed', EG_TEXTDOMAIN),
			'not_selected'   => __('Not Selected', EG_TEXTDOMAIN),
			'class_name'   => __('Class:', EG_TEXTDOMAIN),
			'save_changes' => __('Save Changes', EG_TEXTDOMAIN),
			'enter_position' => __('Enter a Position', EG_TEXTDOMAIN),
			'leave_not_saved'   => __('By leaving now, all changes since the last saving will be lost. Really leave now?', EG_TEXTDOMAIN),
			'please_enter_unique_item_name' => __('Please enter a unique item name', EG_TEXTDOMAIN),
			'fontello_icons' => __('Choose Icon', EG_TEXTDOMAIN),
			'please_enter_unique_element_name' => __('Please enter a unique element name', EG_TEXTDOMAIN),
			'please_enter_unique_skin_name' => __('Please enter a unique Navigation Skin name', EG_TEXTDOMAIN),
			'item_name_too_short' => __('Item name too short', EG_TEXTDOMAIN),
			'skin_name_too_short' => __('Navigation Skin name too short', EG_TEXTDOMAIN),
			'skin_name_already_registered' => __('Navigation Skin with choosen name already exists, please choose a different name', EG_TEXTDOMAIN),
			'withvimeo' => __('With Vimeo', EG_TEXTDOMAIN),
			'withyoutube' => __('With YouTube', EG_TEXTDOMAIN),
			'withimage' => __('With Image', EG_TEXTDOMAIN),
			'withthtml5' => __('With HTML5 Video', EG_TEXTDOMAIN),
			'withsoundcloud' => __('With SoundCloud', EG_TEXTDOMAIN),
			'withoutmedia' => __('Without Media', EG_TEXTDOMAIN),
			'selectyouritem' => __('Select Your Item', EG_TEXTDOMAIN),															
			'add_at_least_one_element' => __('Please add at least one element in Custom Grid mode', EG_TEXTDOMAIN)
		);
		
		return $lang;
	}

	/**
	 * get grid animations
	 */
	public static function get_grid_animations(){
		
		$animations = array(
			'fade' =>  __('Fade', EG_TEXTDOMAIN),
			'scale' =>  __('Scale', EG_TEXTDOMAIN),
			'rotatescale' =>  __('Rotate Scale', EG_TEXTDOMAIN),
			'fall' =>  __('Fall', EG_TEXTDOMAIN),
			'rotatefall' =>  __('Rotate Fall', EG_TEXTDOMAIN),			
			'horizontal-slide' =>  __('Horizontal Slide', EG_TEXTDOMAIN),
			'vertical-slide' =>  __('Vertical Slide', EG_TEXTDOMAIN),
			'horizontal-flip' =>  __('Horizontal Flip', EG_TEXTDOMAIN),
			'vertical-flip' =>  __('Vertical Flip', EG_TEXTDOMAIN),
			'horizontal-flipbook' =>  __('Horizontal Flipbook', EG_TEXTDOMAIN),
			'vertical-flipbook' =>  __('Vertical Flipbook', EG_TEXTDOMAIN)
		);
		
		return $animations;
		
	}
	
	
	/**
	 * get grid animations
	 */
	public static function get_hover_animations($inout = false){
		if(!$inout){
			$animations = array(
				'none' => __(' None', EG_TEXTDOMAIN),
				'fade' => __('Fade', EG_TEXTDOMAIN),
				'flipvertical' => __('Flip Vertical', EG_TEXTDOMAIN),
				'fliphorizontal' => __('Flip Horizontal', EG_TEXTDOMAIN),
				'flipup' => __('Flip Up', EG_TEXTDOMAIN),
				'flipdown' => __('Flip Down', EG_TEXTDOMAIN),
				'flipright' => __('Flip Right', EG_TEXTDOMAIN),
				'flipleft' => __('Flip Left', EG_TEXTDOMAIN),
				'turn' => __('Turn', EG_TEXTDOMAIN),
				'slide' => __('Slide', EG_TEXTDOMAIN),				
				'slideleft' => __('Slide Left', EG_TEXTDOMAIN),
				'slideright' => __('Slide Right', EG_TEXTDOMAIN),
				'slideup' => __('Slide Up', EG_TEXTDOMAIN),
				'slidedown' => __('Slide Down', EG_TEXTDOMAIN),
				'skewleft' => __('Skew Left', EG_TEXTDOMAIN),
				'skewright' => __('Skew Right', EG_TEXTDOMAIN),
				'rollleft' => __('Roll Left', EG_TEXTDOMAIN),
				'rollright' => __('Roll Right', EG_TEXTDOMAIN),
				'falldown' => __('Fall Down', EG_TEXTDOMAIN),
				'rotatescale' => __('Rotate Scale', EG_TEXTDOMAIN),
				'zoomback' => __('Zoom from Back', EG_TEXTDOMAIN),
				'zoomfront' => __('Zoom from Front', EG_TEXTDOMAIN),			
				'flyleft' => __('Fly Left', EG_TEXTDOMAIN),
				'flyright' => __('Fly Right', EG_TEXTDOMAIN),
				'covergrowup' => __('Cover Grow', EG_TEXTDOMAIN)
			);
		}else{
			$animations = array(
				'none' => __(' None', EG_TEXTDOMAIN),
				'fade' => __('Fade In', EG_TEXTDOMAIN),
				'fadeout' => __('Fade Out', EG_TEXTDOMAIN),
				'flipvertical' => __('Flip Vertical In', EG_TEXTDOMAIN),
				'flipverticalout' => __('Flip Vertical Out', EG_TEXTDOMAIN),
				'fliphorizontal' => __('Flip Horizontal In', EG_TEXTDOMAIN),
				'fliphorizontalout' => __('Flip Horizontal Out', EG_TEXTDOMAIN),
				'flipup' => __('Flip Up In Out', EG_TEXTDOMAIN),
				'flipupout' => __('Flip Up Out', EG_TEXTDOMAIN),
				'flipdown' => __('Flip Down In', EG_TEXTDOMAIN),
				'flipdownout' => __('Flip Down Out', EG_TEXTDOMAIN),
				'flipright' => __('Flip Right In', EG_TEXTDOMAIN),
				'fliprightout' => __('Flip Right Out', EG_TEXTDOMAIN),
				'flipleft' => __('Flip Left In', EG_TEXTDOMAIN),
				'flipleftout' => __('Flip Left Out', EG_TEXTDOMAIN),
				'turn' => __('Turn In', EG_TEXTDOMAIN),
				'turnout' => __('Turn Out', EG_TEXTDOMAIN),
				'slideleft' => __('Slide Left In', EG_TEXTDOMAIN),
				'slideleftout' => __('Slide Left Out', EG_TEXTDOMAIN),
				'slideright' => __('Slide Right In', EG_TEXTDOMAIN),
				'sliderightout' => __('Slide Right Out', EG_TEXTDOMAIN),
				'slideup' => __('Slide Up In', EG_TEXTDOMAIN),
				'slideupout' => __('Slide Up Out', EG_TEXTDOMAIN),
				'slidedown' => __('Slide Down In', EG_TEXTDOMAIN),
				'slidedownout' => __('Slide Down Out', EG_TEXTDOMAIN),
				'skewleft' => __('Skew Left In', EG_TEXTDOMAIN),
				'skewleftout' => __('Skew Left Out', EG_TEXTDOMAIN),
				'skewright' => __('Skew Right In', EG_TEXTDOMAIN),
				'skewrightout' => __('Skew Right Out', EG_TEXTDOMAIN),
				'rollleft' => __('Roll Left In', EG_TEXTDOMAIN),
				'rollleftout' => __('Roll Left Out', EG_TEXTDOMAIN),
				'rollright' => __('Roll Right In', EG_TEXTDOMAIN),
				'rollrightout' => __('Roll Right Out', EG_TEXTDOMAIN),
				'falldown' => __('Fall Down In', EG_TEXTDOMAIN),
				'falldownout' => __('Fall Down Out', EG_TEXTDOMAIN),
				'rotatescale' => __('Rotate Scale In', EG_TEXTDOMAIN),
				'rotatescaleout' => __('Rotate Scale Out', EG_TEXTDOMAIN),
				'zoomback' => __('Zoom from Back In', EG_TEXTDOMAIN),
				'zoombackout' => __('Zoom from Back Out', EG_TEXTDOMAIN),
				'zoomfront' => __('Zoom from Front In', EG_TEXTDOMAIN),			
				'zoomfrontout' => __('Zoom from Front Out', EG_TEXTDOMAIN),			
				'flyleft' => __('Fly Left In', EG_TEXTDOMAIN),
				'flyleftout' => __('Fly Left Out', EG_TEXTDOMAIN),
				'flyright' => __('Fly Right In', EG_TEXTDOMAIN),
				'flyrightout' => __('Fly Right Out', EG_TEXTDOMAIN),
				'covergrowup' => __('Cover Grow In', EG_TEXTDOMAIN),
				'covergrowupout' => __('Cover Grow Out', EG_TEXTDOMAIN)
			);
		}
        
        asort($animations);
        
		return $animations;
	}
    
    /**
	 * get media animations (only out animations!)
	 */
    public static function get_media_animations(){
        
        $media_anim = array(
            'none' => __(' None', EG_TEXTDOMAIN),
            'flipverticalout' => __('Flip Vertical', EG_TEXTDOMAIN),
            'fliphorizontalout' => __('Flip Horizontal', EG_TEXTDOMAIN),
            'fliprightout' => __('Flip Right', EG_TEXTDOMAIN),
            'flipleftout' => __('Flip Left', EG_TEXTDOMAIN),
            'flipupout' => __('Flip Up', EG_TEXTDOMAIN),
            'flipdownout' => __('Flip Down', EG_TEXTDOMAIN),
            'shifttotop' => __('Shift To Top', EG_TEXTDOMAIN),            
            'turnout' => __('Turn', EG_TEXTDOMAIN),
            '3dturnright' => __('3D Turn Right', EG_TEXTDOMAIN),
            'pressback' => __('Press Back', EG_TEXTDOMAIN),
			'mediazoom' => __('Zoom', EG_TEXTDOMAIN),      
            'zoombackout' => __('Zoom to Back', EG_TEXTDOMAIN),
            'zoomfrontout' => __('Zoom to Front', EG_TEXTDOMAIN),            
            'zoomandrotate' => __('Zoom And Rotate', EG_TEXTDOMAIN)
        );
        
        //asort($media_anim);
        
        return $media_anim;
    }
	
	
	/**
	 * set basic columns if empty
	 */
	public static function set_basic_colums($columns){
	
		if(!isset($columns[0]) || intval($columns[0]) == 0) $columns[0] = 5;
		if(!isset($columns[1]) || intval($columns[1]) == 0) $columns[1] = 4;
		if(!isset($columns[2]) || intval($columns[2]) == 0) $columns[2] = 4;
		if(!isset($columns[3]) || intval($columns[3]) == 0) $columns[3] = 3;
		if(!isset($columns[4]) || intval($columns[4]) == 0) $columns[4] = 3;
		if(!isset($columns[5]) || intval($columns[5]) == 0) $columns[5] = 3;
		if(!isset($columns[6]) || intval($columns[6]) == 0) $columns[6] = 1;
		
		return $columns;
	}
	
	
	/**
	 * set basic columns width if empty
	 */
	public static function set_basic_colums_width($columns_width){
	
		if(!isset($columns_width[0]) || intval($columns_width[0]) == 0) $columns_width[0] = 1400;
		if(!isset($columns_width[1]) || intval($columns_width[1]) == 0) $columns_width[1] = 1170;
		if(!isset($columns_width[2]) || intval($columns_width[2]) == 0) $columns_width[2] = 1024;
		if(!isset($columns_width[3]) || intval($columns_width[3]) == 0) $columns_width[3] = 960;
		if(!isset($columns_width[4]) || intval($columns_width[4]) == 0) $columns_width[4] = 778;
		if(!isset($columns_width[5]) || intval($columns_width[5]) == 0) $columns_width[5] = 640;
		if(!isset($columns_width[6]) || intval($columns_width[6]) == 0) $columns_width[6] = 480;
		
		return $columns_width;
	}
	
	
	/**
	 * encode array into json for client side
	 */
	public static function jsonEncodeForClientSide($arr){
		$json = "";
		if(!empty($arr)){
			$json = json_encode($arr);
			$json = addslashes($json);
		}
		
		$json = "'".$json."'";
		
		return($json);
	}
	
	
	/**
	 * Get url to secific view.
	 */
	public static function getFontsUrl(){
		
		$link = admin_url('admin.php?page=themepunch-google-fonts');
		return($link);
	}
	
	
	/**
	 * Get url to secific view.
	 */
	public static function getViewUrl($viewName="",$urlParams="",$slug=""){
		$params = "";
        
		$plugin = Essential_Grid::get_instance();
		if($slug == "") $slug = $plugin->get_plugin_slug();
        
		if($viewName != "") $params = "&view=".$viewName;
		$params .= (!empty($urlParams)) ? "&".$urlParams : "";
		
		$link = admin_url( "admin.php?page=".$slug.$params);
		return($link);
	}
	
	
	/**
	 * Get url to secific view.
	 */
	public static function getSubViewUrl($viewName="",$urlParams="",$slug=""){
		$params = "";
        
		$plugin = Essential_Grid::get_instance();
		if($slug == "") $slug = $plugin->get_plugin_slug();
        
		if($viewName != "") $params = "-".$viewName;
		$params .= (!empty($urlParams)) ? "&".$urlParams : "";
		
		$link = admin_url( "admin.php?page=".$slug.$params);
		return($link);
	}
	
	
	/**
	 * Get Post Types + Custom Post Types
	 */
	public static function getPostTypesAssoc($arrPutToTop = array()){
		$arrBuiltIn = array("post"=>"post", "page"=>"page");
		
		$arrCustomTypes = get_post_types(array('_builtin' => false));
		
		//top items validation - add only items that in the customtypes list
		$arrPutToTopUpdated = array();
		foreach($arrPutToTop as $topItem){
			if(in_array($topItem, $arrCustomTypes) == true){
				$arrPutToTopUpdated[$topItem] = $topItem;
				unset($arrCustomTypes[$topItem]);
			}
		}
		
		$arrPostTypes = array_merge($arrPutToTopUpdated,$arrBuiltIn,$arrCustomTypes);
		
		//update label
		foreach($arrPostTypes as $key=>$type){
			$objType = get_post_type_object($type);
			
			if(empty($objType)){
				$arrPostTypes[$key] = $type;
				continue;
			}
			
			$arrPostTypes[$key] = $objType->labels->singular_name;
		}
		
		return($arrPostTypes);
	}
	
	
	/**
	 * Get post types with categories.
	 */
	public static function getPostTypesWithCatsForClient(){
		$arrPostTypes = self::getPostTypesWithCats();

		$globalCounter = 0;

		$arrOutput = array();
		foreach($arrPostTypes as $postType => $arrTaxWithCats){

			$arrCats = array();
			foreach($arrTaxWithCats as $tax){
				$taxName = $tax["name"];
				$taxTitle = $tax["title"];
				$globalCounter++;
				$arrCats["option_disabled_".$globalCounter] = "---- ".$taxTitle." ----";
				foreach($tax["cats"] as $catID=>$catTitle){
					$arrCats[$taxName."_".$catID] = $catTitle;
				}
			}//loop tax

			$arrOutput[$postType] = $arrCats;

		}//loop types

		return($arrOutput);
	}
	
	
	/**
	 * get array of post types with categories (the taxonomies is between).
	 * get only those taxomonies that have some categories in it.
	 */
	public static function getPostTypesWithCats(){
		$arrPostTypes = self::getPostTypesWithTaxomonies();
		
		$arrPostTypesOutput = array();
		foreach($arrPostTypes as $name=>$arrTax){

			$arrTaxOutput = array();
			foreach($arrTax as $taxName=>$taxTitle){
				$cats = self::getCategoriesAssoc($taxName);
				if(!empty($cats))
					$arrTaxOutput[] = array(
							 "name"=>$taxName,
							 "title"=>$taxTitle,
							 "cats"=>$cats);
			}
			
			$arrPostTypesOutput[$name] = $arrTaxOutput;
			
		}
		
		return($arrPostTypesOutput);
	}
	
	/**
	 * get current language code
	 */
	public static function get_current_lang_code(){
		$langTag = get_bloginfo('language');
		$data = explode('-', $langTag);
		$code = $data[0];
		return($code);
	}
	
	/**
	 * get post types array with taxomonies
	 */
	public static function getPostTypesWithTaxomonies(){
		$arrPostTypes = self::getPostTypesAssoc();
		
		foreach($arrPostTypes as $postType=>$title){
			$arrTaxomonies = self::getPostTypeTaxomonies($postType);
			$arrPostTypes[$postType] = $arrTaxomonies;
		}
		
		return($arrPostTypes);
	}
	
	
	/**	
	 * get post categories list assoc - id / title
	 */
	public static function getCategoriesAssoc($taxonomy = "category"){
		
		if(strpos($taxonomy,",") !== false){
			$arrTax = explode(",", $taxonomy);
			$arrCats = array();
			foreach($arrTax as $tax){
				$cats = self::getCategoriesAssoc($tax);
				$arrCats = array_merge($arrCats,$cats);
			}
			
			return($arrCats);
		}	
		
		//$cats = get_terms("category");
		$args = array("taxonomy"=>$taxonomy);
		
		//Essential_Grid_Wpml::disable_language_filtering();
		
		$cats = get_categories($args);
		
		//Essential_Grid_Wpml::enable_language_filtering();
		
		$arrCats = array();
		foreach($cats as $cat){
			$numItems = $cat->count;
			$itemsName = "items";
			if($numItems == 1)
				$itemsName = "item";
				
			$title = $cat->name . " ($numItems $itemsName)";
			
			$id = $cat->cat_ID;
			$id = Essential_Grid_Wpml::get_id_from_lang_id($id,$cat->taxonomy);
			
			$arrCats[$id] = $title;
		}
		return($arrCats);
	}
	
	
	/**
	 * get post type taxomonies
	 */
	public static function getPostTypeTaxomonies($postType){
		$arrTaxonomies = get_object_taxonomies(array('post_type' => $postType), 'objects');
		
		$arrNames = array();
		foreach($arrTaxonomies as $key=>$objTax){			
			$arrNames[$objTax->name] = $objTax->labels->name;
		}
		
		return($arrNames);
	}
	
	
	/**
	 * get first category from categories list
	 */
	private static function getFirstCategory($cats){
					
		foreach($cats as $key=>$value){
			if(strpos($key,"option_disabled") === false)
				return($key);
		}
		return("");
	}		
	
	
	/**
	 * set category by post type, with specific name (can be regular or woocommerce)
	 */
	public static function setCategoryByPostTypes($postTypes, $postTypesWithCats){
		
		//update the categories list by the post types
		if(strpos($postTypes, ",") !== false)
			$postTypes = explode(",",$postTypes);
		else
			$postTypes = array($postTypes);
		
		
		$arrCats = array();
		$isFirst = true;
			
		foreach($postTypes as $postType){
			$cats = array();
			foreach($postTypesWithCats as $postCats){
				if(array_key_exists($postType, $postCats)) $cats = $postCats;
			}
			if($isFirst == true){
				$firstValue = self::getFirstCategory($cats);
				$isFirst = false; 
			}
				
			$arrCats = array_merge($arrCats,$cats);
		}

		return($arrCats);
	}
	
	
	/**
	 * get posts by categorys/tags
	 */
	public static function getPostsByCategory($catID, $postTypes="any", $taxonomies="category", $pages = array(), $sortBy = self::SORTBY_ID, $direction = self::ORDER_DIRECTION_DESC, $numPosts=-1, $arrAddition = array()){ //category
		global $sitepress;
		
		//get post types
		if(strpos($postTypes,",") !== false){
			$postTypes = explode(",", $postTypes);
			if(array_search("any", $postTypes) !== false)
				$postTypes = "any";		
		}
		
		if(empty($postTypes))
			$postTypes = "any";
		
		if(strpos($catID,",") !== false)
			$catID = explode(",",$catID);
		else
			$catID = array($catID);
		
		$query = array(
			'order'=>$direction,
			'posts_per_page'=>$numPosts,
			'showposts'=>$numPosts,
			'post_type'=>$postTypes	
		);
		
		//add sort by (could be by meta)
		if(strpos($sortBy, "meta_num_") === 0){
			$metaKey = str_replace("meta_num_", "", $sortBy);
			$query["orderby"] = "meta_value_num";
			$query["meta_key"] = $metaKey;
		}else if(strpos($sortBy, "meta_") === 0){
			$metaKey = str_replace("meta_", "", $sortBy);
			$query["orderby"] = "meta_value";
			$query["meta_key"] = $metaKey;
		}else
			$query["orderby"] = $sortBy;
			
		//get taxonomies array
		$arrTax = array();
		if(!empty($taxonomies)){
			$arrTax = explode(",", $taxonomies);
		}
			
			
		if(!empty($taxonomies)){
		
			$taxQuery = array();
		
			//add taxomonies to the query
			if(strpos($taxonomies,",") !== false){	//multiple taxomonies
				$taxonomies = explode(",",$taxonomies);
				foreach($taxonomies as $taxomony){
					$taxArray = array(
						'taxonomy' => $taxomony,
						'field' => 'id',
						'terms' => $catID
					);			
					$taxQuery[] = $taxArray;
				}
			}else{		//single taxomony
				$taxArray = array(
					'taxonomy' => $taxonomies,
					'field' => 'id',
					'terms' => $catID
				);			
				$taxQuery[] = $taxArray;				
			}
			
			$taxQuery['relation'] = 'OR';
			
			$query['tax_query'] = $taxQuery;
		} //if exists taxanomies
		
		$query['suppress_filters'] = false;
		
		if(!empty($arrAddition) && is_array($arrAddition)){
			foreach($arrAddition as $han => $val){
				if(strtolower(substr($val, 0, 5)) == 'array') {
					$val = explode(',', str_replace(array('(', ')'), '', substr($val, 5)));
					$arrAddition[$han] = $val;
				}
			}
			$query = array_merge($query, $arrAddition);
		}
		
		$query = apply_filters('essgrid_get_posts', $query);
		
		$objQuery = new WP_Query($query);
		
		//select again the pages 
		if(is_array($postTypes) && in_array('page', $postTypes) && count($postTypes) > 1){ //Page is selected and also another custom category
			$query['post_type'] = 'page';
			unset($query['tax_query']); //delete category/tag filtering
			
			$objQueryPages = new WP_Query($query);
			if(is_array($objQueryPages) && is_array($objQuery))
				$objQuery = array_merge($objQuery, $objQueryPages);
			if(is_array($objQueryPages) && !is_array($objQuery))
				$objQuery = $objQueryPages;
		}
		
		//$arrPosts = $objQuery;
		$arrPosts = $objQuery->posts;
		
		foreach($arrPosts as $key=>$post){
			
			if(method_exists($post, "to_array"))
				$arrPost = $post->to_array();				
			else
				$arrPost = (array)$post;
			
			if($arrPost['post_type'] == 'page'){
				if(!empty($pages)){ //filter to pages if array is set
					$delete = true;
					foreach($pages as $page){
						if(!empty($page)){
							if($arrPost['ID'] == $page){
								$delete = false;
								break;
							}elseif(isset($sitepress)){ //WPML
								$current_main_id = icl_object_id( $arrPost['ID'], 'page', true, $sitepress->get_default_language() );
								if($current_main_id == $page){
									$delete = false;
									break;
								}
							}
						}
					}
					if($delete){ //if not wanted, go to next
						unset($arrPosts[$key]);
						continue;
					}
				}
			}
			/*
			$arrPostCats = self::getPostCategories($post, $arrTax);
			$arrPost["categories"] = $arrPostCats;
			*/
			$arrPosts[$key] = $arrPost;
		}
		
		$arrPosts = apply_filters('essgrid_modify_posts', $arrPosts);
		
		return($arrPosts);
	}
	
	
	/**
	 * Get taxonomies by post ID
	 */
	function get_custom_taxonomies_by_post_id($post_id){
		// get post by post id
		$post = get_post( $post_id );

		// get post type by post
		$post_type = $post->post_type;
		
		// get post type taxonomies
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		
		$terms = array();
		foreach ( $taxonomies as $taxonomy_slug => $taxonomy ){
			// get the terms related to post
			$c_terms = get_the_terms( $post->ID, $taxonomy_slug );
			if(!empty($c_terms)){
				$terms = $terms + $c_terms;
			}
			
		}
		
		if ( !empty( $terms ) ) {
			return $terms;
		}
		
		return array();
	}
	
	/**
	 * Receive all Posts by given IDs
	 */
	public static function get_posts_by_ids($ids){
		
		$query = array(
		   'post__in'      => $ids,
		   'post_type'=> 'any',
		   'numberposts' => count($ids)
		);
		
		$objQuery = get_posts($query);
		
		$arrPosts = $objQuery;

		foreach($arrPosts as $key=>$post){
			if(method_exists($post, "to_array"))
				$arrPost = $post->to_array();				
			else
				$arrPost = (array)$post;
			
			$arrPosts[$key] = $arrPost;
		}
		
		return $arrPosts;
	}
	
	
	/**
	 * get post categories by postID and taxonomies
	 * the postID can be post object or array too
	 */
	public static function getPostCategories($postID,$arrTax){
		
		if(!is_numeric($postID)){
			$postID = (array)$postID;
			$postID = $postID["ID"];
		}
			
		$arrCats = wp_get_post_terms( $postID, $arrTax);
		
		$arrCats = self::convertStdClassToArray($arrCats);
		return($arrCats);
	}
	
	
	/**
	 * Convert std class to array, with all sons
	 * @param unknown_type $arr
	 */
	public static function convertStdClassToArray($arr){
		$arr = (array)$arr;
		
		$arrNew = array();
		
		foreach($arr as $key=>$item){
			$item = (array)$item;
			$arrNew[$key] = $item;
		}
		
		return($arrNew);
	}
	
	
	/**
	 * get cats and taxanomies data from the category id's
	 */
	public static function getCatAndTaxData($catIDs){
	
		if(is_string($catIDs)){
			$catIDs = trim($catIDs);
			if(empty($catIDs))
				return(array("tax"=>"","cats"=>""));
			
			$catIDs = explode(",", $catIDs);
		}
		
		$strCats = "";
		$arrTax = array();
		foreach($catIDs as $cat){
			if(strpos($cat,"option_disabled") === 0)
				continue;
			
			$pos = strrpos($cat,"_");
			
			$taxName = substr($cat,0,$pos);
			$catID = substr($cat,$pos+1,strlen($cat)-$pos-1);
			
			//translate catID to current language if wpml exists
			$catID = Essential_Grid_Wpml::change_cat_id_by_lang($catID, $taxName);
			
			
			$arrTax[$taxName] = $taxName;
			if(!empty($strCats))
				$strCats .= ",";
				
			$strCats .= $catID;				
		}
		
		$strTax = "";
		foreach($arrTax as $taxName){
			if(!empty($strTax))
				$strTax .= ",";
				
			$strTax .= $taxName;
		} 
		
		$output = array("tax"=>$strTax,"cats"=>$strCats);
		
		return($output);
	}
	
	
	/**
	 * get categories list, copy the code from default wp functions
	 */
	public static function get_categories_html_list($catIDs, $do_link, $seperator = ',', $tax = false){
		global $wp_rewrite;
		
		$categories = self::get_categories_by_ids($catIDs, $tax);
		
		$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
		
		$thelist = '';
		
		if(!empty($categories)){
			foreach($categories as $key => $category){
				if($key > 0) $thelist .= $seperator;
				
				if($do_link)
					$thelist .= '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', EG_TEXTDOMAIN), $category->name ) ) . '" ' . $rel . '>' . $category->name.'</a>';
				else
					$thelist .= $category->name;
			}
		}
		
		return $thelist;
	}
	
	
	/**
	 * get categories by id's
	 */
	public static function get_categories_by_ids($arrIDs, $tax = false){			
		
		if(empty($arrIDs))
			return(array());
			
		$strIDs = implode(',', $arrIDs);
		
		$args['include'] = $strIDs;
		
		if($tax !== false)
			$args['taxonomy'] = $tax;
		
		$arrCats = get_categories( $args );
		return($arrCats);
	}
	
	
	/**
	 * get categories by id's
	 */
	public static function get_create_category_by_slug($cat_slug, $cat_name){			
		
		$cat = term_exists( $cat_slug, $cat_name );
		
		if ($cat !== 0 && $cat !== null){
			if(is_array($cat))
				return $cat['term_id'];
			else
				return $cat;
		}
		
		//create category if possible
		$new_name = ucwords(str_replace('-', ' ', $cat_slug));
		$category_array = wp_insert_term(
			$new_name,
			$cat_name,
			array(
				'description' => '',
				'slug'   => $cat_slug
			)
		);
		
		if(is_array($category_array) && !empty($category_array))
			return $category_array['term_id'];
		else
			return false;
			
		return false;
	}
	
	/**
	 * get post tags html list
	 */
	public static function get_tags_html_list($postID, $seperator = ','){
		$tagList = get_the_tag_list("",",","",$postID);
		return($tagList);
	}
	
	/**
	 * check if text has a certain tag in it
	 */
	public function text_has_certain_tag($string, $tag){
		return preg_match("/<" . $tag . "[^<]+>/", $string, $m) != 0;
	}
	
	
	/**
	 * check if text has a certain tag in it
	 */
	public static function output_demo_skin_html($data){
		$grid = new Essential_Grid();
		$base = new Essential_Grid_Base();
		$item_skin = new Essential_Grid_Item_Skin();
		
		$html = '';
		$preview = '';
		
		$preview_type = ($data['postparams']['source-type'] == 'custom') ? 'custom' : 'preview';
		
		$grid_id = (isset($data['id']) && intval($data['id']) > 0) ? intval($data['id']) : '-1';
		
		ob_start();
		$grid->output_essential_grid($grid_id, $data, $preview_type);
		$html = ob_get_contents();
		ob_clean();
		ob_end_clean();
		
		$skin = $base->getVar($data['params'], 'entry-skin', 0, 'i');
		if($skin > 0){
			ob_start();
			$item_skin->init_by_id($skin);
			$item_skin->output_item_skin('custom');
			$preview = ob_get_contents();
			ob_clean();
			ob_end_clean();
		}
		
		return array('html' => $html, 'preview' => $preview);
		
	}
	
	
	/**
	 * return all custom element fields
	 */
	public function get_custom_elements_for_javascript(){
		$meta = new Essential_Grid_Meta();
		$item_elements = new Essential_Grid_Item_Element();
		
		$elements = array(
					array('name' => 'custom-soundcloud', 'type' => 'input'),
					array('name' => 'custom-vimeo', 'type' => 'input'),
					array('name' => 'custom-youtube', 'type' => 'input'),
					array('name' => 'custom-html5-mp4', 'type' => 'input'),
					array('name' => 'custom-html5-ogv', 'type' => 'input'),
					array('name' => 'custom-html5-webm', 'type' => 'input'),
					array('name' => 'custom-image', 'type' => 'image'),
					array('name' => 'custom-text', 'type' => 'textarea'),
					array('name' => 'custom-ratio', 'type' => 'select'),
					array('name' => 'post-link', 'type' => 'input'),
					array('name' => 'custom-filter', 'type' => 'input')
					);
		
		$custom_meta = $meta->get_all_meta();
		if(!empty($custom_meta)){
			foreach($custom_meta as $cmeta){
				if($cmeta['type'] == 'text') $cmeta['type'] = 'input';
				
				$elements[] = array('name' => 'eg-cm-'.$cmeta['handle'], 'type' => $cmeta['type'], 'default' => @$cmeta['default']);
			}
		}
		
		$def_ele = $item_elements->getElementsForDropdown();
		
		foreach($def_ele as $type => $element){
			foreach($element as $handle => $name){
				$elements[] = array('name' => $handle, 'type' => 'input');
			}
		}
		
		return $elements;
	}
	
	
	/**
	 * return all media data of post that we may need
	 */
	public function get_post_media_source_data($post_id, $image_type){
		$ret = array();
		
		//$ret['featured-image'] = wp_get_attachment_url(get_post_thumbnail_id($post_id), $image_type);
		$feat_img = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $image_type);		
		$feat_img_full = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');		
		$ret['featured-image'] = ($feat_img !== false) ? $feat_img['0'] : '';
		$ret['featured-image-full'] = ($feat_img_full !== false) ? $feat_img_full['0'] : '';
		$ret['content-image'] = $this->get_first_content_image($post_id);
		
		//get Post Metas
		$values = get_post_custom($post_id);

		$ret['youtube'] = isset($values['eg_sources_youtube']) ? esc_attr($values['eg_sources_youtube'][0]) : '';
		$ret['vimeo'] = isset($values['eg_sources_vimeo']) ? esc_attr($values['eg_sources_vimeo'][0]) : '';
		//$ret['alternate-image'] = isset($values['eg_sources_image']) ? wp_get_attachment_url(esc_attr($values['eg_sources_image'][0]), $image_type) : '';
		if(isset($values['eg_sources_image'])){
			$alt_img = wp_get_attachment_image_src(esc_attr($values['eg_sources_image'][0]), $image_type);
			$alt_img_full = wp_get_attachment_image_src(esc_attr($values['eg_sources_image'][0]), 'full');
			$ret['alternate-image'] = ($alt_img !== false) ? $alt_img['0'] : '';
			$ret['alternate-image-full'] = ($alt_img_full !== false) ? $alt_img_full['0'] : '';
		}else{
			$ret['alternate-image'] = '';
		}
		
		$ret['iframe'] = isset($values['eg_sources_iframe']) ? esc_attr($values['eg_sources_iframe'][0]) : '';
		
		$ret['soundcloud'] = isset($values['eg_sources_soundcloud']) ? esc_attr($values['eg_sources_soundcloud'][0]) : '';
		
		$ret['html5']['mp4'] = isset($values['eg_sources_html5_mp4']) ? esc_attr($values['eg_sources_html5_mp4'][0]) : '';
		$ret['html5']['ogv'] = isset($values['eg_sources_html5_ogv']) ? esc_attr($values['eg_sources_html5_ogv'][0]) : '';
		$ret['html5']['webm'] = isset($values['eg_sources_html5_webm']) ? esc_attr($values['eg_sources_html5_webm'][0]) : '';
		
		return $ret;
		
	}
	
	
	/**
	 * return all media data of custom element that we may need
	 */
	public function get_custom_media_source_data($values, $image_type){
		$ret = array();
		
		$ret['youtube'] = isset($values['custom-youtube']) ? esc_attr($values['custom-youtube']) : '';
		$ret['vimeo'] = isset($values['custom-vimeo']) ? esc_attr($values['custom-vimeo']) : '';
		
		if(isset($values['custom-image'])){
			$alt_img = wp_get_attachment_image_src(esc_attr($values['custom-image']), $image_type);
			$alt_img_full = wp_get_attachment_image_src(esc_attr($values['custom-image']), 'full');
			$ret['alternate-image'] = ($alt_img !== false) ? $alt_img['0'] : '';
			$ret['alternate-image-full'] = ($alt_img_full !== false) ? $alt_img['0'] : '';
			$ret['featured-image'] = ($alt_img !== false) ? $alt_img['0'] : '';
			$ret['featured-image-full'] = ($alt_img_full !== false) ? $alt_img['0'] : '';
		}
		
		$ret['soundcloud'] = isset($values['custom-soundcloud']) ? esc_attr($values['custom-soundcloud']) : '';
		
		$ret['html5']['mp4'] = isset($values['custom-html5-mp4']) ? esc_attr($values['custom-html5-mp4']) : '';
		$ret['html5']['ogv'] = isset($values['custom-html5-ogv']) ? esc_attr($values['custom-html5-ogv']) : '';
		$ret['html5']['webm'] = isset($values['custom-html5-webm']) ? esc_attr($values['custom-html5-webm']) : '';
		
		return $ret;
		
	}
	
	
	/**
	 * set basic Order List for Main Media Source
	*/
	public static function get_media_source_order(){
	
		return array(	'featured-image' =>  array('name' => __('Featured Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'youtube' =>		 array('name' => __('YouTube Video', EG_TEXTDOMAIN), 'type' => 'video'),
						'vimeo' =>			 array('name' => __('Vimeo Video', EG_TEXTDOMAIN), 'type' => 'video'),
						'html5' =>			 array('name' => __('HTML5 Video', EG_TEXTDOMAIN), 'type' => 'video'),
						'soundcloud' =>		 array('name' => __('SoundCloud', EG_TEXTDOMAIN), 'type' => 'play-circled'),
						'alternate-image' => array('name' => __('Alternate Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'content-image' =>	 array('name' => __('First Content Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'iframe' =>			 array('name' => __('iFrame Markup', EG_TEXTDOMAIN), 'type' => 'align-justify')
						);
	}
	
	/**
	 * set basic Order List for Lightbox Source
	*/
	public static function get_lb_source_order(){
	
		return array(	'featured-image' =>  array('name' => __('Featured Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'youtube' =>		 array('name' => __('YouTube Video', EG_TEXTDOMAIN), 'type' => 'video'),
						'vimeo' =>			 array('name' => __('Vimeo Video', EG_TEXTDOMAIN), 'type' => 'video'),
						'html5' =>			 array('name' => __('HTML5 Video', EG_TEXTDOMAIN), 'type' => 'video'),
						'alternate-image' => array('name' => __('Alternate Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'content-image' =>	 array('name' => __('First Content Image', EG_TEXTDOMAIN), 'type' => 'picture')
						);
	}
	
	
	/**
	 * set basic Order List for Poster Orders
	 */
	public static function get_poster_source_order(){
	
		return array(	'featured-image' =>  array('name' => __('Featured Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'alternate-image' => array('name' => __('Alternate Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'content-image' =>	 array('name' => __('First Content Image', EG_TEXTDOMAIN), 'type' => 'picture'),
						'no-image' =>		 array('name' => __('No Image', EG_TEXTDOMAIN), 'type' => 'align-justify')
						);
	}

	
	/**
	 * retrieve the first content image in post text
	 */
	public function get_first_content_image($post_id) {
		$post = get_post($post_id);
		
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		
		if(isset($matches[1][0]))
			$first_img = $matches[1][0];

		if(empty($first_img)){ //Defines a default image
			$first_img = '';
		}
		return $first_img;
	}
	
	
	/**
	 * check if in the content exists a certain essential grid
	 * @since 1.0.6
	 */
	public function is_shortcode_with_handle_exist($grid_handle) {
		
		$content = get_the_content();
		$pattern = get_shortcode_regex();
        preg_match_all('/'.$pattern.'/s', $content, $matches);
		
		
		if(is_array($matches[2]) && !empty($matches[2])){ //
			foreach($matches[2] as $key => $sc){
				if($sc == 'ess_grid'){
					$attr = shortcode_parse_atts($matches[3][$key]);
					if(isset($attr['alias']))
						if($grid_handle == $attr['alias'])
							return true;
				}
			}
		}
		
		return false;
	}
}
?>