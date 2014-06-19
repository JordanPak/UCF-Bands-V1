<?php

if ( ! class_exists( 'MSP_Main_Widget' ) ) :


class MSP_Main_Widget extends MSP_Widget {

	public $fields   = array(
                            
                            array(
                                'name'    => 'Title',
                                'id'      => 'title',
                                'type'    => 'textbox',
                                'value'   => ''
                            ),
                            array(
                                'name'    => 'Select a Slider :',
                                'id'      => 'id',
                                'type'    => 'select',
                                'value'   => '-1',
                                'options' => array()
                            )
                            
                        );

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 */
	public function __construct() {

		$this->fields['1']['options'] = get_masterslider_names();

		parent::__construct(
			'master-slider-main-widget',
			__( 'Master Slider Widget', MSWP_TEXT_DOMAIN ),
			array(
				'classname'  => 'master-slider-main-widget',
				'description' => __( 'Display a Master Slider', MSWP_TEXT_DOMAIN )
			)
		);

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget on front-end.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );
		echo $before_widget;

		echo get_masterslider( $instance['id'] );
		
		echo $after_widget;
	} // end widget


} // end class


endif;


// init the widget
add_action( 'widgets_init', create_function( '', 'register_widget("MSP_Main_Widget");' ) );