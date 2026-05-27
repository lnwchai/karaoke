/**
 * main.js
 *
 * For all custom js codes.
 */



/* VANILLA JS */
// console.log("Fruit Theme");





/* JQUERY */
jQuery(document).ready(function ($) {
    var bookinLogs = JSON.parse(localStorage.getItem('localData'));
    if (bookinLogs != null && bookinLogs.room != '') {
        $('.get-allroom, .select-location, .select-people, .select-date, .select-room').hide();
        $('.sec-address').fadeIn();
    }

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
        if ($(this).data('check') == 'date') {
            var branch_log = $('.branch-lock').val()
            var blogJson = JSON.parse(branch_log)
            for (const [key, value] of Object.entries(blogJson)) {
                console.log(key, value)
            }
            var dateChoose = $('.datepicker').val()
            var blogChecked = blogJson[dateChoose] 
            if( blogChecked != undefined ){
                blogChecked.forEach(elm => {
                  $('.b-'+elm).hide()  
                })
            }
            
            var checkLog = []
            $('.ip-time').each(function () {
                if ($(this).is(':checked')) {
                    checkLog.push('true');
                } else {
                    checkLog.push('false');
                }
            });

            if($('.datepicker').val() == ''){
                alert('กรุณาเลือกวันที่');
            }else if (!checkLog.includes('true')) {
                alert('กรุณาเลือกเวลา');
            } else {
                $('.' + $(this).data('hide')).hide();
                $('.' + $(this).data('show')).fadeIn();
                setDataLocal();
            }
        } else if ($(this).data('check') == 'location') {
            var checkLog = [];
            var branchId;
            $('.room-branch').each(function () {
                if ($(this).is(':checked')) {
                    checkLog.push('true');
                    branchId = $(this).val();
                } else {
                    checkLog.push('false');
                }
            });

            if (!checkLog.includes('true')) {
                alert('กรุณาเลือกสาขา');
            } else {
                var hide = $('.' + $(this).data('hide'));
                var show = $('.' + $(this).data('show'));
                var data = {
                    action: "get_people_number",
                    date: $('.datepicker').val(),
                    location: branchId
                };

                $.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    data: data,
                    type: 'post',
                    success: function (result) {
                        $('.select-people .input-rows').html(result);
                        hide.hide();
                        show.fadeIn();
                        setDataLocal();
                    },
                });
            }
        } else if ($(this).data('check') == 'room') {
            var checkLog = [];
            $('.room-id').each(function () {
                if ($(this).is(':checked')) {
                    checkLog.push('true');
                } else {
                    checkLog.push('false');
                }
            });

            if (!checkLog.includes('true')) {
                alert('กรุณาเลือกห้อง');
            } else {
                $('.' + $(this).data('hide')).hide();
                $('.' + $(this).data('show')).fadeIn();
                setDataLocal();
            }
        } else if ($(this).data('check') == 'policy') {
            if ($('.term-checked').is(':checked')) {
                $('.' + $(this).data('hide')).hide();
                $('.' + $(this).data('show')).fadeIn();
                setDataLocal();
            } else {
                alert('กรุณากดยินยอม');
            }
        }
        return false;
    });

    $(document).on('click', 'a.b-back', function (e) {
        $('.' + $(this).data('hide')).hide();
        $('.' + $(this).data('show')).fadeIn();

        return false;
    });

    $(document).on('submit', '.get-allroom', function (e) {
        e.preventDefault();

        var data = $(this);
        var checkLog = [];
        var dataValue;
        $('.people-num').each(function () {
            if ($(this).is(':checked')) {
                checkLog.push('true');
                dataValue = $(this).val();
            } else {
                checkLog.push('false');
            }
        });

        if (!checkLog.includes('true')) {
            alert('กรุณาเลือกจำนวนคน');
        } else {
            if (dataValue == 'not-data') {
                $('.full-booked-alert').fadeIn();
            } else {
                $.ajax({
                    url: data.attr('action'),
                    data: data.serialize(),
                    type: data.attr('method'),
                    success: function (res) {
                        data.hide();
                        $('.select-room').html(res);
                        $('.select-room').fadeIn();
                    }
                });
            }
        }
    });

    $(document).on('submit', '.select-room form', function (e) {
        e.preventDefault();

        var checkLog = [];
        $(this).find('.room-id').each(function () {
            if ($(this).is(':checked')) {
                bookinLogs.room = $(this).val();
            } else {
                checkLog.push('false');
            }
        });

        if (!checkLog.includes('true')) {
            alert('กรุณาเลือกท้อง');
        } else {
            var data = {
                action: "get_checkout_form",
                date: bookinLogs.date,
                num: bookinLogs.num,
                room: bookinLogs.room,
                branch: bookinLogs.branch,
            };

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: data,
                type: 'post',
                success: function (result) {
                    $('.select-room').hide();
                    $('.set-address').html(result);
                    $('.set-address').fadeIn();
                },
            });
        }
    });

    $(document).on('click', '.b-next.-create', function (e) {
        e.preventDefault();

        if ($('.fullname').val() == '' || $('.phone').val() == '' || $('.email').val() == '') {
            alert('กรุณาเลือกกรอกข้อมูลให้ครบถ้วน');
        } else {
            $(this).hide();

            var logs = JSON.parse(localStorage.getItem('localData'));
            var data = {
                action: "save_booking_logs",
                fullname: $('.fullname').val(),
                phone: $('.phone').val(),
                room: logs.room,
                email: $('.email').val(),
                date: logs.date,
                time: logs.time
            };

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: data,
                type: 'post',
                beforeSend: function () {
                    $('.wait-loading').fadeIn();
                },
                success: function (result) {
                    var res = JSON.parse(result);
                    if (res.status == 'error') {
                        alert(res.data)
                    } else {
                        localStorage.removeItem('localData');
                        window.location.href = '/booking/payment?logid=' + res.data;
                    }
                }
            });
        }
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

    // Set data to localStorage
    function setDataLocal() {
        var timtVal = '', roomId = '';
        $('.ip-time').each(function () {
            if ($(this).is(':checked')) {
                timtVal = $(this).val();
            }
        });

        $('.room-id').each(function () {
            if ($(this).is(':checked')) {
                roomId = $(this).val();
            }
        });

        var localData = {
            fullname: $('.fullname').val(),
            phone: $('.phone').val(),
            room: roomId,
            email: $('.email').val(),
            date: $('.datepicker').val(),
            time: timtVal
        }

        localStorage.setItem("localData", JSON.stringify(localData));
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

        if (roomId == '' || $('.fullname').val() == '' || $('.phone').val() == '' || $('.email').val() == '') {
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

        if (roomId == '' || $('.fullname').val() == '' || $('.phone').val() == '' || $('.email').val() == '') {
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
                alert(massage);
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



// test