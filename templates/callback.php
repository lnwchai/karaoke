<?php
/**
 * Template Name: Callback
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
        <main id="main" class="site-main booking-main">
            <div class="brand-head">
                <a href="/">
                    <img src="http://karaoke.cmdev.dev/wp-content/uploads/2023/04/logo-v2.png" alt="logo-v2">
                </a>
            </div>
            <div>
                <?php
                if( isset( $_GET['kpm'] ) && $_GET['kpm'] == 'success' ){
                    $pid = sanitize_text_field( $_GET['id'] );
                    
                } ?>
            </div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>

</html>