<?php
/**
 * Template Name: Admin Booking
 */
if( !is_user_logged_in() ){ wp_redirect( '/'); }

$user = wp_get_current_user();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page booking-page admin-booking'); ?>>
    <div class="booking-bg">
    <div class="logo-site-admin">
    <img src="https://booking.karaoke.co.th/wp-content/uploads/r-and-b.png" alt="logo-v2">
    </div>
        <main id="main" class="site-main booking-main">
            <?php echo do_shortcode('[language-switcher]'); ?>
            <?php if(is_user_logged_in()): ?>
                <div class="user-data">
                    <h3><?php echo $user->display_name; ?></h3>
                    <a href="<?php echo wp_logout_url('/'); ?>" class="logout">ออกจากระบบ</a>
                </div>
            <?php endif; ?>

            <div class="status-info s-grid -m3">
                <div class="detail -blank">
                    <div class="color"></div>
                    <h4><?php echo __('ว่าง', 'karaoke'); ?></h4>
                </div>
                <div class="detail -book">
                    <div class="color"></div>
                    <h4><?php echo __('จอง', 'karaoke'); ?></h4>
                </div>
                <div class="detail -payment">
                    <div class="color"></div>
                    <h4><?php echo __('รอชำระเงิน', 'karaoke'); ?></h4>
                </div>
            </div>
            <div class="date-room">
                <div class="location">
                    <h3><?php echo __('เลือกสาขา', 'karaoke'); ?></h3>
                    <select class="branch">
                        <?php if( in_array( 'administrator', $user->roles ) || in_array( 'manager', $user->roles ) || in_array( 'reception', $user->roles ) || in_array( 'operator', $user->roles ) ): ?>
                            <option value="" select><?php echo __('ทั้งหมด', 'karaoke'); ?></option>
                            <?php
                            $args = array(
                                'taxonomy' => 'branchs',
                                'order' => 'ASC',
                                'hide_empty' => false,
                            );
                            $term_query = new WP_Term_Query( $args );
                            foreach ( $term_query->terms as $term ) : ?>
                                <option value="<?php echo $term->slug ?>"><?php echo $term->name?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="<?php echo $user->user_branch ;?>"><?php echo $user->user_branch ;?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="date">
                    <h3><?php echo __('เลือกวันที่', 'karaoke'); ?></h3>
                    <input type="date" class="date-admin" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="room">
                    <?php
                    $date = date('Y-m-d');
                    $args = array(
                        'post_type'  => 'room',
                        'posts_per_page' => -1,
                        'meta_key' => 'room_size',
                        'orderby' => 'meta_value_num',
                        'order' => 'ASC'
                    );
                    
                    if( !in_array( 'administrator', $user->roles ) && !in_array( 'manager', $user->roles ) && !in_array( 'operator', $user->roles ) ): 
                        $args['tax_query'][] = array(
                            'taxonomy' => 'branchs',
                            'field'    => 'slug',
                            'terms'    => $user->user_branch,
                        );
                    endif; 

                    $the_query = new WP_Query( $args );
                    echo '<div class="room-rows s-grid -d4 -m2">';
                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $booking_logs = get_booking_log_data($date, get_the_ID());
                        echo '<div class="radio-group '.$booking_logs['status'].'">';
                            echo '<input type="radio" class="room-id" name="room_id" value="'.get_the_ID().'">';
                            echo '<h3><span>'.get_field('room_code').'</span><span>'.get_the_title().'</span></h3>';
                        echo '</div>';
                    }
                    wp_reset_postdata();
                    echo '</div>';                        
                    ?>
                </div>
            </div>
            <div class="result-data" style="display: none;">
                <div class="booking-detail">
                    <div class="sec-address">
                        <div class="input-addess">
                            <h3><?php echo __('เลือกเวลา', 'karaoke'); ?></h3>
                            <div class="radio-row s-grid -m4 -d4">
                                <div class="time-chouce">
                                    <input type="radio" name="time_choice" value="18.00" checked>
                                    <label for="">18.00</label>
                                </div>  
                                <div class="time-chouce">
                                    <input type="radio" name="time_choice" value="18.30">
                                    <label for="">18.30</label>
                                </div>  
                                <div class="time-chouce">
                                    <input type="radio" name="time_choice" value="19.00">
                                    <label for="">19.00</label>
                                </div>  
                                <div class="time-chouce">
                                    <input type="radio" name="time_choice" value="19.30">
                                    <label for="">19.30</label>
                                </div>    
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('ชื่อ', 'karaoke'); ?></label>
                                <input type="text" class="fullname" value="">
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('อีเมล', 'karaoke'); ?></label>
                                <input type="email" class="email" value="">
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('เบอร์โทร', 'karaoke'); ?></label>
                                <input type="text" class="phone">
                            </div>
                        </div>
                    </div>
                    <div class="btn-group s-grid -m2">
                        <a href="#" class="addmin-add-walkin"><?php echo __('walkin', 'karaoke'); ?></a>
                        <a href="#" class="addmin-add-booking"><?php echo __('บันทึกข้อมูล', 'karaoke'); ?></a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="wait-loading">
        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
            <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
            <path opacity="0.8" fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                C22.32,8.481,24.301,9.057,26.013,10.047z">
                <animateTransform attributeType="xml"
                    attributeName="transform"
                    type="rotate"
                    from="0 20 20"
                    to="360 20 20"
                    dur="0.8s"
                    repeatCount="indefinite"/>
            </path>
        </svg>
    </div>

    <?php wp_footer(); ?>
</body>

</html>