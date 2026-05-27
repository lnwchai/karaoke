<?php 
/**
 * Template Name: BRANCH
 */
$branch = 'sathorn';
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('landing-page'); ?>>
    <div class="booking-bg">
        <main id="main" class="site-main booking-main">
            
            <div class="brand-head">
                <h2><?php echo $branch; ?> </h2>
                <p> <?php echo date("j F Y"); ?></p>
            </div>
            <div class="page-content">
                <div class="sec-result">
                    
                
            

                   
                  

                </div>



                </div>
            </div>  
        </main>
    </div>
    <?php wp_footer(); ?>
</body>
</html>