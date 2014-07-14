<?php
/*
Template Name: Full Width Page (Section Title) (Archive)
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
                
                    <h1><?php the_title(); ?></h1>
                
                </div><!-- /.section-title -->
                
                                    
            </div><!-- /.top-fixed -->
            

            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                                
					<?php
                    
                            if( get_the_content() == '' )
                                echo '<div class="col-lg-3"><div class="block block-featured"><h2><i class="fa fa-calendar"></i> Coming Soon!</h2></div></div>';
                                
                            else
                                the_content(); 
                    
                    ?>
                            
                      
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

                
            </div><!-- /#page-content -->
			

<?php get_footer();