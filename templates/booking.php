<?php
/**
 * Template Name: Booking
 */

$user = wp_get_current_user();

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
        <main id="main" class="site-main booking-main">
                
                <?php if(is_user_logged_in()): ?>
                    <div class="user-data">
                        <h3><?php echo $user->display_name; ?></h3>
                        <a href="<?php echo wp_logout_url('/'); ?>" class="logout"><?php echo __('ออกจากระบบ', 'karaoke'); ?></a>
                    </div>
                <?php endif; ?>
              
                <form class="get-allroom" action="/wp-admin/admin-ajax.php" method="post">
                    <input type="hidden" name="action" value="get_rooms_by_branch">
                    <div class="select-date">
                        <h3 style="margin-left: -30px; margin-right: -30px;"><?php echo __('Select a date and time for the service', 'karaoke'); ?></h3>
                        <!-- <input type="date" class="ip-date" name="date" value="<?php // echo date('Y-m-d'); ?>" onkeydown="return false"> -->
                        <input type="text" placeholder="Please select a date ..." class="datepicker" name="date">
                        <br>
                        <div class="input-rows s-grid -m2 -d2">
                            <div class="radio-group">
                                <input type="radio" class="ip-time" name="p-num" value="18.00">
                                <div class="detail">
                                    <h3>18.00</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="ip-time" name="p-num" value="18.30">
                                <div class="detail">
                                    <h3>18.30</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="ip-time" name="p-num" value="19.00">
                                <div class="detail">
                                    <h3>19.00</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="ip-time" name="p-num" value="19.30">
                                <div class="detail">
                                    <h3>19.30</h3>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group s-grid -m2">
                            <div></div>
                            <a href="#" class="b-next" data-hide="select-date" data-show="select-location" data-check="date">
                                <?php echo __('Next', 'karaoke'); ?>
                                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.5002 1.23999C7.63156 1.23999 1.24023 7.63132 1.24023 15.5C1.24023 23.3687 7.63156 29.76 15.5002 29.76C23.3689 29.76 29.7602 23.3687 29.7602 15.5C29.7602 7.63132 23.3689 1.23999 15.5002 1.23999ZM15.5002 2.47999C22.698 2.47999 28.5202 8.30218 28.5202 15.5C28.5202 22.6978 22.698 28.52 15.5002 28.52C8.30242 28.52 2.48023 22.6978 2.48023 15.5C2.48023 8.30218 8.30242 2.47999 15.5002 2.47999ZM13.5821 8.66062C13.5555 8.66546 13.5288 8.67273 13.5046 8.67999C13.2721 8.72116 13.0856 8.89069 13.0202 9.11593C12.9548 9.34358 13.0227 9.58577 13.1946 9.74562L18.949 15.5L13.1946 21.2544C12.9476 21.5014 12.9476 21.8986 13.1946 22.1456C13.4416 22.3926 13.8388 22.3926 14.0859 22.1456L20.2859 15.9456C20.407 15.8294 20.4748 15.6671 20.4748 15.5C20.4748 15.3329 20.407 15.1706 20.2859 15.0544L14.0859 8.85437C13.9575 8.71632 13.771 8.64608 13.5821 8.66062Z" fill="#D1AB77"/>
                                </svg>
                            </a>
                        </div>
                        <div class="check-booking-btn">
                            <a href="/check-booking/"><?php echo __('Check Booking', 'karaoke'); ?></a>
                        </div>
                    </div>
                    <div class="select-location" style="display: none;">
                        <h3><?php echo __('RESERVE YOUR  BRANCH', 'karaoke'); ?></h3>
                        <div class="input-rows s-grid -m1 -d3">
                            <?php
                            $args = array(
                                'taxonomy' => 'branchs',
                                'order' => 'ASC',
                                'hide_empty' => false,
                            );
                            $term_query = new WP_Term_Query( $args );
                            foreach ( $term_query->terms as $term ) :
                                ?>
                                <div class="radio-group b-<?php echo $term->term_id; ?>">
                                    <input type="radio" class="room-branch" name="branch" value="<?php echo $term->slug ?>">
                                    <div class="detail">
                                        <h3>
                                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.4998 2.65997C6.35738 2.65997 3.7998 5.21755 3.7998 8.35997C3.7998 12.2995 9.04262 16.4632 9.26527 16.6383C9.33356 16.6933 9.41668 16.72 9.4998 16.72C9.58293 16.72 9.66605 16.6933 9.73434 16.6383C9.95699 16.4632 15.1998 12.2995 15.1998 8.35997C15.1998 5.21755 12.6422 2.65997 9.4998 2.65997ZM9.4998 3.03997C12.4329 3.03997 14.8198 5.42685 14.8198 8.35997C14.8198 12.1184 9.71652 16.1693 9.4998 16.34C9.28309 16.1693 4.1798 12.1184 4.1798 8.35997C4.1798 5.42685 6.56668 3.03997 9.4998 3.03997ZM9.44637 6.94982C8.63738 6.94982 7.9798 7.60443 7.9798 8.41341C7.9798 9.2224 8.63738 9.87997 9.44637 9.87997C10.2554 9.87997 10.91 9.2224 10.91 8.41341C10.91 8.27982 10.8907 8.15068 10.8565 8.02896C10.7229 8.20115 10.5181 8.30654 10.2865 8.30654C9.88426 8.30654 9.55324 7.97552 9.55324 7.57325C9.55324 7.34169 9.65863 7.13685 9.83082 7.00325C9.7091 6.96911 9.57996 6.94982 9.44637 6.94982Z" fill="white"/>
                                            </svg>
                                            <?php echo __($term->name, 'karaoke'); ?>
                                        </h3>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                            ?>
                        </div>
                        <div class="btn-group s-grid -m2">
                            <a href="#" class="b-back" data-hide="select-location" data-show="select-date">
                                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.5 29.76C23.3687 29.76 29.76 23.3687 29.76 15.5C29.76 7.63134 23.3687 1.24001 15.5 1.24001C7.63133 1.24001 1.24001 7.63134 1.24001 15.5C1.24001 23.3687 7.63134 29.76 15.5 29.76ZM15.5 28.52C8.30219 28.52 2.48001 22.6978 2.48001 15.5C2.48001 8.3022 8.30219 2.48001 15.5 2.48001C22.6978 2.48001 28.52 8.30219 28.52 15.5C28.52 22.6978 22.6978 28.52 15.5 28.52ZM17.4181 22.3394C17.4448 22.3345 17.4714 22.3273 17.4956 22.32C17.7281 22.2788 17.9146 22.1093 17.98 21.8841C18.0454 21.6564 17.9776 21.4142 17.8056 21.2544L12.0513 15.5L17.8056 9.74563C18.0527 9.4986 18.0527 9.10141 17.8056 8.85438C17.5586 8.60735 17.1614 8.60735 16.9144 8.85438L10.7144 15.0544C10.5933 15.1706 10.5255 15.3329 10.5255 15.5C10.5255 15.6671 10.5933 15.8294 10.7144 15.9456L16.9144 22.1456C17.0427 22.2837 17.2292 22.3539 17.4181 22.3394Z" fill="#D1AB77"/>
                                </svg>
                                <?php echo __('Back', 'karaoke'); ?>
                            </a>
                            <a href="#" class="b-next" data-hide="select-location" data-show="select-people" data-check="location">
                                <?php echo __('Next', 'karaoke'); ?>
                                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.5002 1.23999C7.63156 1.23999 1.24023 7.63132 1.24023 15.5C1.24023 23.3687 7.63156 29.76 15.5002 29.76C23.3689 29.76 29.7602 23.3687 29.7602 15.5C29.7602 7.63132 23.3689 1.23999 15.5002 1.23999ZM15.5002 2.47999C22.698 2.47999 28.5202 8.30218 28.5202 15.5C28.5202 22.6978 22.698 28.52 15.5002 28.52C8.30242 28.52 2.48023 22.6978 2.48023 15.5C2.48023 8.30218 8.30242 2.47999 15.5002 2.47999ZM13.5821 8.66062C13.5555 8.66546 13.5288 8.67273 13.5046 8.67999C13.2721 8.72116 13.0856 8.89069 13.0202 9.11593C12.9548 9.34358 13.0227 9.58577 13.1946 9.74562L18.949 15.5L13.1946 21.2544C12.9476 21.5014 12.9476 21.8986 13.1946 22.1456C13.4416 22.3926 13.8388 22.3926 14.0859 22.1456L20.2859 15.9456C20.407 15.8294 20.4748 15.6671 20.4748 15.5C20.4748 15.3329 20.407 15.1706 20.2859 15.0544L14.0859 8.85437C13.9575 8.71632 13.771 8.64608 13.5821 8.66062Z" fill="#D1AB77"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="select-people" style="display: none;">
                        <h3><?php echo __('Person', 'karaoke'); ?></h3>
                        <div class="input-rows s-grid -m2 -d3">
                            <div class="radio-group">
                                <input type="radio" class="people-num" name="num" value="1-5">
                                <div class="detail">
                                    <h3>1-5</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="people-num" name="num" value="6-8">
                                <div class="detail">
                                    <h3>6-8</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="people-num" name="num" value="9-12">
                                <div class="detail">
                                    <h3>9-12</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="people-num" name="num" value="13-15">
                                <div class="detail">
                                    <h3>13-15</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="people-num" name="num" value="16-20">
                                <div class="detail">
                                    <h3>16-20</h3>
                                </div>
                            </div>
                            <div class="radio-group">
                                <input type="radio" class="people-num" name="num" value="21-25">
                                <div class="detail">
                                    <h3>21-25</h3>
                                </div>
                            </div>
                        </div>
                        <p><?php echo __('Please contact 02-675-4224 for room size over 25 people', 'karaoke'); ?></p>
                        <div class="btn-group s-grid -m2">
                            <a href="#" class="b-back" data-hide="select-people" data-show="select-date">
                                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.5 29.76C23.3687 29.76 29.76 23.3687 29.76 15.5C29.76 7.63134 23.3687 1.24001 15.5 1.24001C7.63133 1.24001 1.24001 7.63134 1.24001 15.5C1.24001 23.3687 7.63134 29.76 15.5 29.76ZM15.5 28.52C8.30219 28.52 2.48001 22.6978 2.48001 15.5C2.48001 8.3022 8.30219 2.48001 15.5 2.48001C22.6978 2.48001 28.52 8.30219 28.52 15.5C28.52 22.6978 22.6978 28.52 15.5 28.52ZM17.4181 22.3394C17.4448 22.3345 17.4714 22.3273 17.4956 22.32C17.7281 22.2788 17.9146 22.1093 17.98 21.8841C18.0454 21.6564 17.9776 21.4142 17.8056 21.2544L12.0513 15.5L17.8056 9.74563C18.0527 9.4986 18.0527 9.10141 17.8056 8.85438C17.5586 8.60735 17.1614 8.60735 16.9144 8.85438L10.7144 15.0544C10.5933 15.1706 10.5255 15.3329 10.5255 15.5C10.5255 15.6671 10.5933 15.8294 10.7144 15.9456L16.9144 22.1456C17.0427 22.2837 17.2292 22.3539 17.4181 22.3394Z" fill="#D1AB77"/>
                                </svg>
                                <?php echo __('Back', 'karaoke'); ?>
                            </a>
                            <button class="b-next" type="submit">
                                <?php echo __('Next', 'karaoke'); ?>
                                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.5002 1.23999C7.63156 1.23999 1.24023 7.63132 1.24023 15.5C1.24023 23.3687 7.63156 29.76 15.5002 29.76C23.3689 29.76 29.7602 23.3687 29.7602 15.5C29.7602 7.63132 23.3689 1.23999 15.5002 1.23999ZM15.5002 2.47999C22.698 2.47999 28.5202 8.30218 28.5202 15.5C28.5202 22.6978 22.698 28.52 15.5002 28.52C8.30242 28.52 2.48023 22.6978 2.48023 15.5C2.48023 8.30218 8.30242 2.47999 15.5002 2.47999ZM13.5821 8.66062C13.5555 8.66546 13.5288 8.67273 13.5046 8.67999C13.2721 8.72116 13.0856 8.89069 13.0202 9.11593C12.9548 9.34358 13.0227 9.58577 13.1946 9.74562L18.949 15.5L13.1946 21.2544C12.9476 21.5014 12.9476 21.8986 13.1946 22.1456C13.4416 22.3926 13.8388 22.3926 14.0859 22.1456L20.2859 15.9456C20.407 15.8294 20.4748 15.6671 20.4748 15.5C20.4748 15.3329 20.407 15.1706 20.2859 15.0544L14.0859 8.85437C13.9575 8.71632 13.771 8.64608 13.5821 8.66062Z" fill="#D1AB77"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
                <div class="select-room" style="display: none;"></div>
                <div class="sec-policy" style="display: none;">
                    <h3><?php echo __('RESERVATION POLICY', 'karaoke'); ?></h3>
                    <p><?php echo __('1.Karaoke room reservation is for a nightly rate only from 6pm to 12am.', 'karaoke'); ?></p>
                    <p><?php echo __('2.Karaoke room price, foods and beverages are subjected to 10% service charge and 7% Vat.', 'karaoke'); ?></p>
                    <p><?php echo __('3.Exceeding room capacity, there will be surcharge of 100++ Bahts per person.', 'karaoke'); ?></p>
                    <p><?php echo __('4.Check in after 7.30pm, there is a minimum food spending of 200++ Bahts per person.', 'karaoke'); ?></p>
                    <p><?php echo __('5.Reservation is completed after room deposit is made. The deposit will be deducted from the bill upon check out.', 'karaoke'); ?></p>
                    <p><?php echo __('6.The receipt and tax invoice can only be issued on the check in date.', 'karaoke'); ?></p>
                    <p> <?php echo __('7.Changing your booking date, a 1 day advance notice is required. Only one rescheduling within 30 days of booking date is permitted. In case of cancellation, the room deposit is not refundable. ', 'karaoke'); ?></p> 
                           
                    
                    <label for="">
                        <input type="checkbox" class="term-checked" checked>
                        <?php echo __('Agree with the service conditions and personal data protection policy', 'karaoke'); ?>
                    </label>
                    <div class="btn-group s-grid -m2">
                        <a href="#" class="b-back" data-hide="sec-policy" data-show="select-room">
                            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 29.76C23.3687 29.76 29.76 23.3687 29.76 15.5C29.76 7.63134 23.3687 1.24001 15.5 1.24001C7.63133 1.24001 1.24001 7.63134 1.24001 15.5C1.24001 23.3687 7.63134 29.76 15.5 29.76ZM15.5 28.52C8.30219 28.52 2.48001 22.6978 2.48001 15.5C2.48001 8.3022 8.30219 2.48001 15.5 2.48001C22.6978 2.48001 28.52 8.30219 28.52 15.5C28.52 22.6978 22.6978 28.52 15.5 28.52ZM17.4181 22.3394C17.4448 22.3345 17.4714 22.3273 17.4956 22.32C17.7281 22.2788 17.9146 22.1093 17.98 21.8841C18.0454 21.6564 17.9776 21.4142 17.8056 21.2544L12.0513 15.5L17.8056 9.74563C18.0527 9.4986 18.0527 9.10141 17.8056 8.85438C17.5586 8.60735 17.1614 8.60735 16.9144 8.85438L10.7144 15.0544C10.5933 15.1706 10.5255 15.3329 10.5255 15.5C10.5255 15.6671 10.5933 15.8294 10.7144 15.9456L16.9144 22.1456C17.0427 22.2837 17.2292 22.3539 17.4181 22.3394Z" fill="#D1AB77"/>
                            </svg>
                            <?php echo __('Back', 'karaoke'); ?>
                        </a>
                        <a href="#" class="b-next" data-hide="sec-policy" data-show="sec-address" data-check="policy">
                            <?php echo __('Next', 'karaoke'); ?>
                            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5002 1.23999C7.63156 1.23999 1.24023 7.63132 1.24023 15.5C1.24023 23.3687 7.63156 29.76 15.5002 29.76C23.3689 29.76 29.7602 23.3687 29.7602 15.5C29.7602 7.63132 23.3689 1.23999 15.5002 1.23999ZM15.5002 2.47999C22.698 2.47999 28.5202 8.30218 28.5202 15.5C28.5202 22.6978 22.698 28.52 15.5002 28.52C8.30242 28.52 2.48023 22.6978 2.48023 15.5C2.48023 8.30218 8.30242 2.47999 15.5002 2.47999ZM13.5821 8.66062C13.5555 8.66546 13.5288 8.67273 13.5046 8.67999C13.2721 8.72116 13.0856 8.89069 13.0202 9.11593C12.9548 9.34358 13.0227 9.58577 13.1946 9.74562L18.949 15.5L13.1946 21.2544C12.9476 21.5014 12.9476 21.8986 13.1946 22.1456C13.4416 22.3926 13.8388 22.3926 14.0859 22.1456L20.2859 15.9456C20.407 15.8294 20.4748 15.6671 20.4748 15.5C20.4748 15.3329 20.407 15.1706 20.2859 15.0544L14.0859 8.85437C13.9575 8.71632 13.771 8.64608 13.5821 8.66062Z" fill="#D1AB77"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="sec-address" style="display: none;">
                    <?php if(is_user_logged_in()): ?>
                        <h3><?php echo __('Login to your account', 'karaoke'); ?></h3>
                        <div class="input-addess">
                            <div class="input-row">
                                <label for=""><?php echo __('Name', 'karaoke'); ?></label>
                                <input type="text" class="fullname" value="<?php echo $user->display_name; ?>">
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('Email', 'karaoke'); ?></label>
                                <input type="email" class="email" value="<?php echo $user->user_email; ?>">
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('Phone', 'karaoke'); ?></label>
                                <input type="text" class="phone">
                            </div>
                        </div>
                    <?php else: ?>
                        <h3><?php echo __('Login Social', 'karaoke'); ?></h3>
                        <?php echo do_shortcode( '[nextend_social_login style="icon"]' ); ?>
                        <h3><?php echo __('Login to your account', 'karaoke'); ?></h3>
                        <div class="input-addess">
                            <div class="input-row">
                                <label for=""><?php echo __('Name', 'karaoke'); ?></label>
                                <input type="text" class="fullname">
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('Email', 'karaoke'); ?></label>
                                <input type="email" class="email">
                            </div>
                            <div class="input-row">
                                <label for=""><?php echo __('Phone', 'karaoke'); ?></label>
                                <input type="text" class="phone">
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="btn-group s-grid -m2">
                        <a href="#" class="b-back" data-hide="sec-address" data-show="sec-policy">
                            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 29.76C23.3687 29.76 29.76 23.3687 29.76 15.5C29.76 7.63134 23.3687 1.24001 15.5 1.24001C7.63133 1.24001 1.24001 7.63134 1.24001 15.5C1.24001 23.3687 7.63134 29.76 15.5 29.76ZM15.5 28.52C8.30219 28.52 2.48001 22.6978 2.48001 15.5C2.48001 8.3022 8.30219 2.48001 15.5 2.48001C22.6978 2.48001 28.52 8.30219 28.52 15.5C28.52 22.6978 22.6978 28.52 15.5 28.52ZM17.4181 22.3394C17.4448 22.3345 17.4714 22.3273 17.4956 22.32C17.7281 22.2788 17.9146 22.1093 17.98 21.8841C18.0454 21.6564 17.9776 21.4142 17.8056 21.2544L12.0513 15.5L17.8056 9.74563C18.0527 9.4986 18.0527 9.10141 17.8056 8.85438C17.5586 8.60735 17.1614 8.60735 16.9144 8.85438L10.7144 15.0544C10.5933 15.1706 10.5255 15.3329 10.5255 15.5C10.5255 15.6671 10.5933 15.8294 10.7144 15.9456L16.9144 22.1456C17.0427 22.2837 17.2292 22.3539 17.4181 22.3394Z" fill="#D1AB77"/>
                            </svg>
                            <?php echo __('Back', 'karaoke'); ?>
                        </a>
                        <button class="b-next -create" type="button">
                            <?php echo __('Next', 'karaoke'); ?>
                            <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5002 1.23999C7.63156 1.23999 1.24023 7.63132 1.24023 15.5C1.24023 23.3687 7.63156 29.76 15.5002 29.76C23.3689 29.76 29.7602 23.3687 29.7602 15.5C29.7602 7.63132 23.3689 1.23999 15.5002 1.23999ZM15.5002 2.47999C22.698 2.47999 28.5202 8.30218 28.5202 15.5C28.5202 22.6978 22.698 28.52 15.5002 28.52C8.30242 28.52 2.48023 22.6978 2.48023 15.5C2.48023 8.30218 8.30242 2.47999 15.5002 2.47999ZM13.5821 8.66062C13.5555 8.66546 13.5288 8.67273 13.5046 8.67999C13.2721 8.72116 13.0856 8.89069 13.0202 9.11593C12.9548 9.34358 13.0227 9.58577 13.1946 9.74562L18.949 15.5L13.1946 21.2544C12.9476 21.5014 12.9476 21.8986 13.1946 22.1456C13.4416 22.3926 13.8388 22.3926 14.0859 22.1456L20.2859 15.9456C20.407 15.8294 20.4748 15.6671 20.4748 15.5C20.4748 15.3329 20.407 15.1706 20.2859 15.0544L14.0859 8.85437C13.9575 8.71632 13.771 8.64608 13.5821 8.66062Z" fill="#D1AB77"/>
                            </svg>
                        </button>
                    </div>
                </div>
        </main>
    </div>

    <div class="full-booked-alert">
        <div class="info">
            <h3><?php echo __('Full Booked', 'karaoke'); ?></h3>
            <div class="close">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="32" height="32" viewBox="0 0 32 32">
                    <path d="M 16 4 C 9.3844239 4 4 9.3844287 4 16 C 4 22.615571 9.3844239 28 16 28 C 22.615576 28 28 22.615571 28 16 C 28 9.3844287 22.615576 4 16 4 z M 16 6 C 21.534697 6 26 10.465307 26 16 C 26 21.534693 21.534697 26 16 26 C 10.465303 26 6 21.534693 6 16 C 6 10.465307 10.465303 6 16 6 z M 12.707031 11.292969 L 11.292969 12.707031 L 14.585938 16 L 11.292969 19.292969 L 12.707031 20.707031 L 16 17.414062 L 19.292969 20.707031 L 20.707031 19.292969 L 17.414062 16 L 20.707031 12.707031 L 19.292969 11.292969 L 16 14.585938 L 12.707031 11.292969 z"></path>
                </svg>
            </div>
        </div>
    </div>  

    <div class="wait-loading">
        <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
            <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
            <path opacity="0.8" fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                C22.32,8.481,24.301,9.057,26.013,10.047z">
                <animateTransform attributeType="xml"
                    attributeName="transform"
                    type="rotate"
                    from="0 20 20"
                    to="360 20 20"
                    dur="0.8s"
                    repeatCount="indefinite"/>
            </path>
        </svg>
    </div>

    <?php
    $min = get_field('start_date', 'options');
    if( strtotime($min) < strtotime('now')){
        $min = date_i18n( 'Y-m-d' );
    }

    $min_arr = explode("-", $min);

    $max = get_field('end_date', 'options');
    $max_arr = explode("-", $max);

    $dis_date = '';
    $lock = [];
    if( have_rows('holiday', 'options') ):
        while( have_rows('holiday', 'options') ) : the_row();
            $date = get_sub_field('date');
            $select = get_sub_field('branch_select');
            if( $select == 'all' ){
                $date_arr = explode("-", $date);
                $dis_date .= '['.$date_arr[0].', '.(intval($date_arr[1]) - 1).', '.intval($date_arr[2]).'],';
            }else{
                $b_data = get_sub_field('choose_branch');
                foreach( $b_data as $val ){
                   $lock[$date][] = $val->term_id;
                }   
            }
        endwhile;
    endif;
    ?>

    <input type="hidden" class="branch-lock" value='<?php echo json_encode($lock); ?>'>
    <script>
        jQuery(document).ready(function($) {
            $('.datepicker').pickadate({
                format: 'yyyy-mm-dd', 
                min: [<?php echo $min_arr[0] ?>, <?php echo intval($min_arr[1]) - 1 ?>, <?php echo intval($min_arr[2]) ?>],
                max: [<?php echo $max_arr[0] ?>, <?php echo intval($max_arr[1]) - 1 ?>, <?php echo intval($max_arr[2]) ?>],
                disable: [<?php echo $dis_arr; ?>]
            })
        })
    </script>

    <?php wp_footer(); ?>
</body>

</html>