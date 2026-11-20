/**
 * main.js
 *
 * For all custom js codes.
 */



/* VANILLA JS */
// console.log("Fruit Theme");





/* JQUERY */
jQuery(document).ready(function ($) {
    var bookinLogs = getLocalBookingData();
    if (bookinLogs && bookinLogs.room) {
        $('.get-allroom, .select-location, .select-people, .select-date, .select-room').hide();
        $('.sec-address').fadeIn();
    }

    $(document).on('change', '.datepicker', function () {
        localStorage.removeItem('localData');
    });
    /*
    $('.room-branch').on('change', function(e){
        $(this).parents('.select-location').hide();
        $('.select-people').fadeIn();
    });
    */

    // Update slip
    $('.uploadslip-form form').on('submit', function (e) {
        e.preventDefault();

        var checkLog = $(this).find('input').val();
        if (checkLog == '') {
            alert('กรุณาในไฟล์สลิป');
        } else {
            var formData = new FormData();
            formData.append('logid', $('.log-id').val());
            formData.append('file_slip', $('.slip-file')[0].files[0]);
            formData.append('action', 'custom_upload_slip');

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                type: 'POST',
                success: function (res) {
                    //console.log(res);
                    window.location.href = '/booking/success?logid=' + res
                }
            });
        }
    });

    $(document).on('click', 'a.b-next', function (e) {
        var checkType = $(this).data('check');

        if (checkType === 'date') {
            e.preventDefault();

            var dateChoose = $('.datepicker').val();
            if (!dateChoose) {
                alert('กรุณาเลือกวันที่');
                return false;
            }
            if (!$('.ip-time:checked').length) {
                alert('กรุณาเลือกเวลา');
                return false;
            }

            var branchLockRaw = $('.branch-lock').val();
            if (branchLockRaw) {
                try {
                    var branchLock = JSON.parse(branchLockRaw);
                    (branchLock[dateChoose] || []).forEach(function (item) {
                        $('.b-' + item).hide();
                    });
                } catch (error) {
                    console.warn('Invalid branch-lock JSON:', error);
                }
            }

            $('.' + $(this).data('hide')).hide();
            $('.' + $(this).data('show')).fadeIn();
            setDataLocal();
            return false;

        } else if (checkType === 'location') {
            e.preventDefault();

            var branchId = $('.room-branch:checked').val();
            if (!branchId) {
                alert('กรุณาเลือกสาขา');
                return false;
            }

            var hide = $('.' + $(this).data('hide'));
            var show = $('.' + $(this).data('show'));

            updateLocalBookingData({ branch: branchId, room: '', num: '' });

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action: 'get_people_number',
                    date: $('.datepicker').val(),
                    location: branchId
                },
                type: 'POST',
                success: function (result) {
                    $('.select-people .input-rows').html(result);
                    hide.hide();
                    show.fadeIn();
                    setDataLocal();
                },
                error: function () {
                    alert('ไม่สามารถโหลดจำนวนผู้เข้าพักได้ กรุณาลองใหม่');
                }
            });
            return false;

        } else if (checkType === 'room') {
            e.preventDefault();

            var selectedRoom = $('.room-id:checked').val();
            if (!selectedRoom) {
                alert('กรุณาเลือกห้อง');
                return false;
            }

            updateLocalBookingData({ room: selectedRoom });
            $('.' + $(this).data('hide')).hide();
            $('.' + $(this).data('show')).fadeIn();
            setDataLocal();
            return false;

        } else if (checkType === 'policy') {
            e.preventDefault();

            if ($('.term-checked').is(':checked')) {
                $('.' + $(this).data('hide')).hide();
                $('.' + $(this).data('show')).fadeIn();
                setDataLocal();
            } else {
                alert('กรุณากดยินยอม');
            }
            return false;
        }
    });

    $(document).on('click', 'a.b-back', function (e) {
        $('.' + $(this).data('hide')).hide();
        $('.' + $(this).data('show')).fadeIn();

        return false;
    });

    $(document).on('submit', '.get-allroom', function (e) {
        e.preventDefault();

        var form = $(this);
        var dataValue = $('.people-num:checked').val();

        if (!dataValue) {
            alert('กรุณาเลือกจำนวนคน');
            return false;
        }
        if (dataValue === 'not-data') {
            $('.full-booked-alert').fadeIn();
            return false;
        }

        updateLocalBookingData({ num: dataValue, room: '' });

        $.ajax({
            url: form.attr('action') || '/wp-admin/admin-ajax.php',
            data: form.serialize(),
            type: (form.attr('method') || 'POST').toUpperCase(),
            beforeSend: function () {
                $('.wait-loading').fadeIn();
            },
            success: function (res) {
                var responseHtml = $.trim(String(res || ''));
                var $response = $('<div>').html(responseHtml);
                var hasAvailableRooms = $response.find('.room-id').length > 0;

                if (!responseHtml || responseHtml === 'false' || !hasAvailableRooms) {
                    alert('ไม่มีห้องว่างสำหรับจำนวนคนและวันที่ที่เลือก กรุณาเลือกใหม่');
                    $('.select-room').hide().empty();
                    form.show();
                    return;
                }

                form.hide();
                $('.select-room').html(responseHtml).fadeIn();
            },
            error: function () {
                alert('ไม่สามารถโหลดรายการห้องได้ กรุณาลองใหม่');
            },
            complete: function () {
                $('.wait-loading').fadeOut();
            }
        });

        return false;
    });


