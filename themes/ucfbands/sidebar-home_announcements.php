
                <!--// THIRD WIDTH COL //-->
                <div class="col-lg-4">
                
                    <div class="block">
                            
                        <div id="" role="complementary">
                        
                            <?php if ( is_active_sidebar( 'home_announcements' ) ) : ?>
        
                                <?php dynamic_sidebar( 'home_announcements' ); ?>
        
                            <?php else : ?>
        
                                <!-- This content shows up if there are no widgets defined in the backend. -->
                                
                                <div class="alert alert-message">
                                
                                    <p><?php _e("Please activate some Widgets","wpbootstrap"); ?>.</p>
                                
                                </div>
        
                            <?php endif; ?>
        
                        </div>
                
                    </div><!-- /.block -->
                
                </div><!-- / Right Column -->         

