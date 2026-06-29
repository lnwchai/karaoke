<?php

// ENQUEUE CSS_JS
function fruit_scripts(){
    $ver = wp_get_theme()->get('Version'); 
    $url = get_stylesheet_directory_uri();
    wp_enqueue_style('f-m', $url . '/css/style.css', [], $ver);
    wp_enqueue_style('f-p-classic', $url . '/css/classic.css', [], $ver);
    wp_enqueue_style('f-p-classic-date', $url . '/css/classic.date.css', [], $ver);

    wp_enqueue_script('f-main', $url . '/js/main.js', array('jquery'), $ver, true);
    wp_enqueue_script('f-picket', $url . '/js/picker.js', array('jquery'), $ver, true);
    wp_enqueue_script('f-picket-date', $url . '/js/picker.date.js', array('jquery'), $ver, true);
}
add_action('wp_enqueue_scripts', 'fruit_scripts', 20);

function get_all_branch(){
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $people = isset($_POST['num']) ? sanitize_text_field($_POST['num']) : 'none';
    
    ?>
    <form action="" method="post">
        <h3>Select location</h3>
        <div class="input-rows s-grid -d3">
            <?php
            $args = array(
                'taxonomy' => 'branchs',
                'order' => 'ASC',
                'hide_empty' => false,
            );
            $term_query = new WP_Term_Query( $args );
            foreach ( $term_query->terms as $term ) :
                ?>
                <div class="radio-group">
                    <input type="radio" class="room-branch" name="room_branch" value="<?php echo $term->slug ?>">
                    <div class="detail">
                        <h3><?php echo $term->name; ?></h3>
                        <h4>
                        <?php 
                            $args = array(
                                'post_type'  => 'room',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'rang',
                                        'value' => $people,
                                        'compare' => 'LIKE'
                                    )
                                ),
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'branchs',
                                        'field'    => 'slug',
                                        'terms'    => $term->slug,
                                    )
                                ),
                            );
                            $count = 0;
                            $the_query = new WP_Query( $args );
                            while ( $the_query->have_posts() ) { $the_query->the_post();
                                $check_booking = check_room_has_booking($date, get_the_ID());
                                if( $check_booking <= 0 ){
                                    $count++;
                                }
                            }
                            wp_reset_postdata();
                            echo $count; 
                        ?>
                        </h4>
                        <p>Room</p>
                    </div>
                </div>
                <?php
            endforeach;
            ?>
        </div>
        <button type="submit">Next</button>
    </form>
    <?php
    exit();
}
add_action('wp_ajax_nopriv_get_get_all_branch', 'get_all_branch');
add_action('wp_ajax_get_all_branch', 'get_all_branch');


function get_rooms_by_branch(){
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $people = isset($_POST['num']) ? sanitize_text_field($_POST['num']) : 'none';
    $branch = isset($_POST['branch']) ? sanitize_text_field($_POST['branch']) : 'none';

    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => $people,
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    
    $the_query = new WP_Query( $args );
    if( $the_query->have_posts() ){
        echo '<h3>'. __('เลือกห้อง', 'karaoke').'</h3>';
        echo '<div class="room-highlight" style="display: none;"></div>';
        echo '<div class="room-rows s-grid -m2 -d3">';
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                echo '<div class="radio-group">';
                    echo '<input type="radio" class="room-id" name="room_id" value="'.get_the_ID().'">';
                    echo '<div class="pic">';
                        the_post_thumbnail();
                        echo '<div class="checked">
                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24.3332 5.104C13.7128 5.104 5.104 13.7128 5.104 24.3332C5.104 34.9536 13.7128 43.5623 24.3332 43.5623C34.9536 43.5623 43.5623 34.9536 43.5623 24.3332C43.5623 13.7128 34.9536 5.104 24.3332 5.104ZM22.1144 31.729L13.979 23.5936L15.4582 21.3748L22.1144 25.8123L32.4353 16.9373L34.6873 19.1561L22.1144 31.729Z" fill="white"/>
                            </svg>
                        </div>';
                    echo '</div>';
                    echo '<h3>'.get_the_title().'</h3>';
                    echo '<p style="color: #fff; font-size: 16px;">';
                        $price = custom_display_price( get_the_ID(), $date );
                        echo number_format_i18n( $price ).' '; 
                        echo __('Baht/Night', 'karaoke'); 
                    echo '</p>';
                    echo karaoke_get_room_promo_image( get_the_ID(), $date );
                echo '</div>';
            }
        }
        wp_reset_postdata();
        echo '</div>';
    }else{
        echo 'false';
    }

    echo '<div class="btn-group s-grid -m2">
        <a href="#" class="b-back" data-hide="select-room" data-show="get-allroom">
            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.5 29.76C23.3687 29.76 29.76 23.3687 29.76 15.5C29.76 7.63134 23.3687 1.24001 15.5 1.24001C7.63133 1.24001 1.24001 7.63134 1.24001 15.5C1.24001 23.3687 7.63134 29.76 15.5 29.76ZM15.5 28.52C8.30219 28.52 2.48001 22.6978 2.48001 15.5C2.48001 8.3022 8.30219 2.48001 15.5 2.48001C22.6978 2.48001 28.52 8.30219 28.52 15.5C28.52 22.6978 22.6978 28.52 15.5 28.52ZM17.4181 22.3394C17.4448 22.3345 17.4714 22.3273 17.4956 22.32C17.7281 22.2788 17.9146 22.1093 17.98 21.8841C18.0454 21.6564 17.9776 21.4142 17.8056 21.2544L12.0513 15.5L17.8056 9.74563C18.0527 9.4986 18.0527 9.10141 17.8056 8.85438C17.5586 8.60735 17.1614 8.60735 16.9144 8.85438L10.7144 15.0544C10.5933 15.1706 10.5255 15.3329 10.5255 15.5C10.5255 15.6671 10.5933 15.8294 10.7144 15.9456L16.9144 22.1456C17.0427 22.2837 17.2292 22.3539 17.4181 22.3394Z" fill="#D1AB77"/>
            </svg>
            Back
        </a>
        <a href="#" class="b-next" data-hide="select-room" data-show="sec-policy" data-check="room">
            Next
            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.5002 1.23999C7.63156 1.23999 1.24023 7.63132 1.24023 15.5C1.24023 23.3687 7.63156 29.76 15.5002 29.76C23.3689 29.76 29.7602 23.3687 29.7602 15.5C29.7602 7.63132 23.3689 1.23999 15.5002 1.23999ZM15.5002 2.47999C22.698 2.47999 28.5202 8.30218 28.5202 15.5C28.5202 22.6978 22.698 28.52 15.5002 28.52C8.30242 28.52 2.48023 22.6978 2.48023 15.5C2.48023 8.30218 8.30242 2.47999 15.5002 2.47999ZM13.5821 8.66062C13.5555 8.66546 13.5288 8.67273 13.5046 8.67999C13.2721 8.72116 13.0856 8.89069 13.0202 9.11593C12.9548 9.34358 13.0227 9.58577 13.1946 9.74562L18.949 15.5L13.1946 21.2544C12.9476 21.5014 12.9476 21.8986 13.1946 22.1456C13.4416 22.3926 13.8388 22.3926 14.0859 22.1456L20.2859 15.9456C20.407 15.8294 20.4748 15.6671 20.4748 15.5C20.4748 15.3329 20.407 15.1706 20.2859 15.0544L14.0859 8.85437C13.9575 8.71632 13.771 8.64608 13.5821 8.66062Z" fill="#D1AB77"/>
            </svg>
        </a>
    </div>';
    exit();
}
add_action('wp_ajax_nopriv_get_rooms_by_branch', 'get_rooms_by_branch');
add_action('wp_ajax_get_rooms_by_branch', 'get_rooms_by_branch');


