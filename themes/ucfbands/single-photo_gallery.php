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
					
					
					// GET GALLERY CATEGORY SLUG
					$category = get_the_category($post->ID);
					$category = $category[0]->slug;

						
				?>
            
                
                <!--// SECTION TITLE //-->
                <div class="section-title <?php echo $category; ?>" style="padding: 40px; padding-left: 0px; padding-right: 0px;">
                <?php /* <div class="section-title <?php echo $category; ?>" style="background-image: url('<?php echo $single_featured_image ?>');"> */ ?>
                
                    <a href="<?php echo get_permalink( $parent_id ); ?>"><h1><?php echo $parent_title //the_title(); ?></h1></a>
                
                </div><!-- /.section-title -->
                
                
                
                <!--// SECTION MENU //-->
				<?php
                	
										
					// Menu Options
					$subnav_options = array(
						//'theme_location'  => get_the_title(),
						'menu'            => $category,
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
            <div id="page-content-fluid" class="<?php echo $parent_slug; ?>">
            			
                                    
                    <?php 
					
						if( get_the_content() == '' )
						{
							echo '<div class="row">';

							echo '<div class="col-lg-3"><div class="block block-featured"><h2><i class="fa fa-calendar"></i> Coming Soon!</h2></div></div>';
							
							echo '</div><!-- /.row -->';
						}
						else
							the_content(); 
					
					?>

                    <?php wp_link_pages(); ?>
                
                
                 
           		
				
				<?php endwhile; ?>		
                
                
                
                <!--// IF NO CONTENT FOUND //-->
          		<div class="row">
                
                    
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

                
            	</div><!-- /.row -->
                
            </div><!-- /#page-content -->
			

<?php get_footer(); ?>