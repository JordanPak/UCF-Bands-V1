<?php

if ( ! class_exists('WeDevs_Settings_API' ) )
    require_once ( 'class-settings-api.php' );

/**
 * MasterSlider Setting page
 *
 * @author Tareq Hasan
 */
if ( !class_exists('MSP_Settings' ) ):

class MSP_Settings {

    private $settings_api;

    function __construct() {

        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );
        add_action( 'admin_action_msp_envato_license', array( $this, 'envato_license_updated' ) );
    }


    function admin_init() {
        
        $this->page_init();

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields  ( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }


    function page_init() {

        $page    = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : '';
        $updated = isset( $_REQUEST['settings-updated'] ) ? $_REQUEST['settings-updated'] : '';

        if( MSWP_SLUG.'-setting' == $page && 'true' == $updated ) {
            
            $envato_username        = $this->settings_api->get_option( 'username', 'msp_envato_license' );
            $envato_api_key         = $this->settings_api->get_option( 'api_key' , 'msp_envato_license' );
            $envato_purchase_code   = $this->settings_api->get_option( 'purchase_code' , 'msp_envato_license' );
            // activate the license
            if( $is_actived = msp_maybe_activate_license( $envato_username, $envato_api_key, $envato_purchase_code ) )
                add_action( 'admin_notices', array( $this, 'activation_notice' ) );
            else
                add_action( 'admin_notices', array( $this, 'deactivation_notice' ) );
        }
    }


    public function activation_notice () {
        printf( '<div class="update-nag" style="border-left-color:#7ad03a;" >%s</div>', __( 'Your license activated successfully. Thank you!', MSWP_TEXT_DOMAIN ) );
    }


    public function deactivation_notice() {
        printf( '<div class="update-nag">%s</div>', __( 'The license activation failed, the purchase code is not valid.', MSWP_TEXT_DOMAIN ) );
    }


    function admin_menu() {
        
        add_submenu_page(
            MSWP_SLUG,
            __( 'Settings' , MSWP_TEXT_DOMAIN ),
            __( 'Settings' , MSWP_TEXT_DOMAIN ),
            apply_filters( 'masterslider_setting_capability', 'manage_options' ),
            MSWP_SLUG . '-setting',
            array( $this, 'render_setting_page' )
        );
    }

    function get_settings_sections() {
        $sections = array(
            
            array(
                'id' => 'msp_general_setting',
                'title' => __( 'General Settings', MSWP_TEXT_DOMAIN )
            )
        );

        if( ! apply_filters( MSWP_SLUG.'_disable_auto_update', 0 ) ) {
            $sections[] = array(
                'id' => 'msp_envato_license',
                'title' => __( 'License Activation', MSWP_TEXT_DOMAIN ),
                'desc'  => __('To activate automatic update for master slider a valid purchase code is required.', MSWP_TEXT_DOMAIN )
            );
        }

        $woo_enabled = msp_is_plugin_active( 'woocommerce/woocommerce.php' );
        $woo_section_desc = $woo_enabled ? '': __( 'You need to install and activate WooCommerce plugin to use following options.', MSWP_TEXT_DOMAIN );

        $sections[] = array(
            'id' => 'msp_woocommerce',
            'title' => __( 'WooCommerce Setting', MSWP_TEXT_DOMAIN ),
            'desc'  => $woo_section_desc
        );

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        
        $settings_fields = array();
            
        $settings_fields['msp_general_setting'] = array(
            array(
                'name'  => 'hide_info_table',
                'label' => __( 'Hide info table', MSWP_TEXT_DOMAIN ),
                'desc'  => __( 'If you want to hide "Latest video tutorials" table on master slider admin panel check this field.', MSWP_TEXT_DOMAIN ),
                'type'  => 'checkbox'
            )
        );
            /*
            'msp_general_setting' => array(
                array(
                    'name' => 'text_val',
                    'label' => __( 'Text Input (integer validation)', 'wedevs' ),
                    'desc' => __( 'Text input description', 'wedevs' ),
                    'type' => 'text',
                    'default' => 'Title',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name' => 'textarea',
                    'label' => __( 'Textarea Input', 'wedevs' ),
                    'desc' => __( 'Textarea description', 'wedevs' ),
                    'type' => 'textarea'
                ),
                array(
                    'name' => 'checkbox',
                    'label' => __( 'Checkbox', 'wedevs' ),
                    'desc' => __( 'Checkbox Label', 'wedevs' ),
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'radio',
                    'label' => __( 'Radio Button', 'wedevs' ),
                    'desc' => __( 'A radio button', 'wedevs' ),
                    'type' => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
                array(
                    'name' => 'multicheck',
                    'label' => __( 'Multile checkbox', 'wedevs' ),
                    'desc' => __( 'Multi checkbox description', 'wedevs' ),
                    'type' => 'multicheck',
                    'options' => array(
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                        'four' => 'Four'
                    )
                ),
                array(
                    'name' => 'selectbox',
                    'label' => __( 'A Dropdown', 'wedevs' ),
                    'desc' => __( 'Dropdown description', 'wedevs' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
                array(
                    'name' => 'password',
                    'label' => __( 'Password', 'wedevs' ),
                    'desc' => __( 'Password description', 'wedevs' ),
                    'type' => 'password',
                    'default' => ''
                ),
                array(
                    'name' => 'file',
                    'label' => __( 'File', 'wedevs' ),
                    'desc' => __( 'File description', 'wedevs' ),
                    'type' => 'file',
                    'default' => ''
                )
            ),*/

        if( ! apply_filters( MSWP_SLUG.'_disable_auto_update', 0 ) ) {
            
            $settings_fields['msp_envato_license'] = array(

                    array(
                        'name'      => 'username',
                        'label'     => __( 'Your Envato Username'     , MSWP_TEXT_DOMAIN ),
                        'desc'      => '',
                        'type'      => 'text',
                        'default'   => ''
                    ),
                    array(
                        'name'      => 'api_key',
                        'label'     => __( 'Your Secret API Key' , MSWP_TEXT_DOMAIN ),
                        'desc'      => __( 'To find your API key, navigate to your envato account, select settings from the account dropdown, then navigate to the API Keys tab. <a href="http://codecanyon.net/help/api" target="_blank">More info ..</a>.', MSWP_TEXT_DOMAIN ),
                        'type'      => 'password',
                        'default'   => ''
                        ),
                    array(
                        'name'      => 'purchase_code',
                        'label'     => __( 'Master Slider Purchase Code' , MSWP_TEXT_DOMAIN ),
                        'desc'      => __( 'Please enter purchase code for your Master Slider', MSWP_TEXT_DOMAIN ) . sprintf( ' (<a href="http://support.averta.net/envato/knowledgebase/find-item-purchase-code/" target="_blank" >%s</a>)',
                                                                                                                              __( "How to find your Item's Purchase Code", MSWP_TEXT_DOMAIN ) ),
                        'type'      => 'text',
                        'default'   => ''
                    )
            );
        }

        $settings_fields['msp_woocommerce'] = array(

                array(
                    'name' => 'enable_single_product_slider',
                    'label' => __( 'Enable slider in product single page', 'wedevs' ),
                    'desc' => __( 'Replace woocommerce default product slider in product single page with Masterslider', MSWP_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                )
        );

        return $settings_fields;
    }

    function render_setting_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}

endif;

$settings = new MSP_Settings();