<?php 
/**
 * Template Name: Dashboard
 */
if( !(is_user_logged_in()) ){
    wp_redirect( '/' );
}

$user = wp_get_current_user();
$branch = '';
if( $user->roles[0] == 'reception' ){
    $branch = get_field('user_branch', 'user_'.$user->ID);
    if(!isset($_GET['_branch'])){
       wp_redirect('/dashboard/?_branch='.$branch);
    }
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page booking-page dashboard-page'); ?>>
    <div class="booking-bg">
        <main id="main" class="site-main booking-main">
            <?php echo do_shortcode('[language-switcher]'); ?>
            <div class="user-data">
                <h3><?php echo $user->display_name; ?></h3>
                <a href="<?php echo wp_logout_url('/'); ?>" class="logout">ออกจากระบบ</a>
            </div>
            <div class="brand-head">
                <a href="/">
                    <img src="https://booking.karaoke.co.th/wp-content/uploads/2023/05/rb_logo.svg" alt="logo-v2">
                </a>
            </div>
            <div class="page-content">
                <div class="sec-filter s-grid -d3">
                    <?php echo do_shortcode( '[facetwp facet="search_name"]'); ?>
                    <?php echo do_shortcode( '[facetwp facet="date"]'); ?>
                    <?php echo do_shortcode( '[facetwp facet="branch"]'); ?>
                </div>
                <div class="sec-result">
                    <?php
                    $args = array(
                        'post_type'  => 'booking_logs',
                        'posts_per_page' => 10,
                        'facetwp' => true
                    );

                    if( $branch != '' ){
                        $args['meta_query'] = array(
                            array(
                                'key' => 'branch',
                                'value' => $branch
                            )
                        );
                    }
                
                    $the_query = new WP_Query( $args );
                    echo '<div class="facetwp-template">';
                    echo '<table>';
                        echo '<thead>';
                            echo '<th class="id">ID</th>';
                            echo '<th>วันที่</th>';
                            echo '<th>ชื่อ</th>';
                            echo '<th>ชื่อห้อง</th>';
                            echo '<th>สาขา</th>';
                            echo '<th>เวลา</th>';
                            echo '<th></th>';
                            echo '<th></th>';
                        echo '</thead>';
                        echo '<tbody>';
                        while ( $the_query->have_posts() ) {
                            $the_query->the_post();
                            $room = get_field('room');
                            echo '<tr>';
                                echo '<td class="id">#'.get_the_ID().'</td>';
                                echo '<td class="date">'.get_field('date').'</td>';
                                echo '<td class="name">'.get_field('fullname').'</td>';
                                echo '<td class="room">'.get_the_title($room).'</td>';
                                echo '<td class="branch">'.ucfirst(get_field('branch')).'</td>';
                                echo '<td class="time">'.get_field('time').'</td>';
                                if( get_field('status') == 'paid' ){
                                    echo '<td class="payment -paid">ชำระเงินแล้ว</td>';
                                }else{
                                    echo '<td class="payment -wait">รอชำระ</td>';
                                }
                                echo '<td><a href="/booking/payment/?logid='.get_the_ID().'">View</a></td>';
                            echo '</tr>';
                        }
                        wp_reset_postdata();
                        echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                    ?>
                    <?php echo do_shortcode( '[facetwp facet="page_number"]'); ?>
                </div>
            </div>  
        </main>
    </div>
    <?php wp_footer(); ?>
</body>
</html>