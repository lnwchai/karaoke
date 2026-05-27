<?php
/**
 * Template Name: Bank Transfer
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page booking-page'); ?>>
    <div class="booking-bg">
    <div class="h-site">
        <h2>RandB Karaoke</h2>
    </div>
    <div class="logo-site">
        <a href="/">
                <img src="https://booking.karaoke.co.th/wp-content/uploads/r-and-b.png" alt="logo-v2">
            </a>
    </div>
        <main id="main" class="site-main booking-main">
            <?php if(is_user_logged_in()): ?>
                <?php $user = wp_get_current_user(); ?>
                <div class="user-data">
                    <h3><?php echo $user->display_name; ?></h3>
                    <a href="<?php echo wp_logout_url('/'); ?>" class="logout">ออกจากระบบ</a>
                </div>
            <?php endif; ?>
            <div class="search-booking">
             <?php echo do_shortcode('[gravityform id="2" title="true"]'); ?>
            </div>
            <div class="booking-result" style="display: none;"></div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>

</html>