$(document).on('submit', '.select-room form', function (e) {
        e.preventDefault();

        var selectedRoom = $(this).find('.room-id:checked').val();
        if (!selectedRoom) {
            alert('กรุณาเลือกห้อง');
            return false;
        }

        var bookingData = getLocalBookingData() || {};
        if (!bookingData.date || !bookingData.branch || !bookingData.num) {
            alert('ข้อมูลการจองไม่ครบ กรุณาเริ่มเลือกวัน สาขา และจำนวนคนใหม่');
            localStorage.removeItem('localData');
            window.location.reload();
            return false;
        }

        updateLocalBookingData({ room: selectedRoom });

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'get_checkout_form',
                date: bookingData.date,
                num: bookingData.num,
                room: selectedRoom,
                branch: bookingData.branch
            },
            type: 'POST',
            beforeSend: function () {
                $('.wait-loading').fadeIn();
            },
            success: function (result) {
                if (!$.trim(String(result || ''))) {
                    alert('ไม่สามารถโหลดแบบฟอร์มการจองได้ กรุณาลองใหม่');
                    return;
                }
                $('.select-room').hide();
                $('.set-address').html(result).fadeIn();
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่');
            },
            complete: function () {
                $('.wait-loading').fadeOut();
            }
        });

        return false;
    });


$(document).on('click', '.b-next.-create', function (e) {
        e.preventDefault();

        var button = $(this);
        var fullname = $.trim($('.fullname').val() || '');
        var phone = $.trim($('.phone').val() || '');
        var email = $.trim($('.email').val() || '');
        var bookingData = getLocalBookingData() || {};

        if (!fullname || !phone || !email) {
            alert('กรุณากรอกข้อมูลให้ครบถ้วน');
            return false;
        }
        if (!bookingData.room || !bookingData.date) {
            alert('ข้อมูลห้องหรือวันที่จองไม่ครบ กรุณาเลือกห้องใหม่');
            return false;
        }

        var selectedTime = $('.set-address .time').val() || bookingData.time || '';
        if (!selectedTime) {
            alert('กรุณาเลือกเวลา');
            return false;
        }
        if (button.data('submitting')) {
            return false;
        }

        button.data('submitting', true).hide();

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: {
                action: 'save_booking_logs',
                fullname: fullname,
                phone: phone,
                room: bookingData.room,
                email: email,
                date: bookingData.date,
                time: selectedTime
            },
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('.wait-loading').fadeIn();
            },
            success: function (res) {
                if (!res || typeof res !== 'object') {
                    alert('ระบบตอบกลับไม่ถูกต้อง กรุณาลองใหม่');
                    button.data('submitting', false).show();
                    return;
                }

                if (res.status === 'error') {
                    alert('ห้องที่คุณเลือกถูกจองไปก่อนแล้ว กรุณาเลือกห้องใหม่');
                    updateLocalBookingData({ room: '' });
                    $('.set-address').hide().empty();

                    var roomForm = $('.get-allroom');
                    if (roomForm.length) {
                        roomForm.show().trigger('submit');
                    } else {
                        $('.select-room').hide().empty();
                        button.data('submitting', false).show();
                    }
                    return;
                }

                if (res.status === 'success' && res.data) {
                    localStorage.removeItem('localData');
                    window.location.href = '/booking/payment?logid=' + encodeURIComponent(res.data);
                    return;
                }

                alert('ไม่สามารถบันทึกการจองได้ กรุณาลองใหม่');
                button.data('submitting', false).show();
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่');
                button.data('submitting', false).show();
            },
            complete: function () {
                $('.wait-loading').fadeOut();
            }
        });

        return false;
    });


