<?php
/**
 * Template Name: Success
 */
$pid = sanitize_text_field( $_GET['logid'] );
if( !isset( $_GET['logid'] ) && get_field('status', $pid) != 'paid' ){ wp_redirect( '/booking/payment?logid='.$pid ); }
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
            <?php echo do_shortcode('[language-switcher]'); ?>
            <div class="brand-head">
                <a href="/">
                    <img src="https://booking.karaoke.co.th/wp-content/uploads/2023/05/rb_logo.svg" alt="logo-v2">
                </a>
            </div>
            <div class="paymert-data">
                
            <?php
                    
                    if( get_field('status') == 'paid' ){
                        echo '<div class="success-massage">';
                        echo __('ชำระเงินเสร็จสิ้น', 'karaoke');
                        echo '</div>';
                    
                    }else{
                        
                    }
                    
                ?>



              
                <div class="price">
                    <div class="sub-total">
                        <p><?php echo __('ค่ามัดจำห้อง', 'karaoke'); ?></p>
                        <p><b><?php echo number_format(get_field('deposit', get_field('room', $pid))) ?></b></p>
                        <p><?php echo __('บาท', 'karaoke'); ?></p>
                    </div>
                    <div class="date">
                        <p><?php echo __('เวลาทำรายการ', 'karaoke'); ?></p>
                        <p><b><?php echo get_the_date( 'd.m.Y H:i', $pid )?></b></p>
                    </div>
                </div>
                <div class="logs">
                    <p><?php echo __('Reservation No.', 'karaoke'); ?> <b><?php echo get_the_date( 'Ydm', $pid ).$pid; ?></b></p>
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
                        <p>
                            <?php echo get_the_title(get_field('room', $pid)); ?><br>
                            <?php 
                                $price = custom_display_price( get_field('room', $pid),  $date_book );
                                echo number_format_i18n( $price ).' ';
                                echo __('Baht/Night', 'karaoke');
                            ?> 
                        </p>
                    </div>
                    <div class="sub">
                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.5 0.874512C7.59961 0.874512 5.25 3.22559 5.25 6.12451C5.25 9.87598 10.5 15.75 10.5 15.75C10.5 15.75 15.75 9.87598 15.75 6.12451C15.75 3.22559 13.4004 0.874512 10.5 0.874512ZM10.5 4.25098C11.5356 4.25098 12.375 5.08887 12.375 6.12451C12.375 7.16016 11.5356 7.99951 10.5 7.99951C9.46436 7.99951 8.625 7.16162 8.625 6.12451C8.625 5.08887 9.46436 4.25098 10.5 4.25098ZM4.20117 13.125L1.75049 19.2495H19.2495L16.7988 13.125H14.7114C14.2866 13.7651 13.8604 14.3569 13.4678 14.8755H15.6167L16.6655 17.5005H4.33447L5.3833 14.8755H7.53223C7.13965 14.3569 6.71338 13.7651 6.28857 13.125H4.20117Z" fill="black"/>
                        </svg>
                        <p><?php echo ucfirst(get_field('branch', $pid)); ?></p>
                    </div>
                </div>
            </div>
            <?php /*if( get_field('status', $pid) == 'paid' ): ?>
                <?php $menu_items = json_decode(get_field('menu_items', $pid), true); ?>
                <?php if($menu_items == ''): ?>
                    <a href="#" class="btn-prim select-foodmenu">เลือกเมนูอาหาร</a>
                <?php endif; ?>
                <div class="success-food-form" style="<?php echo $menu_items == '' ? 'display: none;':''; ?>">
                    <form class="update-food" action='/wp-admin/admin-ajax.php' method="post">
                        <div class="data-area">
                            <?php
                            $count = 0; 
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
                                        <div class="input-group">
                                            <div class="minus">-</div>
                                            <input type="number" name="food_num_<?php echo $count; ?>"value="<?php echo $menu_items[get_the_ID()] == null ? 0 : $menu_items[get_the_ID()]; ?>" class="food-num">
                                            <div class="plus">+</div>
                                        </div>
                                        <div class="info">
                                            <h4> <?php echo get_the_title(); ?></h4>
                                        </div>
                                        <input type="hidden" name="food_<?php echo $count; ?>" value="<?php echo get_the_ID(); ?>">
                                    </div>
                                    <?php
                                }
                            }
                            wp_reset_postdata();
                            ?>
                        </div>
                        <input type="hidden" name="food_count" value="<?php echo $count; ?>">
                        <input type="hidden" name="log_id" value="<?php echo $pid; ?>">
                        <input type="hidden" name="action" value="update_food_to_logs_new">
                        <input type="submit" class="btn-prim" value="บันทึกรายการ">
                    </form>
                </div> 
            <?php endif; */ ?>
            <?php if( get_field('slip', $pid) ): ?>
                <?php echo wp_get_attachment_image( get_field('slip', $pid), 'full'); ?>
            <?php else: ?>
            <div class="btn-payments">
                <a href="https://booking.karaoke.co.th/upload-slip/?logid=<?php echo $pid ?>"><?php echo __('Upload Slip', 'karaoke'); ?></a>
            </div>
            <?php endif; ?>
            <br>
            <div class="line-contact" style="width: 180px; margin: 0 auto;">
                <h3 style="font-size: 16px"><?php echo __('Add Line เพื่อสั่งอาหาร', 'karaoke'); ?></h3>
                <img src="https://booking.karaoke.co.th/wp-content/uploads/2023/06/line.jpg" alt="" srcset="">
            </div>
            <a href="/" style="display: block; color: red; text-align: center; margin-top: 20px;"><?php echo __('กลับไปหน้าแรก', 'karaoke'); ?></a>
        
            <div class="location-map">
                <?php
                $branch = get_term_by('slug', get_field('branch', $pid), 'branchs');
                echo get_field('map', $branch); 
                ?>
            </div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>

</html>