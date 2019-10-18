$(document).ready(function () {
    // Start
    dancingLight();
});

// Header dancing light
var dancingLight = function () {

    var total = $('#header .menu').width();
    var leftExp = total - $('#header .exp').width() - 30;
    var leftAv = total - $('#header .exp').width() - $('#header .avatar').width() - 60;
    var leftS = $('#header .logo').width();
    var leftM1 = leftS + $('#header .search').width();
    var leftM2 = leftM1 + $('#header .menu-1').width();
    var leftM3 = leftM2 + $('#header .menu-2').width();
    var leftM4 = leftM3 + $('#header .menu-3').width();
    var leftM5 = leftM4 + $('#header .menu-4').width();

    // Header menu effect
    $('.search').hover(function () {
        $('.menu-light').animate({left: (leftS + 45) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('.menu-1').hover(function () {
        $('.menu-light').animate({left: (leftM1 - 8) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('.menu-2').hover(function () {
        $('.menu-light').animate({left: (leftM2 - 7) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('.menu-3').hover(function () {
        $('.menu-light').animate({left: (leftM3 - 10) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('.menu-4').hover(function () {
        $('.menu-light').animate({left: (leftM4 + 50) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('.menu-5').hover(function () {
        $('.menu-light').animate({left: (leftM5 + 65) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('.menu-6').hover(function () {
        $('.menu-light').animate({left: (leftM2 + 40) + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('#header .avatar').hover(function () {
        $('.menu-light').animate({left: leftAv + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
    $('#header .exp').hover(function () {
        $('.menu-light').animate({left: leftExp + 'px'}, {queue: false, duration: 100});
    }, function () {
        $('.menu-light').animate({left: '15px'}, {queue: false, duration: 100});
    });
}