function get_checkout_form(){
    $num = isset($_POST['num']) ? sanitize_text_field($_POST['num']) : 'none';
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $room = isset($_POST['room']) ? sanitize_text_field($_POST['room']) : 'none';
    $branch = isset($_POST['branch']) ? sanitize_text_field($_POST['branch']) : 'none';

    $format_date =  date_create( $date );
    ?>
    <form action="" method="post">
        <h3>Select Room Sathorn</h3>
        <div class="head">
            <p><b>Guests</b> : <?php echo get_field('rang', $room); ?> Person</p>
            <p><b>Date</b> : <?php echo date_format($format_date, 'd/m/Y'); ?></p>
            <p><b>Branch</b> : <?php echo ucfirst($branch); ?></p>
            <p><b>Room</b> : <?php echo get_the_title($room); ?> (<?php echo get_field('rang', $room)?>Person)</p>
        </div>
        <div class="condition">
            <h3>Reservation policy</h3>
            <ul>
                <li>1.Karaoke room reservation is for a nightly rate only from 6pm to 12am.</li>
                <li>2.Karaoke room price, foods and beverages are subjected to 10% service charge and 7 % Vat.</li>
                <li>3.Exceeding room capacity, there will be surcharge of 100++ Bahts per person.</li>
                <li>4.Check in after 7.30pm, there is a minimum food spending of 200++ Bahts per person.</li>
                <li>5.Reservation is completed after room deposit is made. The deposit will be deducted from the bill upon check out.</li>
                <li>6.The receipt and tax invoice can only be issued on the check in date.</li>
                <li>7.Changing your booking date, a 1 day advance notice is required. Only one rescheduling within 30 days of booking date is permitted. In case of cancellation, the room deposit is not refun </li>
            </ul>
        </div>
        <div class="name-detail">
            <input type="text" name="phone" class="phone" placeholder="Number Phone">
            <input type="text" name="fullname" class="fullname" placeholder="Fullname">
            <input type="text" name="email" class="email" placeholder="E-mail">
            <select name="time" class="time">
                <option value="18:00">18:00</option>
                <option value="19:00">19:00</option>
                <option value="20:00">20:00</option>
                <option value="21:00">21:00</option>
                <option value="22:00">22:00</option>
                <option value="23:00">23:00</option>
            </select>
        </div>
        <div class="price">
            <h3>Deposit</h3>
            <h4><?php echo number_format(get_field('deposit', $room)); ?></h4>
        </div>
        <button type="submit">Confirm Order</button>
    </form>
    <?php
    exit();
}
add_action('wp_ajax_nopriv_get_checkout_form', 'get_checkout_form');
add_action('wp_ajax_get_checkout_form', 'get_checkout_form');

function save_booking_logs(){
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : 'none';
    $fullname = isset($_POST['fullname']) ? sanitize_text_field($_POST['fullname']) : 'none';
    $room = isset($_POST['room']) ? sanitize_text_field($_POST['room']) : 'none';
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : 'none';
    $time = isset($_POST['time']) ? sanitize_text_field($_POST['time']) : 'none';

    $check_booking = check_room_has_booking($date, $room);
    $data = [];
    
    if( $check_booking <= 0 ){
        $title = $fullname;
        $post = array(
            'post_title' => $title,
            'post_type' => 'booking_logs',
            'post_status' => 'publish'
        );

        $postid = wp_insert_post( $post );

        $terms = get_the_terms( $room, 'branchs' );
        foreach ( $terms as $term ) {
            $branch = $term->name;
        }
        
        update_field( 'fullname', $fullname, $postid );
        update_field( 'phone', $phone, $postid );
        update_field( 'room', $room, $postid );
        update_field( 'date', $date, $postid );
        update_field( 'time', $time, $postid );
        update_field( 'email', $email, $postid );
        update_field( 'status', 'wait', $postid );
        update_field( 'branch', strtolower($branch), $postid );
        update_field( 'slip', '', $postid );
        update_field( 'room_number', get_field('room_code', $room), $postid );

        $deposit = get_field('deposit', $room);
        update_field( 'deposit', $deposit, $postid );

        $content = custom_email_contents( $postid );
        $subj = get_the_title( $room ).'( '.get_field('room_code', $room).' )';
        sent_email_for_booking( $email, $content, $subj );

        $data['status'] = 'success';
        $data['data'] = $postid;
    }else{
        $data['status'] = 'error';
        $data['data'] = __('This room has already been reserved.', 'booking');
    }

    echo json_encode($data);
    exit();
}
add_action('wp_ajax_nopriv_save_booking_logs', 'save_booking_logs');
add_action('wp_ajax_save_booking_logs', 'save_booking_logs');