$('.full-booked-alert .close').on('click', function () {
        $('.full-booked-alert').fadeOut();
    });

    $('.search-booking form').on('submit', function () {
        var data = $(this);
        var checkLog = data.find('input').val();

        if (checkLog == '') {
            alert('กรุณากรอกเบอร์โทร');
        } else {
            $.ajax({
                url: data.attr('action'),
                data: data.serialize(),
                type: data.attr('method'),
                success: function (res) {
                    $('.search-booking').hide();
                    $('.booking-result').html(res);
                    $('.booking-result').fadeIn();
                }
            });
        }
        return false;
    });

    $(document).on('submit', '.update-food', function (e) {
        e.preventDefault();

        var data = $(this);
        $.ajax({
            url: data.attr('action'),
            data: data.serialize(),
            type: data.attr('method'),
            success: function (res) {
                alert('Update Success');
                location.reload();
            }
        });
        return false;
    });

    $('.upade-booking-foods').on('click', function () {
        $('.food-logs').hide();
        $('.food-form').fadeIn();
        return false;
    });

    $('.food-filter select').on('change', function () {
        $('.food-filter form').submit();
    });

    $('.food-filter input').on('keyup', function () {
        $('.food-filter form').submit();
    });

    $('.food-filter form').on('submit', function () {
        var data = $(this);
        $.ajax({
            url: data.attr('action'),
            data: data.serialize(),
            type: data.attr('method'),
            success: function (res) {
                $('.form-menu').html(res);
            }
        });

        return false;
    });

    $(document).on('click', '.food-item input[type="checkbox"]', function () {
        var parent = $(this).parents('.food-item');
        if ($(this).is(':checked')) {
            parent.find('.count').fadeIn();
        } else {
            parent.find('.count').hide();
        }
    });

    // Room Highlight
    $(document).on('click', '.room-id', function () {
        var data = {
            action: "get_room_highlight",
            room: $(this).val(),
            date: $('.datepicker').val()
        };

        $('.room-highlight').hide();

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: data,
            type: 'post',
            success: function (result) {
                $('.room-highlight').html(result);
                $('.room-highlight').fadeIn(1000);
            },
        });
    });

    //Admin 
    $(document).on('submit', '.admin-booking', function () {
        var data = $(this);
        $.ajax({
            url: data.attr('action'),
            data: data.serialize(),
            type: data.attr('method'),
            success: function (res) {
                $('.admin-booking').hide();
                $('.admin-result').html(res);
            }
        });

        return false;
    });

    $(document).on('change', '.admin-booking .date, .admin-booking .branch', function () {
        var thisData = $('.admin-booking');
        var formData = new FormData();
        formData.append('date', thisData.find('.date').val());
        formData.append('branch', thisData.find('.branch').val());
        formData.append('action', 'get_room_admin');

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            type: 'POST',
            beforeSend: function () {
                $('.wait-loading').fadeIn();
                $('.admin-booking .room').fadeOut();
            },
            success: function (res) {
                $('.select-room').html(res);
            },
            complete: function () {
                $('.wait-loading').fadeOut();
                $('.admin-booking .room').fadeIn();
            }
        });

        return false;
    });

    // Customize input date
    // $(function(){
    //     var dtToday = new Date();
    //     var ctMin = new Date( $('.s-booking-date').val() );

    //     var month = dtToday.getMonth() + 1;
    //     var day = dtToday.getDate();
    //     var year = dtToday.getFullYear();
    //     if(month < 10)
    //         month = '0' + month.toString();
    //     if(day < 10)
    //         day = '0' + day.toString();

    //     var minDate= $('.s-booking-date').val();
    //     var maxDate= $('.e-booking-date').val();

    //     if( ctMin.getTime() < dtToday.getTime() ){
    //         minDate = year + '-' + month + '-' + day;
    //     }

    //     $('input[type="date"]').attr('min', minDate);
    //     $('input[type="date"]').attr('max', maxDate);
    // });

    // Local booking data helpers
    function getLocalBookingData() {
        try {
            var stored = localStorage.getItem('localData');
            return stored ? JSON.parse(stored) : {};
        } catch (error) {
            console.warn('Invalid local booking data:', error);
            localStorage.removeItem('localData');
            return {};
        }
    }

    function updateLocalBookingData(values) {
        var current = getLocalBookingData() || {};
        Object.keys(values || {}).forEach(function (key) {
            current[key] = values[key];
        });
        localStorage.setItem('localData', JSON.stringify(current));
        return current;
    }

    function setDataLocal() {
        var current = getLocalBookingData() || {};
        var selectedTime = $('.ip-time:checked').val();
        var selectedRoom = $('.room-id:checked').val();
        var selectedBranch = $('.room-branch:checked').val();
        var selectedPeople = $('.people-num:checked').val();
        var dateValue = $('.datepicker').val();

        if (dateValue) current.date = dateValue;
        if (selectedTime) current.time = selectedTime;
        if (selectedRoom) current.room = selectedRoom;
        if (selectedBranch) current.branch = selectedBranch;
        if (selectedPeople && selectedPeople !== 'not-data') current.num = selectedPeople;

        current.fullname = $('.fullname').val() || current.fullname || '';
        current.phone = $('.phone').val() || current.phone || '';
        current.email = $('.email').val() || current.email || '';

        localStorage.setItem('localData', JSON.stringify(current));
    }

    // Admin booking
    $(document).on('click', '.addmin-add-booking', function (e) {
        e.preventDefault();
        var roomId, timeData;

        $('.room-id').each(function () {
            if ($(this).is(':checked')) {
                roomId = $(this).val();
            }
        });

        $('.time-chouce input').each(function () {
            if ($(this).is(':checked')) {
                timeData = $(this).val();
            }
        });

        if (!roomId || !$('.fullname').val() || !$('.phone').val() || !$('.email').val()) {
            alert('กรุณาเลือกกรอกข้อมูลให้ครบถ้วน');
        } else {
            var data = {
                action: "save_admin_booking_logs",
                fullname: $('.fullname').val(),
                phone: $('.phone').val(),
                room: roomId,
                email: $('.email').val(),
                date: $('.date-admin').val(),
                time: timeData
            };

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: data,
                type: 'post',
                success: function (result) {
                    window.location.href = '/admin-booking/admin-payment?logid=' + result;
                },
            });
        }
    });

    $(document).on('click', '.addmin-add-walkin', function (e) {
        e.preventDefault();
        var roomId, timeData;

        $('.room-id').each(function () {
            if ($(this).is(':checked')) {
                roomId = $(this).val();
            }
        });

        $('.time-chouce input').each(function () {
            if ($(this).is(':checked')) {
                timeData = $(this).val();
            }
        });

        if (!roomId || !$('.fullname').val() || !$('.phone').val() || !$('.email').val()) {
            alert('กรุณาเลือกกรอกข้อมูลให้ครบถ้วน');
        } else {
            var data = {
                action: "save_admin_booking_walkin_logs",
                fullname: $('.fullname').val(),
                phone: $('.phone').val(),
                room: roomId,
                email: $('.email').val(),
                date: $('.date-admin').val(),
                time: timeData
            };

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: data,
                type: 'post',
                success: function (result) {
                    alert('Add room to walkin is suceess');
                    location.reload();
                },
            });
        }
    });

    $(document).on('change', '.date-admin, .date-room select.branch', function () {
        var data = {
            action: "get_room_admin_result",
            date: $('.date-room .date-admin').val(),
            branch: $('.date-room select.branch').val()
        };

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: data,
            type: 'post',
            success: function (result) {
                $('.date-room .room').html(result);
            },
        });
    });

    $(document).on('change', '.date-room .room-id', function () {
        var data = {
            action: "get_room_bookind_log_admin",
            date: $('.date-admin').val(),
            roomid: $(this).val()
        };

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: data,
            type: 'post',
            beforeSend: function () {
                $('.wait-loading').fadeIn();
                $('.admin-booking .result-data').fadeOut();
            },
            success: function (result) {
                $('.result-data .booking-detail').html(result);
            },
            complete: function () {
                $('.wait-loading').fadeOut();
                $('.admin-booking .result-data').fadeIn();
            }
        });
    });

    // Copy link
    $(document).on('click', '.copylink-btn', function () {
        var url = $(this).data('url');
        var copyBtn = $(this);
        navigator.clipboard.writeText(url).then(
            function () {
                copyBtn.html('COPIED URL');
            },
            function () {
                console.log('Copy error');
            }
        );
    });

    // + - 
    $('.food-item .minus').on('click', function () {
        var old = $(this).parents('.input-group').find('.food-num').val();
        if (old == 0) {
            $(this).parents('.input-group').find('.food-num').val(0);
        } else {
            $(this).parents('.input-group').find('.food-num').val(parseInt(old) - 1);
        }
    });

    $('.food-item .plus').on('click', function () {
        var old = $(this).parents('.input-group').find('.food-num').val();
        if (old == 10) {
            $(this).parents('.input-group').find('.food-num').val(10);
        } else {
            $(this).parents('.input-group').find('.food-num').val(parseInt(old) + 1);
        }
    });

    // Add menu
    $('.select-foodmenu').on('click', function () {
        $(this).hide();
        $('.success-food-form').fadeIn();

        return false;
    });
});

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

