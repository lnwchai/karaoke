<?php 
/**
 * Template Name: Search Room
 */

get_header(); ?>

<?php
$date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
$branch = isset($_GET['branch']) ? sanitize_text_field($_GET['branch']) : '';
$people = isset($_GET['people']) ? sanitize_text_field($_GET['people']) : '';
if( $date == '' ){
    $date = date("Y-m-d");
}
?>

<header class="room-header">
    <div class="header-content">
        <h1><?php echo get_the_title(); ?></h1>
    </div>
</header>

<main id="main" class="site-main -wide faculty-main-page">
    <div class="page-content">
        <div class="sec-filter">
            <h2>ค้นหาห้อง</h2>
            <form action="/search-room" method="get">
                <input type="date" name="date" value="<?php echo $date; ?>">
                <select name="branch">
                <?php
                $args = array(
                    'taxonomy' => 'branchs',
                    'order' => 'ASC',
                    'hide_empty' => false,
                );
                $term_query = new WP_Term_Query( $args );
                echo '<option value="">เลือกสาขา</option>';
                foreach ( $term_query->terms as $term ) :
                    ?>
                    <option value="<?php echo $term->slug; ?>" <?php echo $branch == $term->slug ? 'selected' : ''; ?>><?php echo $term->name; ?></option>
                    <?php
                endforeach;
                ?>
                </select>
                <select name="people">
                    <option value="">จำนวนคน</option>
                    <option value="1-5" <?php echo $people == '1-5' ? 'selected' : ''; ?>>1-5</option>
                    <option value="6-8" <?php echo $people == '6-8' ? 'selected' : ''; ?>>6-8</option>
                    <option value="9-12" <?php echo $people == '9-12' ? 'selected' : ''; ?>>9-12</option>
                    <option value="12-15" <?php echo $people == '12-15' ? 'selected' : ''; ?>>12-15</option>
                    <option value="15-20" <?php echo $people == '15-20' ? 'selected' : ''; ?>>15-20</option>
                    <option value="20-25" <?php echo $people == '20-25' ? 'selected' : ''; ?>>20-25</option>
                    <option value="25-35" <?php echo $people == '25-35' ? 'selected' : ''; ?>>25-35</option>
                    <option value="35-40" <?php echo $people == '35-40' ? 'selected' : ''; ?>>35-40</option>
                    <option value="40-50" <?php echo $people == '40-50' ? 'selected' : ''; ?>>40-50</option>
                    <option value="50-70" <?php echo $people == '50-70' ? 'selected' : ''; ?>>50-70</option>
                    <option value="35-100" <?php echo $people == '35-100' ? 'selected' : ''; ?>>35-100</option>
                    <option value="มากกว่า 70" <?php echo $people == 'มากกว่า 70' ? 'selected' : ''; ?>>มากกว่า 70</option>
                </select>
                <button type="submit">ค้นหา</button>
            </form>
        </div>
        <?php
        $args = array(
            'post_type'  => 'room',
            'posts_per_page' => -1
            
        );
        if( $people != '' ){
            $args['meta_query'] = array(
                array(
                    'key' => 'rang',
                    'value' => $people,
                    'compare' => 'LIKE'
                )
            );
        }
        if( $branch != '' ){
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'branchs',
                    'field'    => 'slug',
                    'terms'    => $branch,
                )
            );
        }
        ?>
        <div class="sec-result facetwp-template s-grid -d3">
            <?php
            $the_query = new WP_Query( $args );
            if( $the_query->have_posts() ){
                while( $the_query->have_posts() ) {
                    $the_query->the_post();
                    $check_booking = check_room_has_booking($date, get_the_ID());
                    if( $check_booking <= 0 ){
                        echo '<div class="room-card">';
                            if( $date != '' ){
                                echo '<a href="'.get_the_permalink().'/?date='.$date.'">';
                            }else{
                                echo '<a href="'.get_the_permalink().'">';
                            }
                                echo '<div class="pic">';
                                    the_post_thumbnail();
                                echo '</div>';           
                                echo '<div class="info">';
                                    echo '<h3>'.get_the_title().' ('.get_field('rang').'ท่าน)</h3>';
                                    echo '<div class="cat">';
                                        $terms = get_the_terms( get_the_ID(), 'branchs' );
                                        foreach ( $terms as $term ) {
                                            echo $term->name;
                                        }
                                    echo '</div>';
                                echo '</div>';        
                            echo '</a>';
                        echo '</div>';
                    }
                }
            }else{
                echo '<p>ไม่พบห้องที่คุณต้องการ</p>';
            }
            wp_reset_postdata();
            ?>
        </div>
    </div>  
</main>

<?php get_footer(); ?>