<?php
/*
Template Name: Ensemble Dashboard
*/
?>

<?php get_header(); ?>

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            
            <!--// FIX AFFIX //-->
            <div class="top-fixed">
            
            	
                <?php 
				
					// Get Featured Image Link // 
					$single_featured_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
						
				?>
            
                
                <!--// PAGE TITLE //-->
                <div class="section-title" style="background-image: url('<?php echo $single_featured_image ?>');">
                
                    <h1 style="cursor: default;"><?php the_title(); ?></h1>
                
                </div><!-- /.section-title -->
                
                
                
                <!--// SECTION MENU //-->
				<?php
                	
										
					// Menu Options
					$subnav_options = array(
						//'theme_location'  => get_the_title(),
						'menu'            => get_the_title(),
						'container'       => 'nav',
						'container_class' => 'sub',
						'menu_class'      => 'sub',
						'menu_id'         => '',
						'echo'            => true,
						'fallback_cb'     => 'wp_page_menu',
						'depth'           => 2,
						'walker'          => new wp_bootstrap_navwalker()
					);

					
					// Output Menu
					wp_nav_menu( $subnav_options );
                
                ?>                
                
                                    
            </div><!-- /.top-fixed -->

            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
                <br> 
                
				<div class="row">
				
                    <!--// MAIN VERBIAGE //-->
                    <div class="col-lg-8">
                                        
                        <div class="block">
                                                    
                            
                            <section class="post_content clearfix" itemprop="articleBody">
                                
                                <?php // the_post_thumbnail( 'wpbs-featured' ); ?>
								
								<?php the_content(); ?>

								<?php wp_link_pages(); ?>
                        
                            </section> <!-- end article section -->
                            
                        
                            <?php comments_template('',true); ?>
                            
                  
				  <?php endwhile; ?>		
                            
                            
							<?php else : ?>
                            
                            <article id="post-not-found">
                                
                                <header>
                                    <h1><?php _e("Not Found", "wpbootstrap"); ?></h1>
                                </header>
                                
                                <section class="post_content">
                                    <p><?php _e("Sorry, but the requested resource was not found on this site.", "wpbootstrap"); ?></p>
                                </section>
                                
                                <footer>
                                </footer>
                            </article>
                            
                            <?php endif; ?>


                        </div><!-- /.block -->
                                            
                    </div><!-- / Main Verbiage -->   
                    
                    
                    
					<?php get_sidebar(); // sidebar 1 ?>
                                                        
                
                
                
            	</div><!-- /.row -->
                
            </div><!-- /#page-content -->
			

<?php get_footer(); ?>