function search_my_booking(){
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : 'none';
    $args = array(
        'post_type'  => 'booking_logs',
        'posts_per_page' => -1,
        'post_status' => array('publish'),
        'meta_query' => array(
            array(
                'key' => 'phone',
                'value' => $phone
            )
        )
    );

    $the_query = new WP_Query( $args );
    echo '<h3>User ID : <b>'.$phone.'</b></h3>';
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $room = get_field('room');
            ?>
            <div class="paymert-data">
                <div class="logs">
                    <p>Reservation No. <b><?php echo get_the_date( 'Ydm' ).get_the_ID(); ?></b></p>
                    <div class="sub">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 3.555V2.88833C16 1.29667 14.7033 0 13.1117 0H2.88833C1.29667 0 0 1.29667 0 2.88833V3.555H16ZM0 4.88833V13.1117C0 14.7033 1.29667 16 2.88833 16H13.1117C14.7033 16 16 14.7033 16 13.1117V4.88833H0ZM4.22167 13.3333C3.60833 13.3333 3.11167 12.8367 3.11167 12.2217C3.11167 11.6083 3.60833 11.1117 4.22167 11.1117C4.83667 11.1117 5.33333 11.6083 5.33333 12.2217C5.33333 12.8367 4.83667 13.3333 4.22167 13.3333ZM4.22167 9.33333C3.60833 9.33333 3.11167 8.83667 3.11167 8.22167C3.11167 7.60833 3.60833 7.11167 4.22167 7.11167C4.83667 7.11167 5.33333 7.60833 5.33333 8.22167C5.33333 8.83667 4.83667 9.33333 4.22167 9.33333ZM8 13.3333C7.38667 13.3333 6.88833 12.8367 6.88833 12.2217C6.88833 11.6083 7.38667 11.1117 8 11.1117C8.61333 11.1117 9.11167 11.6083 9.11167 12.2217C9.11167 12.8367 8.61333 13.3333 8 13.3333ZM8 9.33333C7.38667 9.33333 6.88833 8.83667 6.88833 8.22167C6.88833 7.60833 7.38667 7.11167 8 7.11167C8.61333 7.11167 9.11167 7.60833 9.11167 8.22167C9.11167 8.83667 8.61333 9.33333 8 9.33333ZM11.7783 9.33333C11.1633 9.33333 10.6667 8.83667 10.6667 8.22167C10.6667 7.60833 11.1633 7.11167 11.7783 7.11167C12.3917 7.11167 12.8883 7.60833 12.8883 8.22167C12.8883 8.83667 12.3917 9.33333 11.7783 9.33333Z" fill="black"/>
                        </svg>
                        <p>
                            <?php
                            $date_book = get_field('date');
                            $format_date =  date_create( $date_book );
                            if( $format_date ){
                                echo date_format($format_date, 'j F Y');
                            }
                            ?>
                        </p>
                    </div>
                    <div class="sub">
                        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.11167 0V1.83C3.11833 2.27333 0 5.66667 0 9.77667C0 14.1883 3.59 17.7767 8 17.7767C12.41 17.7767 16 14.1883 16 9.77667C16 5.66667 12.8817 2.27333 8.88833 1.83V0H7.11167ZM7.11167 4.44333H8.88833V9.41L11.6883 12.2083L10.43 13.465L7.11167 10.145V4.44333Z" fill="black"/>
                        </svg>
                        <p><?php echo get_field('time'); ?></p>
                    </div>
                    <div class="sub">
                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.33333 9.29V0C7.17167 0.0333333 7.01167 0.0766664 6.85667 0.133333L1.605 2.05167C0.645 2.40167 0 3.325 0 4.34667V12.4033C0 12.545 0.015 12.6833 0.0383335 12.82L7.33333 9.29ZM3.11167 5.99167C3.11167 5.76 3.255 5.555 3.47167 5.47667L5.33833 4.79667C5.55167 4.71833 5.77833 4.87667 5.77833 5.10333V7.97C5.77833 8.13333 5.67667 8.28167 5.525 8.34167L3.55833 9.11667C3.34333 9.2 3.11167 9.04333 3.11167 8.81167V5.99167ZM8.66667 9.29L10.2217 10.0433V4.86167C10.2217 4.64167 10.4417 4.49167 10.6467 4.56833L12.585 5.3C12.7683 5.37 12.8883 5.545 12.8883 5.74V11.3333L15.9617 12.82C15.985 12.6833 16 12.545 16 12.4033V4.34667C16 3.325 15.355 2.40333 14.395 2.05167L9.14333 0.133333C8.98833 0.0766664 8.82833 0.0333333 8.66667 0V9.29ZM8 10.45L0.62 14.02C0.883333 14.3183 1.21667 14.5567 1.605 14.6983L6.85667 16.6167C7.225 16.7517 7.61167 16.82 8 16.82C8.38833 16.82 8.775 16.7517 9.14333 16.6167L14.395 14.6983C14.7833 14.5567 15.1167 14.3183 15.38 14.02L8 10.45Z" fill="black"/>
                        </svg>
                        <p><?php echo get_the_title(get_field('room')); ?> <?php echo get_field('branch'); ?></p>
                    </div>
                    <div class="sub">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.3333 0C10.1383 0 9.055 0.223334 8.20833 0.646667C7.36167 1.06833 6.66667 1.755 6.66667 2.66667V5.33333C6.66667 5.42 6.695 5.50333 6.70833 5.58333C6.08833 5.42167 5.40167 5.33333 4.66667 5.33333C3.47167 5.33333 2.38833 5.55667 1.54167 5.98C0.695 6.40167 0 7.08833 0 8V13.3333C0 14.245 0.695 14.9317 1.54167 15.355C2.38833 15.7767 3.47167 16 4.66667 16C5.86167 16 6.945 15.7767 7.79167 15.355C8.63833 14.9317 9.33333 14.245 9.33333 13.3333V13.0633C9.945 13.2233 10.615 13.3333 11.3333 13.3333C12.5283 13.3333 13.6117 13.11 14.4583 12.6883C15.305 12.265 16 11.5783 16 10.6667V2.66667C16 1.755 15.305 1.06833 14.4583 0.646667C13.6117 0.223334 12.5283 0 11.3333 0ZM11.3333 1.33333C12.3483 1.33333 13.2533 1.55167 13.8533 1.855C14.455 2.15667 14.6667 2.47333 14.6667 2.66667C14.6667 2.86 14.455 3.17667 13.8533 3.47833C13.2533 3.78167 12.3483 4 11.3333 4C10.3183 4 9.41333 3.78167 8.81333 3.47833C8.21167 3.17667 8 2.86 8 2.66667C8 2.47333 8.21167 2.15667 8.81333 1.855C9.41333 1.55167 10.3183 1.33333 11.3333 1.33333ZM8 4.56333C8.07 4.60167 8.135 4.65167 8.20833 4.68667C9.055 5.11 10.1383 5.33333 11.3333 5.33333C12.5283 5.33333 13.6117 5.11 14.4583 4.68667C14.5317 4.65167 14.5967 4.60167 14.6667 4.56333V5.33333C14.6667 5.52667 14.455 5.84333 13.8533 6.145C13.2533 6.44833 12.3483 6.66667 11.3333 6.66667C10.3183 6.66667 9.41333 6.44833 8.81333 6.145C8.21167 5.84333 8 5.52667 8 5.33333V4.56333ZM4.66667 6.66667C5.68167 6.66667 6.58667 6.885 7.18667 7.18667C7.78833 7.49 8 7.80667 8 8C8 8.19333 7.78833 8.51 7.18667 8.81333C6.58667 9.115 5.68167 9.33333 4.66667 9.33333C3.65167 9.33333 2.74667 9.115 2.14667 8.81333C1.545 8.51 1.33333 8.19333 1.33333 8C1.33333 7.80667 1.545 7.49 2.14667 7.18667C2.74667 6.885 3.65167 6.66667 4.66667 6.66667ZM14.6667 7.23V8C14.6667 8.19333 14.455 8.51 13.8533 8.81333C13.2533 9.115 12.3483 9.33333 11.3333 9.33333C10.575 9.33333 9.89 9.20833 9.33333 9.02167V8C9.33333 7.91333 9.305 7.83 9.29167 7.75C9.91167 7.91167 10.5983 8 11.3333 8C12.5283 8 13.6117 7.77667 14.4583 7.35333C14.5317 7.31833 14.5967 7.26833 14.6667 7.23ZM1.33333 9.89667C1.40333 9.935 1.46833 9.985 1.54167 10.0217C2.38833 10.4433 3.47167 10.6667 4.66667 10.6667C5.86167 10.6667 6.945 10.4433 7.79167 10.0217C7.865 9.985 7.93 9.935 8 9.89667V10.6667C8 10.86 7.78833 11.1767 7.18667 11.48C6.58667 11.7817 5.68167 12 4.66667 12C3.65167 12 2.74667 11.7817 2.14667 11.48C1.545 11.1767 1.33333 10.86 1.33333 10.6667V9.89667ZM14.6667 9.89667V10.6667C14.6667 10.86 14.455 11.1767 13.8533 11.48C13.2533 11.7817 12.3483 12 11.3333 12C10.575 12 9.89 11.8933 9.33333 11.7083V10.4167C9.945 10.5783 10.6117 10.6667 11.3333 10.6667C12.5283 10.6667 13.6117 10.4433 14.4583 10.0217C14.5317 9.985 14.5967 9.935 14.6667 9.89667ZM1.33333 12.5633C1.40333 12.6017 1.46833 12.6517 1.54167 12.6883C2.38833 13.11 3.47167 13.3333 4.66667 13.3333C5.86167 13.3333 6.945 13.11 7.79167 12.6883C7.865 12.6517 7.93 12.6017 8 12.5633V13.3333C8 13.5267 7.78833 13.8433 7.18667 14.1467C6.58667 14.4483 5.68167 14.6667 4.66667 14.6667C3.65167 14.6667 2.74667 14.4483 2.14667 14.1467C1.545 13.8433 1.33333 13.5267 1.33333 13.3333V12.5633Z" fill="black"/>
                        </svg>
                        <?php if( get_field('status') == 'paid' ): ?>
                            <p class="success">Payment successful</p>
                        <?php else: ?>
                            <p class="wait">Payment Pending</p>
                        <?php endif; ?>
                    </div>
                    <?php

                        if( get_field('status') == 'wait' ){
                            echo '<a class="view-detail" href="/booking/success?logid='.get_the_ID().'">View booking</a>';
                            echo '<a class="view-detail" href="/upload-slip?logid='.get_the_ID().'">Upload Slip</a>';
                        }else{
                            echo '<a class="view-detail" href="/booking/payment?logid='.get_the_ID().'">View booking</a>';
                        }
                        
                    ?>
               
                </div>
            </div>
            <?php
        }
        echo '<a href="/" style="color: #d1ab77; text-align: center;">กลับหน้าแรก</a>';
    }else{
        echo '<div class="no-result">';
            echo '<p>"No Booking"</p>';
            echo '<a href="/check-booking">Back to Search</a>';   
        echo '</div>';
    }
    wp_reset_postdata();
    exit();
}
add_action('wp_ajax_nopriv_search_my_booking', 'search_my_booking');
add_action('wp_ajax_search_my_booking', 'search_my_booking');

