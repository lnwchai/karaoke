<?php

/**
 * Template Name: Right Sidebar
 */

get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();
        ?>
<div class="site-rightbar">
    <main id="main" class="site-main">
        <?php echo plant_page_title(); ?>
        <div class="page-content">
            <?php the_content(); ?>
            <?php edit_post_link('EDIT', '', '', null, 'btn-edit'); ?>
            <?php
            
            $args = array(
                'post_type'  => 'booking_logs',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'transition_id',
                        'value' => '',
                        'compare' => '!='
                    )
                )
            );
        
            $the_query = new WP_Query( $args );
            date_default_timezone_set('Asia/Bangkok');
            while ( $the_query->have_posts() ) { $the_query->the_post();
                $transition_id = get_post_meta( get_the_ID(), 'transition_id', true );
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://kpaymentgateway-services.kasikornbank.com/qr/v2/order/'.$transition_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'x-api-key: skey_prod_7348EjH4YLIH5xqYQsMIfM9hcJ3vWhZo8u2O'
                    ),
                ));
            
                $response = curl_exec($curl);
            
                curl_close($curl);
                $data = json_decode($response); 
                var_dump( $data );
                echo '<br>'. $transition_id;
                echo '<br>';
                if( $data != null && $data->status == 'success' ){
                    //update_field( 'status', 'paid', get_the_ID() );
                    $time = current_time('mysql');
                }else{
                }
            }
            wp_reset_postdata();
            ?>
        </div>
    </main>
    <div class="site-sidebar widget-area">
        <?php dynamic_sidebar('rightbar'); ?>
    </div>
</div>
<?php
    endwhile;
endif;
?>
<?php get_footer(); ?>