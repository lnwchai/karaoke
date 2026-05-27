<?php
/**
 * Template Name: Check Booking
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
                <form action="/wp-admin/admin-ajax.php" method="post">
                    <h3><?php echo __('Please enter your phone number', 'karaoke'); ?></h3>
                    <input type="number" name="phone" placeholder="Number Phone">
                    <input type="hidden" name="action" value="search_my_booking">
                    <button type="submit" class="btn-checked"><?php echo __('Search', 'karaoke'); ?></button>
                </form>
            </div>
            <div class="booking-result" style="display: none;"></div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>

</html>