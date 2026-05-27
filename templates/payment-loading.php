<?php
/**
 * Template Name: Payment Loading
 */

$pid = isset($_GET['logid']) ? intval($_GET['logid']) : '';
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'qr';
$room = get_field('room', $pid);
$amount = get_field('deposit', $room);

$branch = get_term_by('slug', get_field('branch', $pid), 'branchs');
$secret_key = get_field('secret_key', $branch);
$public_key = get_field('public_key', $branch);
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
    <?php if( $type == 'qr' ): ?>  
        <?php
        $field = array(
            "amount" => $amount,
            "currency" => 'THB',
            "description" => get_the_title( $pid ),
            "source_type" => "qr",
            "reference_order" => "BOOKING-".$pid.date('Ymd')
        );

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://kpaymentgateway-services.kasikornbank.com/qr/v2/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode( $field ),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'x-api-key: ' .  $secret_key
            ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);    
        update_post_meta( $pid, 'transition_id', $data->id );       
        ?>
        <form method="POST" action="/payment-callback/?logid=<?php echo $pid; ?>&type=qr">
            <script type="text/javascript"
                src="https://kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js"
                data-apikey="<?php echo $public_key; ?>"
                data-amount="<?php echo $amount ?>"
                data-currency="THB"
                data-payment-methods="qr"
                data-name="URBAN BUSSINESS GROUP ( QR <?php echo strtoupper(get_field('branch', $pid)) ?> )" 
                data-order-id="<?php echo $data->id ?>">
            </script>
        </form>
    <?php else: ?>
        <form method="POST" onsubmit="return kpaymentForm( this );" >
            <script type="text/javascript" src="https://kpaymentgateway.kasikornbank.com/ui/v2/kpayment.min.js"
            data-apikey="<?php echo $public_key; ?>"
            data-amount="<?php echo $amount ?>"
            data-currency="THB"
            data-payment-methods="card"
            data-name="URBAN BUSSINESS GROUP ( <?php echo strtoupper(get_field('branch', $pid)) ?> )"
            data-mid="<?php echo $mid ?>">
            </script>
            <input type="hidden" class="payment-id" value="<?php echo $pid; ?>">
        </form>
    <?php endif; ?>
    <script>
        window.onload = setTimeout( function(){
            const payBtn = document.querySelector('.pay-button');
            if( payBtn != null ){
                payBtn.click();
            }
        }, 3000);
    </script>
</main>
<?php get_footer(); ?>