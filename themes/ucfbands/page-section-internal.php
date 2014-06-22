<?php
/*
Template Name: Section Page (Default)
*/
?>

<?php get_header(); ?>

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            
            <!--// FIX AFFIX //-->
            <div class="top-fixed">
            
            	
                <?php 
				
					// Parent Post
					$page_parents = get_post_ancestors( $post );
					$parent_id = $page_parents[0];
					
					$parent_post = get_post( $parent_id );
					$parent_slug = $parent_post->post_name;
					
										
					// Get Parent (Dashboard) Page Data
					$parent_title = get_the_title( $post->post_parent );
					
				
					// Get Featured Image Link // 
					$single_featured_image = wp_get_attachment_url( get_post_thumbnail_id( $parent_id ));
						
				?>
            
                
                <!--// SECTION TITLE //-->
                <div class="section-title <?php echo $parent_slug; ?>" style="background-image: url('<?php echo $single_featured_image ?>');">
                
                    <a href="<?php echo get_permalink( $parent_id ); ?>"><h1><?php echo $parent_title //the_title(); ?></h1></a>
                
                </div><!-- /.section-title -->
                
                
                
                <!--// SECTION MENU //-->
				<?php
                	
										
					// Menu Options
					$subnav_options = array(
						//'theme_location'  => get_the_title(),
						'menu'            => $parent_slug,
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
            <div id="page-content" class="<?php echo $parent_slug; ?>">
                
                <br> 
 
                <!--// PAGE TITLE //-->
                <h1 class="section-page-title"><?php the_title(); ?></h1>    
                
                
				<div class="row">
				
                    <!--// MAIN VERBIAGE //-->
                    <div class="col-lg-8">
                                        
                        <div class="block">
                                                    
                            
                            <section class="post_content clearfix" itemprop="articleBody">
                                
                                <?php // the_post_thumbnail( 'wpbs-featured' ); ?>
								
								<?php
                                
									if( get_the_content() == '' )
										echo '<h3><i class="fa fa-calendar"></i> Coming Soon!</h3>';
										
									else
										the_content(); 
											
								?>

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