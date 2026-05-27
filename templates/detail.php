<?php
/**
 * Template Name: Detail
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page booking-page'); ?>>
    <div class="booking-bg">
    <div class="h-site">
        <?php echo do_shortcode('[language-switcher]'); ?>
        <a href="/"><h2>RandB Karaoke</h2></a>
    </div>
    <div class="logo-site">
        <a href="/">
                <img src="https://booking.karaoke.co.th/wp-content/uploads/r-and-b.png" alt="logo-v2">
            </a>
    </div>
        <main id="main" class="site-main booking-main">
            <?php
            $logid = isset($_GET['logid']) ? sanitize_text_field($_GET['logid']) : 'none';
            $pid = $logid;
            $room = get_field('room', $pid);
            
            $date_book = get_field('date', $pid);
            $format_date =  date_create( $date_book );
            ?>
            <div class="booking-detail">
                <div class="head">
                    <h3>Your Order #<?php echo $pid; ?></h3>
                    <?php if( get_field('status', $pid) == 'paid' ): ?>
                        <div class="status-complate">ชำระเงินแล้ว</div>
                    <?php else: ?>
                        <div class="status-wait">รอชำระเงิน</div>
                    <?php endif; ?>
                </div>
                <div class="info">
                    <p>Date : <?php if($date_book != 'none'){ echo date_format($format_date, 'd/m/Y'); } ?></p>
                    <p>Room : <?php echo get_the_title($room); ?> (<?php echo get_field('rang', $room)?>ท่าน)</p>
                    <p>Branch : <?php echo get_field('branch', $pid); ?></p>
                    <p>Name : <?php echo get_field('fullname', $pid); ?></p>
                </div>
                <?php if( get_field('status', $pid) != 'paid' ): ?>
                    <a href="/booking/payment/?logid=<?php echo $pid; ?>" class="payment-btn">ชำระเงิน</a>
                <?php endif; ?>
                <hr>
                <div class="food-rows">
                    <?php
                    $food_count = 0;
                    if( have_rows('menu_items', $pid) ):
                        while( have_rows('menu_items', $pid) ) : the_row();
                            $food_count++;
                        endwhile;
                    endif;
                    ?>
                    <h3>รายการเมนูอาหาร</h3>
                    <div class="food-logs">
                        <?php if($food_count <= 0): ?>
                            <p class="red">ยังไม่ได้เลือกรายการ</p>
                        <?php else: ?>
                            <?php
                             if( have_rows('menu_items', $pid) ):
                                while( have_rows('menu_items', $pid) ) : the_row();
                                    $food_id = get_sub_field('food');
                                    ?>
                                    <div class="food-item">
                                        <?php $price = get_field('price', $food_id); ?>
                                        <div class="pic">
                                            <?php echo get_the_post_thumbnail( $food_id, 'small' ) ?>
                                        </div>
                                        <div class="info">
                                            <h4> <?php echo get_the_title($food_id); ?></h4>
                                            <p><?php echo number_format($price); ?></p>
                                        </div>
                                        <div class="count">
                                            <?php the_sub_field('count'); ?>
                                        </div>
                                    </div>
                                    <?php
                                endwhile;
                            endif;    
                            ?>
                        <?php endif; ?>
                        <?php 
                        date_default_timezone_set('Asia/Bangkok');
                        $book_date = strtotime(get_field('date', $logid));
                        $now = strtotime(date("Y-m-d"));
                        $hour = date('H');
                        if( $book_date > $now || $book_date == $now && $hour <= 17 ):?>
                            <a href="#" class="upade-booking-foods">อัพเดทรายการอารหาร</a>
                        <?php endif; ?>
                    </div>
                    <div class="food-form" style="display: none;">
                        <div class="food-filter">
                            <form class="s-grid -d2" action='/wp-admin/admin-ajax.php' method="post">
                                <input type="hidden" name="action" value="get_food_booking_form">
                                <input type="hidden" name="logid" value="<?php echo $pid; ?>">
                                <input type="text" name="keyword" class="" placeholder="ค้นหารายการอาหาร">
                                <select name="food_cat" class="">
                                    <option value="">เลือกหมวดอาหาร</option>
                                    <?php
                                    $args = array(
                                        'taxonomy' => 'food_cat',
                                        'order' => 'ASC',
                                        'hide_empty' => false,
                                    );
                                    $term_query = new WP_Term_Query( $args );
                                    foreach ( $term_query->terms as $term ) :
                                        echo '<option value="'.$term->slug.'">'.$term->name.'</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </form>
                        </div>
                        <div class="form-menu">
                            <form class="update-food" action='/wp-admin/admin-ajax.php' method="post">
                                <?php $count = 0; ?>
                                <?php
                                $args = array(
                                    'post_type'  => 'food',
                                    'posts_per_page' => -1
                                );
                                $the_query = new WP_Query( $args );
                                if( $the_query->have_posts() ){
                                    while( $the_query->have_posts() ) {
                                        $the_query->the_post(); $count++;
                                        ?>
                                        <div class="food-item">
                                            <?php $price = get_field('price'); ?>
                                            <div class="input-box">
                                                <input type="checkbox" name="food-<?php echo $count; ?>">
                                                <div class="checked">
                                                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M24.3332 5.104C13.7128 5.104 5.104 13.7128 5.104 24.3332C5.104 34.9536 13.7128 43.5623 24.3332 43.5623C34.9536 43.5623 43.5623 34.9536 43.5623 24.3332C43.5623 13.7128 34.9536 5.104 24.3332 5.104ZM22.1144 31.729L13.979 23.5936L15.4582 21.3748L22.1144 25.8123L32.4353 16.9373L34.6873 19.1561L22.1144 31.729Z" fill="white"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="pic">
                                                <?php the_post_thumbnail( 'small' ) ?>
                                            </div>
                                            <div class="info">
                                                <h4> <?php echo get_the_title(); ?></h4>
                                                <p><?php echo number_format($price); ?></p>
                                            </div>
                                            <div class="count">
                                                <select name="foodnum-<?php echo $count; ?>">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                    <option value="7">7</option>
                                                    <option value="8">8</option>
                                                    <option value="9">9</option>
                                                    <option value="10">10</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="food_<?php echo $count; ?>" value="<?php echo get_the_ID(); ?>">
                                        </div>
                                        <?php
                                    }
                                }
                                wp_reset_postdata();
                                ?>
                                <input type="hidden" name="food_count" value="<?php echo $count; ?>">
                                <input type="hidden" name="log_id" value="<?php echo $pid; ?>">
                                <input type="hidden" name="action" value="update_food_to_logs">
                                <input type="submit" value="บันทึกรายการ">
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>

</html>