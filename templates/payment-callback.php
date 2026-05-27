<?php
/**
 * Template Name: Payment Callback
 */

$pid = isset($_GET['logid']) ? $_GET['logid'] : '';
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'card';
$transition_id = get_post_meta( $pid, 'transition_id', true );

$branch = get_term_by('slug', get_field('branch', $pid), 'branchs');
$secret_key = get_field('secret_key', $branch);
$mid = get_field('mid', $branch);
?>
<?php get_header(); ?>
<main id="main" class="site-main page-main">
    <div class="payment-action">
        <div class="icon">
            <img src="https://booking.karaoke.co.th/wp-content/uploads/2023/06/loading-circle.gif" alt="" srcset="">
        </div>
        <div class="info">
            <h3><?php echo __('Processing, please wait', 'karaoke'); ?></h3>
        </div>
    </div>
</main>
<?php
if( $type == 'qr' ){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://kpaymentgateway-services.kasikornbank.com/qr/v2/order/'.$transition_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'x-api-key: ' . $secret_key
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $data = json_decode($response); 
    if( $data->status == 'success' ){
        update_field( 'status', 'paid', $pid );
        $time = current_time('mysql');
        wp_update_post(
            array (
                'ID'            => $pid, // ID of the post to update
                'post_date'     => $time,
                'post_date_gmt' => get_gmt_from_date( $time ),
                'post_status'   => 'publish'
            )
        );
        ?>
        <script>
            window.location = '/booking/success/?logid='+<?php echo $pid; ?>;
        </script>
    <?php
    }else{
        ?>
        <script>
            window.location = '/booking/payment/?logid='+<?php echo $pid; ?>;
        </script>
        <?php
    }
}else{
    if( isset($_POST['objectId']) && isset($_POST['status']) ){
        $curlrg_id = $_POST['objectId'];
        $status = $_POST['status'];
        if( $status ){
            $secretkey = 'skey_prod_7348EjH4YLIH5xqYQsMIfM9hcJ3vWhZo8u2O';
            $curl = curl_init();                        
            
            curl_setopt( $curl, CURLOPT_URL, 'https://kpaymentgateway-services.kasikornbank.com/card/v2/charge/'.$curlrg_id );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $curl, CURLOPT_MAXREDIRS, 10 );
            curl_setopt( $curl, CURLOPT_TIMEOUT, 30 );
            curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt( $curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );
            curl_setopt( $curl, CURLOPT_HTTPHEADER, array(                                                                                       
                'Cache-Control:no-cache',
                'x-api-key: '.$secretkey
            ));   

            $data = curl_exec($curl);
            $response = json_decode($data);

            curl_close ($curl);

            if( $status == 'true' ){
                if( $response->status == 'success' && $response->transaction_state == 'Authorized' ){
                    update_field( 'status', 'paid', $response->ref_1 );
                    $time = current_time('mysql');
                    wp_update_post(
                        array (
                            'ID'            => $response->ref_1, // ID of the post to update
                            'post_date'     => $time,
                            'post_date_gmt' => get_gmt_from_date( $time ),
                            'post_status'   => 'publish'
                        )
                    );
                    ?>
                    <script>
                        window.location = '/booking/success/?logid='+<?php echo $response->ref_1; ?>;
                    </script>
                    <?php
                }
            }else{
                ?>
                <script>
                    window.location = '/booking/payment/?logid='+<?php echo $response->ref_1; ?>;
                </script>
                <?php
            }
        }
    }else{
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://kpaymentgateway-services.kasikornbank.com/card/v2/charge/'.$transition_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'x-api-key: ' . $secret_key
            ),
        ));
    
        $response = curl_exec($curl);
        $data = json_decode($response); 
        curl_close($curl);

        if( $data->status == 'success' ){
            update_field( 'status', 'paid', $pid );
            $time = current_time('mysql');
            wp_update_post(
                array (
                    'ID'            => $pid, // ID of the post to update
                    'post_date'     => $time,
                    'post_date_gmt' => get_gmt_from_date( $time ),
                    'post_status'   => 'publish'
                )
            );
            ?>
            <script>
                window.location = '/booking/success/?logid='+<?php echo $pid; ?>;
            </script>
        <?php
        }else{
            ?>
            <script>
                window.location = '/booking/payment/?logid='+<?php echo $pid; ?>;
            </script>
            <?php
        }
    }
}
?>
<?php get_footer(); ?>