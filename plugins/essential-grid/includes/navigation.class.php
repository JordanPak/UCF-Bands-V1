<?php
/**
 * @package   Essential_Grid
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/essential/
 * @copyright 2014 ThemePunch
 */
 
class Essential_Grid_Navigation {
	
    private $filter = array();
    private $filter_show = true;
    private $filter_type = 'singlefilters';
    private $sorting = array();
    private $sorting_start = 'none';
	
    private $layout = array('top-1' => array(), 'top-2' => array(), 'left' => array(), 'right' => array(), 'bottom-1' => array(), 'bottom-2' => array());
    private $specific_styles = array(
									'top-1' => array('margin-bottom' => 0, 'text-align' => 'center'),
									'top-2' => array('margin-bottom' => 0, 'text-align' => 'center'),
									'left' => array('margin-left' => 0),
									'right' => array('margin-right' => 0),
									'bottom-1' => array('margin-top' => 0, 'text-align' => 'center'),
									'bottom-2' => array('margin-top' => 0, 'text-align' => 'center'));
    
    private $styles = array();
	
    private $filter_all_text;
    private $spacing = false;
    
    
	public function __construct() {
		
		$this->filter_all_text = __('Filter - All', EG_TEXTDOMAIN);
		
		self::get_essential_navigation_skins();
	}
    
    
    /**
     * Return all Navigation skins
     */
    public static function get_essential_navigation_skins(){
        global $wpdb;
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_NAVIGATION_SKINS;
        $navigation_skins = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
		
		if($navigation_skins == false){ //empty, insert defaults again
			self::propagate_default_navigation_skins();
			$navigation_skins = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
		}
		
		return $navigation_skins; 
    }
    
    
    /**
	 * Get Navigation Skin by ID from Database
	 */
	public static function get_essential_navigation_skin_by_id($id = 0){
		global $wpdb;
		
		$id = intval($id);
		if($id == 0) return false;
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_NAVIGATION_SKINS;
		
		$skin = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
		
		
		if(!empty($skin)){
			$skin['css'] = Essential_Grid_Base::stripslashes_deep($skin['css']);
		}
		
		return $skin;
	}
    
    
    /**
	 * Get Navigation Skin by ID from Database
	 */
	public static function get_essential_navigation_skin_by_handle($handle = ''){
		global $wpdb;
		
		if($handle == '') return false;
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_NAVIGATION_SKINS;
		
		$skin = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s", $handle), ARRAY_A);
		
		if(!empty($skin)){
			$skin['css'] = Essential_Grid_Base::stripslashes_deep($skin['css']);
		}
		
		return $skin;
	}
	
	
	/**
	 * All default Item Skins
	 */
	public static function get_default_navigation_skins(){
		
		$default = array();
		
		include('assets/default-navigation-skins.php');
		
		return $default;
	}
	
	
	public static function propagate_default_navigation_skins(){
		$skins = self::get_default_navigation_skins();
		
		self::insert_default_navigation_skins($skins);
	}
	