function check_room_has_booking($date, $room){
    $args = array(
        'post_type'  => 'booking_logs',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'date',
                'value' => $date
            ),
            array(
                'key' => 'room',
                'value' => $room
            )
        )
    );

    $the_query = new WP_Query( $args );
    $count = $the_query->found_posts;
    
    return $count;
    exit();
}

function redirect_admin( $redirect_to, $request, $user ){
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'administrator', $user->roles ) ) {
            $redirect_to = home_url() . '/wp-admin';
        }else if( in_array( 'operator', $user->roles ) || in_array( 'reception', $user->roles ) || in_array( 'manager', $user->roles ) ){
            $redirect_to = '/admin-booking';   
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'redirect_admin', 10, 3 );

function get_room_admin(){
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $branch = isset($_POST['branch']) ? sanitize_text_field($_POST['branch']) : 'none';

    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );

    $the_query = new WP_Query( $args );
    echo '<h3>เลือกห้อง</h3>';
    echo '<div class="room-rows s-grid -d3">';
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $check_booking = check_room_has_booking($date, get_the_ID());
        if( $check_booking <= 0 ){
            echo '<div class="radio-group">';
                echo '<input type="radio" class="room-id" name="room_id" value="'.get_the_ID().'">';
                echo '<div class="pic">';
                    the_post_thumbnail();
                    echo '<div class="checked">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24.3332 5.104C13.7128 5.104 5.104 13.7128 5.104 24.3332C5.104 34.9536 13.7128 43.5623 24.3332 43.5623C34.9536 43.5623 43.5623 34.9536 43.5623 24.3332C43.5623 13.7128 34.9536 5.104 24.3332 5.104ZM22.1144 31.729L13.979 23.5936L15.4582 21.3748L22.1144 25.8123L32.4353 16.9373L34.6873 19.1561L22.1144 31.729Z" fill="white"/>
                        </svg>
                    </div>';
                echo '</div>';
                echo '<h3>'.get_the_title().'</h3>';
                echo '<p>('.get_field('rang').'Person)</p>';
            echo '</div>';
        }
    }
    wp_reset_postdata();
    echo '</div>';
    exit();
}
add_action('wp_ajax_nopriv_get_room_admin', 'get_room_admin');
add_action('wp_ajax_get_room_admin', 'get_room_admin');

function redirect_someuser_to_dashboard_page() {
    if ( is_user_logged_in() ) {
        $user = wp_get_current_user();
        if( in_array( 'operator', $user->roles ) || in_array( 'reception', $user->roles ) || in_array( 'manager', $user->roles ) ){
            $redirect_to = '/dashboard';
            wp_redirect( $redirect_to );
            exit();   
        }
   }
}
//add_action( 'admin_init', 'redirect_someuser_to_dashboard_page' );

function change_card_request(){
    $token = $_POST['token'];
    $amount = $_POST['amount'];
    $pid = $_POST['pid'];
    $currency = $_POST['currency'];
    $type = $_POST['type'];
    
    $branch = get_term_by('slug', get_field('branch', $pid), 'branchs');
    $secret_key = get_field('secret_key', $branch);
    $mid = get_field('mid', $branch);
    $ref_order = 'Order-'.$pid. date_i18n( 'jmYHi', current_time( 'timestamp', 0 ));
    
    if( $type == 'DCC' ){
        $data = array(
            "amount" => $amount,
            "currency" => 'THB',
            "description" => $type,
            "source_type" => "card",
            "saveCard" => "true",
            "mode" => "token",
            "token" => $token,
            "dcc_data" => array(
                "dcc_currency" => $currency
            ),
            "reference_order" => $ref_order,
            "ref_1" => $pid,
            "additional_data" => array(
                "mid" => $mid
            )
        );
    }else{
        $data = array(
            "amount" => $amount,
            "currency" => $currency,
            "description" => $type,
            "source_type" => "card",
            "saveCard" => "true",
            "mode" => "token",
            "token" => $token,
            "reference_order" => $ref_order,
            "ref_1" => $pid,
            "additional_data" => array(
                "mid" => $mid
            )
        );
    }

    $data_string = json_encode( $data );
    $head = array(
        'Cache-Control:no-cache',
        'x-api-key: '.$secret_key, 
        'Content-Type: application/json'
    );
        

    $curl = curl_init();
    $live = 'https://kpaymentgateway-services.kasikornbank.com/card/v2/charge';
    $dev = 'https://dev-kpaymentgateway-services.kasikornbank.com/card/v2/charge';
                
    curl_setopt( $curl, CURLOPT_URL, $live );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_MAXREDIRS, 10 );
    curl_setopt( $curl, CURLOPT_TIMEOUT, 30 );
    curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
    curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'POST' );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_string );
    curl_setopt( $curl, CURLOPT_HTTPHEADER, $head );

    $response = curl_exec($curl);
    $data = json_decode($response);    
    
    curl_close($curl);
    update_post_meta( $pid, 'transition_id', $data->id ); 

    echo $response;
    exit();
}
add_action('wp_ajax_nopriv_ksent_request', 'change_card_request');
add_action('wp_ajax_ksent_request', 'change_card_request');