/**
 * 
 */
function kpaymentForm(el) {
    var token = el.querySelector('input[name="token"]').value;
    var total = el.querySelector('script').dataset.amount;
    var currency = 'THB';
    var type = 'MCC';

    if (el.elements.dcc_currency) {
        currency = el.elements.dcc_currency.value;
        type = 'DCC';
    }


    var pid = el.querySelector('.payment-id').value;
    var params = 'action=ksent_request&token=' + token + '&amount=' + total + '&pid=' + pid + '&currency=' + currency + "&type=" + type;
    var ajax_request = new XMLHttpRequest();

    ajax_request.open("POST", '/wp-admin/admin-ajax.php', true);
    ajax_request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
    ajax_request.onload = function () {
        if (this.status >= 200 && this.status < 400) {
            var result, redirect, status,

                result = JSON.parse(this.response);
            status = result.status;
            redirect = result.redirect_url;
            if (status == 'success') {
                if (redirect != null) {
                    window.location = redirect;
                } else {
                    window.location = '/payment-callback/?logid=' + pid;
                }
            } else if (status == 'fail') {
                alert(result.message || 'Payment failed. Please try again.');
            } else {
                console.log(result);
            }
        } else {
            console.log("error" + this.status);
        }
    };

    ajax_request.onerror = function () { };
    ajax_request.send(params);
    return false;
}


setInterval(() => {
    const firstPage = document.querySelector('a.facetwp-page.first');
    const nextPage = document.querySelector('a.facetwp-page.next');
    if (nextPage) {
        nextPage.click();
    } else if (firstPage) {
        firstPage.click();
    }
}, 5000);



