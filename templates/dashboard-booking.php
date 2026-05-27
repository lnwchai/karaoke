<?php 
/**
 * Template Name: Dashboard Booking
 */
if( !is_user_logged_in() ){
    wp_redirect( '/' );
}
get_header(); 
$user = wp_get_current_user();
date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d');
$branch = 'aree';
if( $user->roles[0] == 'reception' ){
    $branch = get_field('user_branch', 'user_'.$user->ID);
}
?>

<main id="main" class="site-main booking-page dashboard-page">
    <div class="page-content booking-main">
        <div class="sec-header s-grid -m2">
            <div class="add-booking">
            </div>
            <div class="user-info">
                <h3><?php echo $user->display_name; ?></h3>
                <a href="<?php echo wp_logout_url('/wp-admin'); ?>">
                    <span>ออกจากระบบ</span>
                </a>
            </div>
        </div>
        <form action="/wp-admin/admin-ajax.php" class="admin-booking" method="post">
            <h2>จองห้อง</h2>
            <div class="sec-input select-date">
                <h3>เลือกวันที่</h3>
                <input type="date" name="date" class="date" value="<?php echo $date; ?>">
            </div>
            <div class="sec-input select-location">
                <h3>เลือกสาขา</h3>
                <select name="branch" class="branch">
                    <?php
                    if( $branch != 'aree' ){
                        echo '<option value="'.$branch.'"  selected>'.$branch.'</option>';
                    }else{
                        $args = array(
                            'taxonomy' => 'branchs',
                            'order' => 'ASC',
                            'hide_empty' => false,
                        );
                        $term_query = new WP_Term_Query( $args );
                        foreach ( $term_query->terms as $term ) :
                            ?>
                            <option value="<?php echo $term->slug; ?>" <?php echo $branch == $term->slug ? 'selected' : ''; ?>><?php echo $term->name; ?></option>
                            <?php
                        endforeach;
                    }
                    ?>
                </select>
            </div>
            <div class="sec-input select-room">
                <?php
                $args = array(
                    'post_type'  => 'room',
                    'posts_per_page' => -1,
                );

                if( $branch != '' ){
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'branchs',
                            'field'    => 'slug',
                            'terms'    => $branch,
                        )
                    );
                }
            
                $the_query = new WP_Query( $args );
                echo '<h3>Please select room</h3>';
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
                            echo '<p>('.get_field('rang').'ท่าน)</p>';
                        echo '</div>';
                    }
                }
                wp_reset_postdata();
                echo '</div>';
                ?>
            </div>
            <div class="sec-input set-address">
                <h3>ข้อมูลผู้จอง</h3>
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
            <input type="hidden" name="action" value="save_booking_admin_log">
            <button type="submit">Confirm Order</button>
        </form>
        <div class="admin-result">
        </div>
    </div>  
</main>

<?php get_footer(); ?>