/**
 * Error massage
 */
function kpayment_error_massage( $code ){
    $massage = array(
        '01' => 'Refer to Card Issuer',
        '02' => 'Refer to Issuer is Special Conditions',
        '03' => 'Invalid Merchant ID',
        '04' => 'Pick Up Card',
        '05' => 'Do Not Honor',
        '06' => 'Error',
        '07' => 'Pick Up Card, Special Conditions',
        '08' => 'Honor with ID',
        '09' => 'Request in Progress',
        '10' => 'Partial Amount Approved',
        '11' => 'Approved VIP',
        '12' => 'Invalid Transaction',
        '13' => 'Invalid Amount',
        '14' => 'Invalid Card Number',
        '15' => 'No Sun Issuer',
        '16' => 'Approved, Update Track 3',
        '17' => 'Customer Cancellation',
        '18' => 'Customer Dispute',
        '19' => 'Re-enter Transaction',
        '20' => 'Invalid Response',
        '21' => 'No Action Taken',
        '22' => 'Suspected Malfunction',
        '23' => 'Unacceptable Transaction Fee',
        '24' => 'File Update not Supported by Receiver',
        '25' => 'Unable to Locate Record on File',
        '26' => 'Duplicate File Update Record',
        '27' => 'File Update Field Edit Error',
        '28' => 'File Update File Locked Out',
        '29' => 'File Update not Successful',
        '30' => 'Format Error',
        '31' => 'Bank not Supported by Switch',
        '32' => 'Completed Partially',
        '33' => 'Expired Card - Pick Up',
        '34' => 'Suspected Fraud - Pick Up',
        '35' => 'Contact Acquirer - Pick Up',
        '36' => 'Restricted Card - Pick Up',
        '37' => 'Call Acquirer Security - Pick Up',
        '38' => 'Allowable PIN Tries Exceeded',
        '39' => 'No Credit Account',
        '40' => 'Requested Function not Supported',
        '41' => 'Lost Card - Pick Up',
        '42' => 'No Universal Amount',
        '43' => 'Stolen Card - Pick Up',
        '44' => 'No Investment Account',
        '51' => 'Insufficient Funds',
        '52' => 'No Cheque Account',
        '53' => 'No Savings Account',
        '54' => 'Expired Card',
        '55' => 'Incorrect PIN',
        '56' => 'No Card Record',
        '57' => 'Trans. not Permitted to Cardholder',
        '58' => 'Transaction not Permitted to Terminal',
        '59' => 'Suspected Fraud',
        '60' => 'Card Acceptor Contact Acquirer',
        '61' => 'Exceeds Withdrawal Amount Limits',
        '62' => 'Restricted Card',
        '63' => 'Security Violation',
        '64' => 'Original Amount Incorrect',
        '65' => 'Exceeds Withdrawal Frequency Limit',
        '66' => 'Card Acceptor Call Acquirer Security',
        '67' => 'Hard Capture - Pick Up Card at ATM',
        '68' => 'Response Received Too Late',
        '75' => 'Allowable PIN Tries Exceeded',
        '86' => 'ATM Malfunction',
        '87' => 'No Envelope Inserted',
        '88' => 'Unable to Dispense',
        '89' => 'Administration Error',
        '90' => 'Cut-off in Progress',
        '91' => 'Issuer or Switch is Inoperative',
        '92' => 'Financial Institution not Found',
        '93' => 'Trans Cannot be Completed',
        '94' => 'Duplicate Transmission',
        '95' => 'Reconcile Error',
        '96' => 'System Malfunction',
        '97' => 'Reconciliation Totals Reset',
        '98' => 'MAC Error',
        '99' => 'Reserved',
        'N7' => 'Invalid CVV2',
        'NC' => 'Invalid CVV2 with Approval Code',
        '500' => 'Timeout',
        '600' => 'Reverse Transaction'
    );

    return $massage[ $code ];
}

// Booking logs
function get_booking_log_data($date, $room){
    $args = array(
        'post_type'  => 'booking_logs',
        'posts_per_page' => 1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'date',
                'value' => $date
            ),
            array(
                'key' => 'room',
                'value' => $room
            )
        )
    );

    $booking_log = get_posts( $args );
    $return = ['status' => ''];
    foreach ( $booking_log as $post ) {
        if( get_field('status', $post->ID) == 'paid' || get_field('status', $post->ID) == 'walkin' ){
            $return['status'] = 'success';
        }else{
            $return['status'] = 'wait-payment';
        }
        $return['id'] = $post->ID;
    }
    return $return;
}

// Admin create booking log
function save_admin_booking_logs(){
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : 'none';
    $fullname = isset($_POST['fullname']) ? sanitize_text_field($_POST['fullname']) : 'none';
    $room = isset($_POST['room']) ? sanitize_text_field($_POST['room']) : 'none';
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : 'none';
    $time = isset($_POST['time']) ? sanitize_text_field($_POST['time']) : 'none';

    $title = $fullname;
    $post = array(
        'post_title' => $title,
        'post_type' => 'booking_logs',
        'post_status' => 'publish'
    );

    $postid = wp_insert_post( $post );

    $terms = get_the_terms( $room, 'branchs' );
    foreach ( $terms as $term ) {
        $branch = $term->name;
    }
    
    update_field( 'fullname', $fullname, $postid );
    update_field( 'phone', $phone, $postid );
    update_field( 'room', $room, $postid );
    update_field( 'date', $date, $postid );
    update_field( 'time', $time, $postid );
    update_field( 'email', $email, $postid );
    update_field( 'status', 'wait', $postid );
    update_field( 'branch', strtolower($branch), $postid );
    update_field( 'slip', '', $postid );
    update_field( 'room_number', get_field('room_code', $room), $postid );

    $deposit = get_field('deposit', $room); 
    update_field( 'deposit', $deposit, $postid );

    echo $postid;
    $content = custom_email_contents( $postid );
    $subj = get_the_title( $room ).'( '.get_field('room_code', $room).' )';
    sent_email_for_booking( $email, $content, $subj );
    exit();
}
add_action('wp_ajax_nopriv_save_admin_booking_logs', 'save_admin_booking_logs');
add_action('wp_ajax_save_admin_booking_logs', 'save_admin_booking_logs');

// Admin create booking log
function save_admin_booking_walkin_logs(){
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : 'none';
    $fullname = isset($_POST['fullname']) ? sanitize_text_field($_POST['fullname']) : 'none';
    $room = isset($_POST['room']) ? sanitize_text_field($_POST['room']) : 'none';
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : 'none';
    $time = isset($_POST['time']) ? sanitize_text_field($_POST['time']) : 'none';

    $title = $fullname;
    $post = array(
        'post_title' => $title,
        'post_type' => 'booking_logs',
        'post_status' => 'publish'
    );

    $postid = wp_insert_post( $post );

    $terms = get_the_terms( $room, 'branchs' );
    foreach ( $terms as $term ) {
        $branch = $term->name;
    }
    
    update_field( 'fullname', $fullname, $postid );
    update_field( 'phone', $phone, $postid );
    update_field( 'room', $room, $postid );
    update_field( 'date', $date, $postid );
    update_field( 'time', $time, $postid );
    update_field( 'email', $email, $postid );
    update_field( 'status', 'walkin', $postid );
    update_field( 'branch', strtolower($branch), $postid );
    update_field( 'slip', '', $postid );
    update_field( 'room_number', get_field('room_code', $room), $postid );

    $deposit = get_field('deposit', $room); 
    update_field( 'deposit', $deposit, $postid );

    $content = custom_email_contents( $postid );
    $subj = get_the_title( $room ).'( '.get_field('room_code', $room).' )';
    sent_email_for_booking( $email, $content, $subj );
    exit();
}
add_action('wp_ajax_nopriv_save_admin_booking_walkin_logs', 'save_admin_booking_walkin_logs');
add_action('wp_ajax_save_admin_booking_walkin_logs', 'save_admin_booking_walkin_logs');

