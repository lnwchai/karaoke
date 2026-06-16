<?php 
/**
 * Template Name: Asoke TV
 */
$branch = 'asoke';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page'); ?>>
    <div class="booking-bg">
        <main id="main" class="site-main booking-main">
            
            <div class="brand-head-tv">
                <h2><?php echo $branch; ?> </h2>
                <p> <?php echo date("j F Y"); ?></p>
            </div>
            <div class="page-content">
                <div class="sec-result">
                    <?php

                        $today = date( 'Y-m-d' );
                        $args = array(
                        'post_type' => 'booking_logs',
                        'post_status' => 'publish',
                        'posts_per_page' => 10,
                        'meta_key' => 'room_number',
                        'orderby' => 'meta_value_num',
                        'order' => 'ASC',
                        'facetwp' => true,
                        'meta_query' => array(
                            'relation' => 'AND',
                                array(
                                    'key' => 'date',
                                    'value' => $today,
                                    'compare' => '=',
                                    'type' => 'DATE'
                                ),
                                array(
                                    'key' => 'branch',
                                    'value' => $branch
                                )
                            ) 
                        );
                
                    $the_query = new WP_Query( $args );

                    echo '<div class="s-data">';

                        echo '<div class="s-head">';

                            echo '<div> <h3>No.</h3>';
                            echo '</div>';

                            echo '<div> <h3>Room</h3>';
                            echo '</div>';

                            

                            echo '<div> <h3>Customer</h3>';
                            echo '</div>';

                        echo '</div>';

                        echo '<div class="block-list">';
                        echo '<div class="s-list tv-list">';

                            while ( $the_query->have_posts() ) {
                                $the_query->the_post();
                                $room = get_field('room');

                                echo '<div class="sub-list">';

                                echo '<div class="room"> <p>K'.get_field('room_number').'</p>';
                                echo '</div>';


                                echo '<div class="room"> <p>'.get_the_title($room).'</p>';
                                echo '</div>';
                                

                                echo '<div class="name"> <p>คุณ '.get_field('fullname').'</p>';
                                echo '</div>';

                                echo '</div>';

    
                            }

                            wp_reset_postdata();

                        echo '</div>';


                    

                    echo '</div>';

                    ?>
        
        <?php echo do_shortcode( '[facetwp facet="page_number"]'); ?>
                    
                </div>
            </div>
            <div class="s-vdo">
                <?php echo do_shortcode('[smartslider3 slider="3"]'); ?>
            </div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>
</html>