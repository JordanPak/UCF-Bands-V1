<?php get_header(); ?>

            
            <!--// FIX AFFIX //-->
            <div class="top-fixed">
            
            	            
                <!--// PAGE TITLE //-->
                <div class="section-title" style="background-image: url('<?php echo home_url(); ?>/wp-content/themes/ucfbands/images/section-headers/page-title-sample-bg2.jpg');">
                
                    <h1>Faculty & Staff</h1>
                
                </div><!-- /.section-title -->
                
                                    
            </div><!-- /.top-fixed -->

            
            <!--// PAGE CONTENT //-->
            <div id="page-content">
                
                <br><br>

                <!--// FACULTY TITLE //-->
                <h1 class="page-title">Faculty <a class="btn btn-md btn-ucf-gray pull-right" href="#ucfbands-staff"><i class="fa fa-sort-down"></i> &nbsp;To Staff</a></h1>    


				<div class="row">


				<?php // SHOW FACULTY FIRST // ?>
                <?php if (have_posts()) : while (have_posts()) : the_post();

					
					// Post Variable
                    global $post;
                			
                
					// FACULTY CHECK //
					if( get_post_meta( $post->ID, '_ucfbands_staff_is_faculty', true ) == 'is_faculty' ) {
					 
				?>					
                    
                    <!--// STAFF MEMEBER //-->
                    <div class="col-lg-6">
                        
                        <div class="block block-staff">
							
                            <?php
							
								//-------------------//
								// SHOW FACULTY INFO //
								//-------------------//	
								
								// Display Portrait / Featured Image
								the_post_thumbnail('medium');
								
								
								// Get staff info
								$staff_position = get_post_meta( $post->ID, '_ucfbands_staff_position', true );
								$staff_email	= get_post_meta( $post->ID, '_ucfbands_staff_email', true );
								$staff_phone	= get_post_meta( $post->ID, '_ucfbands_staff_phone', true );
								
								
								// Display staff name
								echo '<h3>' . get_the_title() . '</h3>';
								
								
								// Display staff position
								echo '<b><i>' . $staff_position . '</i></b>';
								
								
								// Spacer
								echo '<br><br>';
								
								
								// Email (If available)
								if( $staff_email != '' )
								{	
									// Icon
									echo '<i class="fa fa-envelope"></i> ';
									
									// Mailto link
									echo '<a href="mailto:' . $staff_email . '">' . $staff_email . '</a>';
								}
								
								
								// Phone (If available) 
								if( ($staff_phone != '') && ($staff_phone != '(407) 823-') )
								{	
									// Break, then Icon
									echo '<br><i class="fa fa-phone"></i> ';
									
									// Number
									echo $staff_phone;
								}
								
								
								// Spacer
								echo '<br><br>';
								
								
								// Staff Bio
								the_content();
																
							?>
                            
                        </div><!-- /.staff -->
                        
                    </div><!-- /.col-lg-6 (Staff) -->
					

					<?php 
					
						} // Faculty Checl
					
						endwhile; 
						
					?>		
							
							
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



                <br><br>



                <!--// STAFF TITLE //-->
                <h1 class="page-title" id="ucfbands-staff">Staff</h1>    


				<div class="row">


				<?php // SHOW FACULTY FIRST // ?>
                <?php if (have_posts()) : while (have_posts()) : the_post();

					
					// Post Variable
                    global $post;
                			
                
					// FACULTY CHECK //
					if( get_post_meta( $post->ID, '_ucfbands_staff_is_faculty', true ) == 'not_faculty' ) {
					 
				?>					
                    
                    <!--// STAFF MEMEBER //-->
                    <div class="col-lg-6">
                        
                        <div class="block block-staff">
							
                            <?php
							
								//-------------------//
								// SHOW FACULTY INFO //
								//-------------------//	
								
								// Display Portrait / Featured Image
								the_post_thumbnail('medium');
								
								
								// Get staff info
								$staff_position = get_post_meta( $post->ID, '_ucfbands_staff_position', true );
								$staff_email	= get_post_meta( $post->ID, '_ucfbands_staff_email', true );
								$staff_phone	= get_post_meta( $post->ID, '_ucfbands_staff_phone', true );
								
								
								// Display staff name
								echo '<h3>' . get_the_title() . '</h3>';
								
								
								// Display staff position
								echo '<b><i>' . $staff_position . '</i></b>';
								
								
								// Spacer
								echo '<br><br>';
								
								
								// Email (If available)
								if( $staff_email != '' )
								{	
									// Icon
									echo '<i class="fa fa-envelope"></i> ';
									
									// Mailto link
									echo '<a href="mailto:' . $staff_email . '">' . $staff_email . '</a>';
								}
								
								
								// Phone (If available) 
								if( ($staff_phone != '') && ($staff_phone != '(407) 823-') )
								{	
									// Break, then Icon
									echo '<br><i class="fa fa-phone"></i> ';
									
									// Number
									echo $staff_phone;
								}
								
								
								// Spacer
								echo '<br><br>';
								
								
								// Staff Bio
								the_content();
																
							?>
                            
                        </div><!-- /.staff -->
                        
                    </div><!-- /.col-lg-6 (Staff) -->
					

					<?php 
					
						} // Faculty Checl
					
						endwhile; 
						
					?>		
							
							
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