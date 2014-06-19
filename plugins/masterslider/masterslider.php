<?php
/**
 * Master Slider WordPress Plugin.
 *
 * @package   MasterSlider
 * @author    averta [averta.net]
 * @license   LICENSE.txt
 * @link      http://masterslider.com
 * @copyright Copyright © 2014 averta
 *
 * Plugin Name:       Master Slider WP
 * Plugin URI:        http://codecanyon.net/item/master-slider-wordpress-responsive-touch-slider/7467925?ref=averta
 * Description:       Master Slider is the most advanced responsive HTML5 WordPress slider plugin with layer and Touch Swipe Navigation that works smoothly on devices too.
 * Version:           1.8.6
 * Author:            averta
 * Author URI:        http://averta.net
 * Text Domain:       masterslider
 * License URI:       LICENSE.txt
 * Domain Path:       /languages
 * Tested up to: 	  3.9.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('No Naughty Business Please !');
}

// Abort loading if WordPress is upgrading
if ( defined('WP_INSTALLING') && WP_INSTALLING ) {
    return;
}

/*----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/init/define.php' 		 );
require_once( plugin_dir_path( __FILE__ ) . 'public/class-master-slider.php' );

// Register hooks that are fired when the plugin is activated or deactivated.
register_activation_hook  ( __FILE__, array( 'Master_Slider', 'activate'   ) );
register_deactivation_hook( __FILE__, array( 'Master_Slider', 'deactivate' ) );

/*----------------------------------------------------------------------------*/
