					<?php if ( is_active_sidebar( 'home_slider' ) ) : ?>
                
                        <?php dynamic_sidebar( 'home_slider' ); ?>
                
                    <?php else : ?>
                
                        
                        <div class="alert alert-message">
                        
                            <p><?php // _e("Please activate some Widgets","wpbootstrap"); ?></p>
                        
                        </div>
                
                    <?php endif; ?>