// Admin rooms
function get_room_admin_result(){
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : date('Y-m-d');
    $branch = isset($_POST['branch']) ? sanitize_text_field($_POST['branch']) : '';

    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_key' => 'room_size',
        'orderby' => 'meta_value_num',
        'order' => 'ASC'
    );
    
    if( $branch != '' ){
        $args['tax_query'][] = array(
            'taxonomy' => 'branchs',
            'field'    => 'slug',
            'terms'    => $branch,
        );
    }

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

    exit();
}
add_action('wp_ajax_nopriv_get_room_admin_result', 'get_room_admin_result');
add_action('wp_ajax_get_room_admin_result', 'get_room_admin_result');

// Admin rooms
function get_room_bookind_log_admin(){
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : date('Y-m-d');
    $room_id = isset($_POST['roomid']) ? sanitize_text_field($_POST['roomid']) : '';

    $booking_logs = get_booking_log_data($date, $room_id);
    if( $booking_logs['status'] == '' ){
    ?>
    <div class="sec-address">
        <div class="input-addess">
            <h3>Choose Time</h3>
            <h4 style="text-align: center; margin-bottom: 24px;"><?php echo get_the_title( $room_id ); ?></h4>
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
                <label for="">Name</label>
                <input type="text" class="fullname" value="">
            </div>
            <div class="input-row">
                <label for="">Email</label>
                <input type="email" class="email" value="">
            </div>
            <div class="input-row">
                <label for="">Phone</label>
                <input type="text" class="phone">
            </div>
        </div>
    </div>
    <div class="btn-group s-grid -m2">
        <a href="#" class="addmin-add-walkin"><?php echo __('walkin', 'karaoke'); ?></a>
        <a href="#" class="addmin-add-booking"><?php echo __('บันทึกข้อมูล', 'karaoke'); ?></a>
    </div>
    <?php
    } else {
    $pid = $booking_logs['id'];
    ?>
    <div class="paymert-data">
        <?php if( $booking_logs['status'] == 'success' ): ?>
            <div class="success-massage">Payment successful</div>
        <?php else: ?>
            <div class="success-massage -wait">Payment pending</div>
        <?php endif; ?>
        <div class="price">
            <div class="sub-total">
                <p>Booking Deposit</p>
                <p><b><?php echo number_format(get_field('deposit', $room_id)) ?></b></p>
                <p>Baht</p>
            </div>
            <div class="date">
                <p>Transaction Time</p>
                <p><b><?php echo get_the_date( 'd.m.Y H:i', $pid )?></b></p>
            </div>
        </div>
        <div class="logs">
            <div class="sub">
                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.76875 0C4.7375 0 3.07625 1.29375 3.07625 3.6925C3.07625 5.2975 3.82375 6.96125 4.9225 7.885V8.73125C4.9225 9.10125 4.6725 9.41875 4.365 9.48125C1.96625 10.2188 0 11.8075 0 12.7312V13.8462C0 15.015 3.01375 16 6.76875 16C10.5237 16 13.5375 15.015 13.5375 13.8462V12.7312C13.5375 11.87 11.6337 10.2188 9.1725 9.48125C8.865 9.41875 8.615 9.03875 8.615 8.73125V7.885C9.71375 6.96125 10.4612 5.2975 10.4612 3.6925C10.4612 1.29375 8.8 0 6.76875 0Z" fill="black"/>
                </svg>
                <p>
                    <?php
                    echo get_field('fullname', $pid).'<br>';
                    echo get_field('email', $pid).'<br>';
                    echo get_field('phone', $pid);
                    ?>
                </p>
            </div>
            <div class="sub">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 3.555V2.88833C16 1.29667 14.7033 0 13.1117 0H2.88833C1.29667 0 0 1.29667 0 2.88833V3.555H16ZM0 4.88833V13.1117C0 14.7033 1.29667 16 2.88833 16H13.1117C14.7033 16 16 14.7033 16 13.1117V4.88833H0ZM4.22167 13.3333C3.60833 13.3333 3.11167 12.8367 3.11167 12.2217C3.11167 11.6083 3.60833 11.1117 4.22167 11.1117C4.83667 11.1117 5.33333 11.6083 5.33333 12.2217C5.33333 12.8367 4.83667 13.3333 4.22167 13.3333ZM4.22167 9.33333C3.60833 9.33333 3.11167 8.83667 3.11167 8.22167C3.11167 7.60833 3.60833 7.11167 4.22167 7.11167C4.83667 7.11167 5.33333 7.60833 5.33333 8.22167C5.33333 8.83667 4.83667 9.33333 4.22167 9.33333ZM8 13.3333C7.38667 13.3333 6.88833 12.8367 6.88833 12.2217C6.88833 11.6083 7.38667 11.1117 8 11.1117C8.61333 11.1117 9.11167 11.6083 9.11167 12.2217C9.11167 12.8367 8.61333 13.3333 8 13.3333ZM8 9.33333C7.38667 9.33333 6.88833 8.83667 6.88833 8.22167C6.88833 7.60833 7.38667 7.11167 8 7.11167C8.61333 7.11167 9.11167 7.60833 9.11167 8.22167C9.11167 8.83667 8.61333 9.33333 8 9.33333ZM11.7783 9.33333C11.1633 9.33333 10.6667 8.83667 10.6667 8.22167C10.6667 7.60833 11.1633 7.11167 11.7783 7.11167C12.3917 7.11167 12.8883 7.60833 12.8883 8.22167C12.8883 8.83667 12.3917 9.33333 11.7783 9.33333Z" fill="black"/>
                </svg>
                <p>
                    <?php
                    $date_book = get_field('date', $pid);
                    $format_date =  date_create( $date_book );
                    echo date_format($format_date, 'j F Y');
                    ?>
                </p>
            </div>
            <div class="sub">
                <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.11167 0V1.83C3.11833 2.27333 0 5.66667 0 9.77667C0 14.1883 3.59 17.7767 8 17.7767C12.41 17.7767 16 14.1883 16 9.77667C16 5.66667 12.8817 2.27333 8.88833 1.83V0H7.11167ZM7.11167 4.44333H8.88833V9.41L11.6883 12.2083L10.43 13.465L7.11167 10.145V4.44333Z" fill="black"/>
                </svg>
                <p><?php echo get_field('time', $pid); ?></p>
            </div>
            <div class="sub">
                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.33333 9.29V0C7.17167 0.0333333 7.01167 0.0766664 6.85667 0.133333L1.605 2.05167C0.645 2.40167 0 3.325 0 4.34667V12.4033C0 12.545 0.015 12.6833 0.0383335 12.82L7.33333 9.29ZM3.11167 5.99167C3.11167 5.76 3.255 5.555 3.47167 5.47667L5.33833 4.79667C5.55167 4.71833 5.77833 4.87667 5.77833 5.10333V7.97C5.77833 8.13333 5.67667 8.28167 5.525 8.34167L3.55833 9.11667C3.34333 9.2 3.11167 9.04333 3.11167 8.81167V5.99167ZM8.66667 9.29L10.2217 10.0433V4.86167C10.2217 4.64167 10.4417 4.49167 10.6467 4.56833L12.585 5.3C12.7683 5.37 12.8883 5.545 12.8883 5.74V11.3333L15.9617 12.82C15.985 12.6833 16 12.545 16 12.4033V4.34667C16 3.325 15.355 2.40333 14.395 2.05167L9.14333 0.133333C8.98833 0.0766664 8.82833 0.0333333 8.66667 0V9.29ZM8 10.45L0.62 14.02C0.883333 14.3183 1.21667 14.5567 1.605 14.6983L6.85667 16.6167C7.225 16.7517 7.61167 16.82 8 16.82C8.38833 16.82 8.775 16.7517 9.14333 16.6167L14.395 14.6983C14.7833 14.5567 15.1167 14.3183 15.38 14.02L8 10.45Z" fill="black"/>
                </svg>
                <p><?php echo get_the_title(get_field('room', $pid)); ?> <?php echo get_field('branch', $pid); ?></p>
            </div>
        </div>
        <?php if( get_field('slip', $pid) ): ?>
            <?php echo wp_get_attachment_image( get_field('slip', $pid), 'full'); ?>
        <?php else: ?>
        <div class="btn-payments">
            <a href="https://booking.karaoke.co.th/upload-slip/?logid=<?php echo $pid ?>"><?php echo __('Upload Slip', 'karaoke'); ?></a>
        </div>
        <?php endif; ?>
        <?php if( $booking_logs['status'] != 'success' ): ?>
            <a href="/admin-booking/admin-payment?logid=<?php echo $pid; ?>" class="admin-gopay">ชำระเงิน</a>
        <?php endif; ?>
    </div>
    <?php
    }
    
    die();
}
add_action('wp_ajax_nopriv_get_room_bookind_log_admin', 'get_room_bookind_log_admin');
add_action('wp_ajax_get_room_bookind_log_admin', 'get_room_bookind_log_admin');

