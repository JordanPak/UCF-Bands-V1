<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package UCFBands
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
    
    <head>
    
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        
        <?php wp_head(); ?>
        
    </head>


<body>

	<!-- TOP LINE -->
    <div class="top-line"></div>

	<!--// WRAPPER //-->
    <div id="wrapper">
		
        
        <!--// NAV BAR (TOP) //-->
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
            
            <div class="navbar-header">
                
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                </button>
                
                <a class="navbar-brand" href="<?php echo home_url('/'); ?>"><img src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/logo-header.png"></a>
            
            </div><!-- /.navbar-header -->

			
            <!--// SIDEBAR NAVIGATION //-->
            <div class="navbar-default navbar-static-side" role="navigation">
            	

                <!--// MAIN LOGO //-->
                <a class="logo-header" href="<?php echo home_url('/'); ?>"><img src="<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/logo-header.png"></a>
            
                
                <!--<div class="sidebar-collapse">
                    <ul class="nav" id="side-menu">-->
                        
						<?php 
						    
							// PRIMARY MENU OPTIONS //              
                            $defaults = array(
                                'theme_location'  => 'primary',
                                'container'       => 'div',
                                'container_class' => 'sidebar-collapse',
                                'menu_class'      => 'nav',
                                'menu_id'         => 'side-menu',
								'fallback_cb'	  => false
                            );
                            
                            
							// PRIMARY MENU //
							wp_nav_menu( $defaults );
                            
                        ?>                        
                    
                    <!--</ul>
                    <!-- /#side-menu -->
                    
                <!--</div>-->
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>


		
        <!--// PAGE WRAPPER //-->
        <div id="page-wrapper">          
            
</body>



<?php /*  ORIGINAL 
<body <?php body_class(); ?>>

<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'ucfbands' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
		</div>

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle"><?php _e( 'Primary Menu', 'ucfbands' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">

*/ ?>