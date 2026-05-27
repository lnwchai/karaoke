<?php
/**
 * Template Name: Upload Slip
 */

$user = wp_get_current_user();
$logid = isset($_GET['logid']) ? sanitize_text_field($_GET['logid']) : 'none';
$pid = $logid;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page booking-page'); ?>>
    <div class="h-site">
        <?php echo do_shortcode('[language-switcher]'); ?>
        <a href="/"><h2>RandB Karaoke</h2></a>
    </div>
    <div class="logo-site">
        <a href="/">
            <img src="https://booking.karaoke.co.th/wp-content/uploads/r-and-b.png" alt="logo-v2">
        </a>
    </div>
    <div class="booking-bg">
        <div class="uploadslip-form">
            <form>
                <h3>Upload Your Slip</h3>
                <input type="file" name="slip" class="slip-file">
                <input type="hidden" name="logid" class="log-id" value="<?php echo $pid; ?>">
                <button type="submit">Update Slip</button>
            </form>
        </div>
    </div>
 
    <?php wp_footer(); ?>
</body>

</html>