// Get room highlght
function get_room_highlight(){
    $room_id = isset($_POST['room']) ? sanitize_text_field($_POST['room']) : '';
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    ?>
    <div class="pic">
        <?php echo get_the_post_thumbnail( $room_id, 'full' ); ?>
        <p class="not">* Room visuals for advertising</p>
    </div>
    <div class="sec-info">
    <?php echo karaoke_get_room_promo_image( $room_id, $date ); ?>
    <div class="info">
        <h3><?php echo get_the_title($room_id); ?></h3>
        <p style="color: #fff; font-size: 16px;">
            <?php
            $price = custom_display_price( $room_id, $date );
            echo number_format_i18n( $price ).' '; 
            ?> 
            <?php echo __('Baht/Night', 'karaoke'); ?>
        </p>
    </div>
    
    </div>
    <?php
    exit();
}
add_action('wp_ajax_nopriv_get_room_highlight', 'get_room_highlight');
add_action('wp_ajax_get_room_highlight', 'get_room_highlight');

// Sent email for booking.
function sent_email_for_booking( $email, $data, $subj ){
    $to = [$email, 'info@karaoke.co.th'];
    $subject = 'Booking Details '.$subj;
    $body = $data;
    $headers = array('Content-Type: text/html; charset=UTF-8','From: Booking Karaoke <no-reply@karaoke.co.th>');

    wp_mail( $to, $subject, $body, $headers );
}

function redirect_after_logout( $redirect_to, $request, $user ){
    $redirect_to = home_url();
    return $redirect_to;
}
add_filter( 'logout_redirect', 'redirect_after_logout', 10, 3 );

function custom_email_contents( $logid ){
    $date_book = get_field('date', $logid);
    $format_date =  date_create( $date_book );
    $content = '<table style="max-width: 630px; width: 100%; text-align: left; background: #F6F6F6;">
        <thead style="background: #1A2F36;">
            <tr>
                <th colspan="2" style="color: #fff; padding: 10px 16px;"> Reservation No. '.get_the_date( 'Ydm', $logid ).$logid.'</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px 16px; width: 30%">
                <h3  style="text-align: center;">แสกน QR เพื่อสั่งอาหาร</h3>
                <img src="https://booking.karaoke.co.th/wp-content/uploads/2023/06/line.jpg" alt="" srcset="" width="250px">
                    <a href="https://booking.karaoke.co.th/booking/payment/?logid='.$logid.'" style="display: block; 
                    margin: 10px auto 0; 
                    padding: 6px; 
                    color: #fff; 
                    background-color: #f90;
                    width: 180px;
                    border-radius: 5px;
                    text-decoration: none;
                    text-align: center;">Check payment status</a>
                </td>
                <td style="width: 70%">
                    <table>
                        <tr>
                            <td style="font-size: 14px; padding-right: 16px">Booking Deposit</td>
                            <td><b>'.number_format(get_field('deposit', get_field('room', $logid))).'</b> บาท</td>
                        </tr>
                        <tr>
                            <td style="font-size: 14px; padding-right: 16px">transaction Time</td>
                            <td>'.get_the_date('d.m.Y H:i', $logid).'</td>
                        </tr>
                        <tr>
                            <td style="font-size: 14px; padding-right: 16px">Reservation Information</td>
                            <td><b>'.get_field('fullname', $logid).'<br>'.get_field('email', $logid).'<br>'.get_field('phone', $logid).'</b></td>
                        </tr>
                        <tr>
                            <td style="font-size: 14px; padding-right: 16px">Day/Time</td>
                            <td>'.date_format($format_date, 'j F Y').' '.get_field('time', $logid).'</td>
                        </tr>
                        <tr>
                            <td style="font-size: 14px; padding-right: 16px">Room/branch</td>
                            <td>'.get_the_title(get_field('room', $logid)).' '.get_field('branch', $logid).'</td>
                        </tr>
                        <tr>
                        <td style="font-size: 14px; padding-right: 16px">Person</td>
                            <td>'.get_field('rang', get_field('room', $logid)).' คน</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 16px;">
                    <h3><b>RESERVATION POLICY</b></h3>
                    
                    <p style="font-size: 12px">1.Karaoke room reservation is for a nightly rate only from 6pm to 12am.</p>
                    <p style="font-size: 12px">2.Karaoke room price, foods and beverages are subjected to 10% service charge and 7 % Vat.</p>
                    <p style="font-size: 12px">3.Exceeding room capacity, there will be surcharge of 100++ Bahts per person.</p>
                    <p style="font-size: 12px">4.Check in after 7.30pm, there is a minimum food spending of 200++ Bahts per person.</p>
                    <p style="font-size: 12px"> 5.Reservation is completed after room deposit is made. The deposit will be deducted from the bill upon check out.</p>
                    <p style="font-size: 12px">6.The receipt and tax invoice can only be issued on the check in date.</p>
                    <p style="font-size: 12px">7.Changing your booking date, a 1 day advance notice is required. Only one rescheduling within 30 days of booking date is permitted. In case of cancellation, the room deposit is not refundable.</p>
                    
                    
                </td>
            </tr>
        </tbody>
    </table>';
    return $content;
}

