<?php
$date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
if( $date == '' ){
    wp_safe_redirect( '/search-room' );
}
?>
<?php get_header(); ?>
<main id="main" class="site-main single-main">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            $img = get_the_post_thumbnail($post->ID);
            $title = get_the_title();

            echo '<div class="single-content">';
            echo '<div class="pic">'.$img.'</div>';
            echo '<div class="content">';
            echo '<h1>'.$title.'</h1>';
            the_content();
            $people_arr = array(
                '20-25',
                '25-35', 
                '35-40', 
                '40-50', 
                '50-70',
                "35-100",
                '35-100' 
            );
            $people = get_field('rang');
            if(in_array($people, $people_arr)):
            ?>
            <a href="tel: 02-675-4224" class="contact-us">โทรจองห้อง 02-675-4224</a>
            <?php
            else:
                $check_booking = check_room_has_booking($date, get_the_ID());
                $format_date =  date_create( $date );
                if( $check_booking <= 0 ){
                    ?>
                    <form action="/booking" method="get">
                        <input type="text" value="<?php echo date_format($format_date, 'd/m/Y'); ?>" readonly>
                        <input type="hidden" name="date" value="<?php echo $date; ?>">
                        <?php
                        $terms = get_the_terms( get_the_ID(), 'branchs' );
                        foreach ( $terms as $term ) {
                            ?>
                            <input type="hidden" name="branch" value="<?php echo $term->slug; ?>">
                            <?php
                        };
                        ?>
                        <input type="hidden" name="room_id" value="<?php the_ID(); ?>">
                        <button type="submit">Booking</button>
                    </form>
                    <?php
                }else{
                    ?>
                    <a href="/search-room" class="btn-back">กลับหน้าค้นหา</a>
                    <?php
                }
            endif;
            echo '</div>';
            edit_post_link('EDIT', '', '', null, 'btn-edit');
        }
    }
    ?>

</main>
<?php get_footer(); ?>