	/**
	 * Insert Default Skin if they are not already installed
	 */
	public static function insert_default_navigation_skins($data){
		global $wpdb;
		
		$table_name = $wpdb->prefix . Essential_Grid::TABLE_NAVIGATION_SKINS;
		
        if(!empty($data)){
			foreach($data as $skin){
        
				//create
				$check = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s ", $skin['handle']), ARRAY_A);
				
				//check if exists, if no, create
				if(!empty($check)) continue;
				
				//insert if function did not return yet
				$response = $wpdb->insert($table_name, array('name' => $skin['name'], 'handle' => $skin['handle'], 'css' => $skin['css']));
            
			}
		}
		
	}
	
	
	/**
	 * Update / Save Navigation Skins
	 */
    public static function update_create_navigation_skin_css($data){
        global $wpdb;
        
        $table_name = $wpdb->prefix . Essential_Grid::TABLE_NAVIGATION_SKINS;
        if(isset($data['name'])){ //create new skin
            if(strlen($data['name']) < 2) return __('Invalid name. Name has to be at least 2 characters long.', EG_TEXTDOMAIN);
            if(strlen(sanitize_title($data['name'])) < 2) return __('Invalid name. Name has to be at least 2 characters long.', EG_TEXTDOMAIN);
            
        }else{
			if(isset($data['sid'])){
				if(intval($data['sid']) == 0) return __('Invalid Navigation Skin. Wrong ID given.', EG_TEXTDOMAIN);
			}else{
				return __('Invalid Navigation Skin. Wrong ID given.', EG_TEXTDOMAIN);
			}
        }
        
        
		
        if(!isset($data['skin_css']) || empty($data['skin_css'])) return __('No CSS found.', EG_TEXTDOMAIN);
        
        if(isset($data['sid']) && intval($data['sid']) > 0){ //update
			//check if entry with id exists, because this is unique
			$skin = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id != %s ", $data['sid']), ARRAY_A);
			
			//check if exists, if yes, update
			if(!empty($skin)){
				$response = $wpdb->update($table_name,
											array(
												'css' => stripslashes($data['skin_css'])
												), array('id' => $data['sid']));
											
				if($response === false) return __('Navigation skin could not be changed.', EG_TEXTDOMAIN);
				
				return true;
			}
		}else{
            
            //create
            $skin = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE handle = %s ", sanitize_title($data['name'])), ARRAY_A);
            
            //check if exists, if no, create
            if(!empty($skin)){
                return __('Navigation skin with chosen name already exist. Please use a different name.', EG_TEXTDOMAIN);
            }
            
            //insert if function did not return yet
            $response = $wpdb->insert($table_name, array('name' => $data['name'], 'handle' => sanitize_title($data['name']), 'css' => stripslashes($data['skin_css'])));
            
            if($response === false) return false;
            
            return true;
        }
    }
	
	
	/**
	 * Update / Save Navigation Skins
	 */
    public static function delete_navigation_skin_css($data){
        global $wpdb;
        
        $table_name = $wpdb->prefix . Essential_Grid::TABLE_NAVIGATION_SKINS;
        
		if(!isset($data['skin'])){
			return __('Invalid Navigation Skin. Wrong ID given.', EG_TEXTDOMAIN);
		}
        
		//check if entry with id exists, because this is unique
		$response = $wpdb->delete($table_name, array('handle' => $data['skin']));
		
		if($response === false) return __('Navigation Skin not be deleted', EG_TEXTDOMAIN);
		
		return true;
    }
	
	
	/**
	 * Set all Layout Elements
	 */
	public function set_layout($layout){
		if(!empty($layout)){
			foreach($layout as $type => $position){
				if(!empty($position)){
					$pos = current(array_keys($position));
					
					$this->layout[$pos][$layout[$type][$pos]] = $type;
				}
			}
			
			foreach($this->layout as $key => $val)
				ksort($this->layout[$key]);
			
		}
	}
	
	
	/**
	 * Set specific
	 */
	public function set_specific_styles($layout){
		
		$this->specific_styles = $layout;
		
	}
	
	
	/**
	 * Set specific
	 */
	public function set_filter_text($text){
		
		$this->filter_all_text = $text;
		
	}
	
	
	/**
	 * Output Container
	 */
	public function output_layout($layout_container, $spacing = 0){
		$fclass = ($this->filter_type != false) ? ' esg-'.$this->filter_type : '';
		
		if(!empty($this->layout[$layout_container])){
			echo '<article class="esg-filters'.$fclass;
			if($layout_container == 'left') echo ' esg-navbutton-solo-left';
			if($layout_container == 'right') echo ' esg-navbutton-solo-right';
			
			echo '"';
			echo ' style="';
			if(!empty($this->styles)){
				foreach($this->styles as $style => $value){
					echo $style.': '.$value.'; ';
				}
			}
			
			if(!empty($this->specific_styles[$layout_container])){
				foreach($this->specific_styles[$layout_container] as $style => $value){
					echo $style.': '.$value.'; ';
				}
			}
			
			echo '"';
			echo '>';
			
			$num = count($this->layout[$layout_container]) - 1;
			
			foreach($this->layout[$layout_container] as $key => $what){
				if($num >= 1 && $key != $num){
					$important = ($what == 'right' || $what == 'left') ? ' !important' : ''; //set important if we are the arrows, because they have already !important in settings.css
					$this->spacing = ' style="margin-right: '.$spacing.'px'.$important.';';
					if($what == 'right' || $what == 'left') $this->spacing .= ' display: none;'; //hide navigation buttons left & right at start
					$this->spacing .= '"';
				}else{
					$this->spacing = false;
				}
					
				switch($what){
					case 'sorting':
						self::output_sorting();
					break;
					case 'cart':
						self::output_cart();
					break;
					case 'left':
						self::output_navigation_left();
					break;
					case 'right':
						self::output_navigation_right();
					break;
					case 'pagination':
						self::output_pagination();
					break;
					case 'filter':
						self::output_filter_unwrapped();
					break;
					
				}
			}
			
			echo '</article>';
			echo '<div class="esg-clear-no-height"></div>';
		}
	}
	
	
    public function set_filter($data){
        $this->filter = $data + $this->filter; //merges the array and preserves the key
        
		arsort($this->filter);
    }
	
	
    public function set_orders($data){
        $this->sorting = $data + $this->sorting; //merges the array and preserves the key
        
		arsort($this->sorting);
    }
	
	
    public function set_orders_start($start_by){

		$this->sorting_start = $start_by;

    }
    
    
    public function set_filter_type($type){
        if($type == 'single'){
            $this->filter_type = 'singlefilters';
        }elseif($type == 'multi'){
			$this->filter_type = 'multiplefilters';
        }else{
			$this->filter_type = false;
		}
    }
    
    
    public function set_style($name, $value){
        $this->styles[$name] = $value;
    }
    
    
    public function output_filter($demo = false){
        if(!$this->filter_show || $this->filter_type == false) return true;
        
        echo '<!-- THE FILTERING, SORTING AND WOOCOMMERCE BUTTONS -->';
		echo '<article class="esg-filters esg-'.$this->filter_type.'"';
		
		if(!empty($this->styles)){
			echo ' style="';
			foreach($this->styles as $style => $value){
				echo $style.': '.$value.'; ';
			}
			echo '"';
		}
		
		echo '><!-- USE esg-multiplefilters FOR MIXED FILTERING, AND esg-singlefilters FOR SINGLE FILTERING -->';
        echo '<!-- THE FILTER BUTTONS -->';
        echo '<div class="esg-filter-wrapper">';
        echo '<div class="esg-filterbutton selected esg-allfilter" data-filter="filterall" data-fid="-1" ><span>'.$this->filter_all_text.'</span></div>';
		
		if($demo === 'skinchoose'){
            echo '<div class="esg-filterbutton" data-filter="filter-selectedskin"><span>'.__('Selected Skin', EG_TEXTDOMAIN).'</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
        }
        if($demo !== false){
            echo '<div class="esg-filterbutton" data-filter="filter-favorite"><span>'.__('Favorites', EG_TEXTDOMAIN).'</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
        }
		
        if(!empty($this->filter)){
            foreach($this->filter as $filter_id => $filter){
                $filter_text = ($demo !== false) ? self::translate_demo_filter($filter['slug']) : $filter['name'];
                echo '<div class="esg-filterbutton" data-fid="'.$filter_id.'" data-filter="filter-'.sanitize_key($filter['slug']).'"><span>'.$filter_text.'</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
            }
        }
        echo '</div>';

        //self::output_sorting();
        //self::output_cart();
        
        echo '<div class="clear"></div>';

        echo '</article><!-- END OF FILTERING, SORTING AND  CART BUTTONS -->';
            
        echo '<div class="clear eg-filter-clear"></div>';
        
    }
    
	
	public function output_filter_unwrapped($demo = false){
        
        echo '<!-- THE FILTERING, SORTING AND WOOCOMMERCE BUTTONS -->';
		echo '<!-- THE FILTER BUTTONS -->';
        echo '<div class="esg-filter-wrapper"';
		if($this->spacing !== false) echo $this->spacing;
		echo '>';
        echo '<div class="esg-filterbutton selected esg-allfilter" data-filter="filterall" data-fid="-1" ><span>'.$this->filter_all_text.'</span></div>';
        if($demo){
            echo '<div class="esg-filterbutton" data-filter="filter-favorite"><span>'.__('Favorites', EG_TEXTDOMAIN).'</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
        }
        if(!empty($this->filter)){
            foreach($this->filter as $filter_id => $filter){
                $filter_text = ($demo) ? self::translate_demo_filter($filter['slug']) : $filter['name'];
                echo '<div class="esg-filterbutton" data-fid="'.$filter_id.'" data-filter="filter-'.sanitize_key($filter['slug']).'"><span>'.$filter_text.'</span><span class="esg-filter-checked"><i class="eg-icon-ok-1"></i></span></div>';
            }
        }
        
        echo '</div>';
        
    }
	
    
    public function output_sorting(){
        
		if(!empty($this->sorting)){
			
			echo '<!-- THE SORTING BUTTON -->';
			echo '<div class="esg-sortbutton-wrapper"';
			if($this->spacing !== false) echo $this->spacing;
			echo '>';
			echo '<div class="esg-sortbutton"><span>'.__('Sort By ', EG_TEXTDOMAIN).'</span><span class="sortby_data">'.$this->set_sorting_text($this->sorting_start).'</span>';
			echo '<select class="esg-sorting-select">';
			foreach($this->sorting as $sort){
				echo '<option value="'.$this->set_sorting_value($sort).'">'.$this->set_sorting_text($sort).'</option>';
			}
			echo '</select>';
			echo '</div><div class="esg-sortbutton-order eg-icon-down-open tp-asc"></div>';
			echo '</div><!-- END OF SORTING BUTTON -->';
		}
        
    }
	
	
	public function set_sorting_text($san_text){
		switch($san_text){
			case 'date':
				$san_text = __('Date', EG_TEXTDOMAIN);
			break;
			case 'title':
				$san_text = __('Title', EG_TEXTDOMAIN);
			break;
			case 'excerpt':
				$san_text = __('Excerpt', EG_TEXTDOMAIN);
			break;
			case 'id':
				$san_text = __('ID', EG_TEXTDOMAIN);
			break;
			case 'slug':
				$san_text = __('Slug', EG_TEXTDOMAIN);
			break;
			case 'author':
				$san_text = __('Author', EG_TEXTDOMAIN);
			break;
			case 'last-modified':
				$san_text = __('Last Modified', EG_TEXTDOMAIN);
			break;
			case 'number-of-comments':
				$san_text = __('Comments', EG_TEXTDOMAIN);
			break;
			case 'meta_num_total_sales':
				$san_text = __('Total Sales', EG_TEXTDOMAIN);
			break;
			case 'meta_num__regular_price':
				$san_text = __('Regular Price', EG_TEXTDOMAIN);
			break;
			case 'meta_num__sale_price':
				$san_text = __('Sale Price', EG_TEXTDOMAIN);
			break;
			case 'meta__featured':
				$san_text = __('Featured', EG_TEXTDOMAIN);
			break;
			case 'meta__sku':
				$san_text = __('SKU', EG_TEXTDOMAIN);
			break;
			case 'meta_num_stock':
				$san_text = __('In Stock', EG_TEXTDOMAIN);
			break;
			default:
				$san_text = ucfirst($san_text);
			break;
		}
		return $san_text;
	}
	
	
	public function set_sorting_value($san_handle){
		switch($san_handle){
			case 'meta_num_total_sales':
				$san_handle = 'total-sales';
			break;
			case 'meta_num__regular_price':
				$san_handle = 'regular-price';
			break;
			case 'meta_num__sale_price':
				$san_handle = 'sale-price';
			break;
			case 'meta__featured':
				$san_handle = 'featured';
			break;
			case 'meta__sku':
				$san_handle = 'sku';
			break;
			case 'meta_num_stock':
				$san_handle = 'in-stock';
			break;
		}
		return $san_handle;
	}
	
	
    public function output_cart(){

		if(!Essential_Grid_Woocommerce::is_woo_exists()) return true;

		echo '<!-- THE CART BUTTON -->';
		echo '<div class="esg-cartbutton-wrapper"';
		if($this->spacing !== false) echo $this->spacing;
		echo '>';
		
		echo '<div class="esg-cartbutton">';
		echo '<a href="'.esc_url(WC()->cart->get_cart_url()).'">';
		echo '<i class="eg-icon-basket"></i><span class="ess-cart-content">';
		echo WC()->cart->get_cart_contents_count();
		echo __(' items - ', EG_TEXTDOMAIN);
		wc_cart_totals_subtotal_html();
		echo '</span>';
		echo '</a>';
		echo '</div>';//<a href="#"><div class="esg-sortbutton-order eg-icon-down-open tp-asc"></div></a>
		echo '</div><!-- END OF CART BUTTON -->';

    }
    
    
    public function output_navigation(){
        echo '<!-- THE NAVIGATION BUTTONS -->';
        echo '<article class="navigationbuttons">';
		self::output_navigation_left();
		self::output_navigation_right();
        echo '</article><!-- END OF THE NAVIGATION BUTTONS -->';
    }
	
	
	public function output_navigation_left(){
        echo '<div class="esg-navigationbutton esg-left" ';
		if($this->spacing !== false) echo $this->spacing;
		echo '><i class="eg-icon-left-open"></i></div>';
	}
	
	
	public function output_navigation_right(){
        echo '<div class="esg-navigationbutton esg-right" ';
		if($this->spacing !== false) echo $this->spacing;
		echo '><i class="eg-icon-right-open"></i></div>';
	}
    
	
    public function output_pagination(){
        echo '<!-- THE PAGINATION CONTAINER. PAGE BUTTONS WILL BE ADDED ON DEMAND AUTOMATICALLY !! -->';
        echo '<div class="esg-pagination"';
		if($this->spacing !== false) echo $this->spacing;
		echo '></div><!-- END OF THE PAGINATION -->';
    }
    
    
    public function output_navigation_skin($handle){
        $css = self::get_essential_navigation_skin_by_handle($handle);
		
		if($css !== false){
			echo '<style type="text/css">'."\n";
			echo $css['css']."\n";
			echo '</style>'."\n";
		}
    }
    
    
    public static function output_navigation_skins(){
        $skins = self::get_essential_navigation_skins();
		
		$css = '';
		
		if(!empty($skins)){
			foreach($skins as $skin){
				$css .= '<style class="navigation-skin-css-'.$skin['id'].'" type="text/css">'."\n";
				$css .= $skin['css']."\n\n";
				$css .= '</style>'."\n";
			}
		}
		
		return $css;
    }
    
    
    private function translate_demo_filter($name){
        
        $post = Essential_Grid_Item_Element::getPostElementsArray();
        $event = Essential_Grid_Item_Element::getEventElementsArray();
		$woocommerce = array();
		if(Essential_Grid_Woocommerce::is_woo_exists()){
			$tmp_wc = Essential_Grid_Woocommerce::get_meta_array();
			
			foreach($tmp_wc as $handle => $wc_name){
				$woocommerce[$handle]['name'] = $wc_name;
			}
		}
		
        if(array_key_exists($name, $post)) return $post[$name]['name'];
        if(array_key_exists($name, $event)) return $event[$name]['name'];
        if(array_key_exists($name, $woocommerce)) return $woocommerce[$name]['name'];
        
        return ucwords($name);
    }
	
	
	
}