function get_people_number(){
    $branch = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : 'none';
    $date = isset($_POST['date']) ? sanitize_text_field($_POST['date']) : 'none';
    ?>
    
    <?php
    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => '1-5',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    $the_query = new WP_Query( $args );
    $room_s = false;
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                $room_s = true;
            }
        }
        wp_reset_postdata();
    }
    ?>
    <div class="radio-group">
        <input type="radio" class="people-num" name="num" value="<?php echo $room_s == false ? 'not-data': '1-5'; ?>">
        <div class="detail">
            <h3>1-5</h3>
        </div>
    </div>

    <?php
    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => '6-8',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    $the_query = new WP_Query( $args );
    $room_m = false;
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                $room_m = true;
            }
        }
        wp_reset_postdata();
    }
    ?>
    <div class="radio-group">
        <input type="radio" class="people-num" name="num" value="<?php echo $room_m == false ? 'not-data': '6-8'; ?>">
        <div class="detail">
            <h3>6-8</h3>
        </div>
    </div>

    <?php
    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => '9-12',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    $the_query = new WP_Query( $args );
    $room_l = false;
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                $room_l = true;
            }
        }
        wp_reset_postdata();
    }
    ?>
    <div class="radio-group">
        <input type="radio" class="people-num" name="num" value="<?php echo $room_l == false ? 'not-data': '9-12'; ?>">
        <div class="detail">
            <h3>9-12</h3>
        </div>
    </div>

    <?php
    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => '13-15',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    $the_query = new WP_Query( $args );
    $room_xl = false;
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                $room_xl = true;
            }
        }
        wp_reset_postdata();
    }
    ?>
    <div class="radio-group">
        <input type="radio" class="people-num" name="num" value="<?php echo $room_xl == false ? 'not-data': '13-15'; ?>">
        <div class="detail">
            <h3>13-15</h3>
        </div>
    </div>

    <?php
    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => '16-20',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    $the_query = new WP_Query( $args );
    $room_xxl = false;
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                $room_xxl = true;
            }
        }
        wp_reset_postdata();
    }
    ?>
    <div class="radio-group">
        <input type="radio" class="people-num" name="num" value="<?php echo $room_xxl == false ? 'not-data': '16-20'; ?>">
        <div class="detail">
            <h3>16-20</h3>
        </div>
    </div>

    <?php
    $args = array(
        'post_type'  => 'room',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'rang',
                'value' => '21-25',
                'compare' => 'LIKE'
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'branchs',
                'field'    => 'slug',
                'terms'    => $branch,
            )
        ),
    );
    $the_query = new WP_Query( $args );
    $room_xxxl = false;
    if( $the_query->have_posts() ){
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $check_booking = check_room_has_booking($date, get_the_ID());
            if( $check_booking <= 0 ){
                $room_xxxl = true;
            }
        }
        wp_reset_postdata();
    }
    ?>
    <div class="radio-group">
        <input type="radio" class="people-num" name="num" value="<?php echo $room_xxxl == false ? 'not-data': '21-25'; ?>">
        <div class="detail">
            <h3>21-25</h3>
        </div>
    </div>
    <?php
    exit();
}
add_action('wp_ajax_nopriv_get_people_number', 'get_people_number');
add_action('wp_ajax_get_people_number', 'get_people_number');

function karaoke_get_room_promo_image( $room_id, $date ){
    $timestamp = strtotime($date);
    if( !$timestamp ){
        return '';
    }

    $field_name = in_array(date('D', $timestamp), array('Fri', 'Sat'), true) ? 'image_pro_fri_sat' : 'image_pro_sun_thu';
    $image = get_field($field_name, $room_id);

    if( empty($image) ){
        return '';
    }

    $class = 'room-promo-image';

    if( is_numeric($image) ){
        return '<div class="'.$class.'" style="'.$wrapper_style.'">'.wp_get_attachment_image($image, 'full', false, $image_attr).'</div>';
    }

    if( is_array($image) ){
        if( !empty($image['ID']) ){
            return '<div class="'.$class.'" style="'.$wrapper_style.'">'.wp_get_attachment_image($image['ID'], 'full', false, $image_attr).'</div>';
        }

        if( !empty($image['url']) ){
            $alt = !empty($image['alt']) ? $image['alt'] : '';
            return '<div class="'.$class.'" style="'.$wrapper_style.'"><img src="'.esc_url($image['url']).'" alt="'.esc_attr($alt).'" style="'.$image_attr['style'].'"></div>';
        }
    }

    if( is_string($image) ){
        return '<div class="'.$class.'" style="'.$wrapper_style.'"><img src="'.esc_url($image).'" alt="" style="'.$image_attr['style'].'"></div>';
    }

    return '';
}

function custom_display_price( $room_id, $date ){
    $day = date( 'D' , strtotime($date));
    if( $day == 'Fri' || $day == 'Sat' ){
        $rp = get_field('deposit_fri_sat', $room_id);
    }else{
        $rp = get_field('deposit_sun_thu', $room_id);
    }

    $price_exp = get_field('price_setting', $room_id);
    $data_checked = [];
    $price_array = [];
    foreach ($price_exp as $data) {
        $data_checked[] = $data['date'];
        $price_array[$data['date']] = $data; 
    }

    $date_exp = date( 'Y-m-d' , strtotime($date));
    if( in_array( $date_exp, $data_checked ) ){
        $data_exp = $price_array[$date_exp];
        if( $data_exp['min_max_switch'] == 'min' ){
            $return = intval( $rp ) - intval( $data_exp['price'] );
        }else{
            $return = intval( $rp ) + intval( $data_exp['price'] );
        }
    }else{
        $return = $rp;
    }

    return $return;
}

function date_booking() {
    $start = get_field('start_date', 'options');
    $end = get_field('end_date', 'options');

    echo '<input type="hidden" value="'.$start.'" class="s-booking-date">';
    echo '<input type="hidden" value="'.$end.'" class="e-booking-date">';
}
// add_action( 'wp_footer', 'date_booking');

// Upload slip
function custom_upload_slip(){
    $logid = isset($_POST['logid']) ? intval($_POST['logid']) : 0;

    /* Upload slip file */
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $attachment_id = media_handle_upload( 'file_slip', 0 );
   /*if ( is_wp_error( $attachment_id ) ) {
        echo $attachment_id->get_error_message();
    } else {
        update_field( 'slip', $attachment_id, $logid );
        update_field( 'status', 'paid', $logid );
        echo $logid;
    }*/
    update_field( 'slip', $attachment_id, $logid );
    update_field( 'status', 'paid', $logid );
    echo $logid;
    exit();
}
add_action('wp_ajax_nopriv_custom_upload_slip', 'custom_upload_slip');
add_action('wp_ajax_custom_upload_slip', 'custom_upload_slip');

define('PLANT_DISABLE_ACF', true);

