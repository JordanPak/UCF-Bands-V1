<?php
/*
Template Name: Contact
*/
?>

<?php get_header(); ?>
            
			
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
                <br> 


				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                
                <!--// PAGE TITLE //-->
                <h1 class="page-title"><?php the_title(); ?></h1>    
                                
				<div class="row">
				
                                    
                    <section class="post_content clearfix" itemprop="articleBody">
                        
                        <?php 
                                    
                            if( get_the_content() == '' )
                                echo '<h3><i class="fa fa-calendar"></i> Coming Soon!</h3>';
                                
                            else
                                the_content(); 
                                    
                        ?>
                        
                        
                        <!--// GOOGLE MAP //-->
                        <div class="col-lg-6">
                        	<div class="block">
                                
                                <iframe width="100%" height="600px%" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=University%20of%20Central%20Florida%20Bands%20Department%20of%20Music&key=AIzaSyBwRMGuuEA0HhosFyJCAGz6-AY1de5JKHc"></iframe>
                                
                            </div>
                        </div>
                        
                        
                        
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

            
                
            	</div><!-- /.row -->
                
            </div><!-- /#page-content -->


<?php get_footer(); ?>