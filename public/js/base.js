$(function () {

    //maximum cache size for previous container contents
    //https://github.com/defunkt/jquery-pjax
    $.pjax.defaults.maxCacheLength = 0;

    //pjax 初始化
    $(document).pjax('a', '#pjax-container');

    $(document).on('submit', 'form[data-pjax]', function(event) {
        $.pjax.submit(event, '#pjax-container');
    });

    // NProgress 初始化
    $(document).on('pjax:send', function() {
        NProgress.start();
    });

    $(document).on('pjax:complete', function() {
        NProgress.done();
    });

    $(document).on("pjax:end", function() {
        initTopMenu();
        initPNotify();
    });

    initBootbox();
    initPNotify();

});

/**
 * top menu initialize
 */
function initTopMenu() {

    var url = window.location;

    var element = $('ul.nav a').filter(function() {
        if(this.href == url || url.href.indexOf(this.href) == 0) {
            return true;
        } else {
            var element = $(this).removeClass('active').parent().parent().removeClass('in').parent();

            if (element.is('li')) {
                element.removeClass('active');
            }
        }
    }).addClass('active').parent().parent().addClass('in').parent();

    if (element.is('li')) {
        element.addClass('active');
    }
}




/**
 * Bootbox initialize
 */
function initBootbox() {
    bootbox.setLocale('zh_CN');
}

/**
 * initPNotify initialize
 *
 * @returns {boolean}
 */
function initPNotify() {

    PNotify.prototype.options.styling = 'bootstrap3';

    PNotify.desktop.permission();

    $pnotify = $('.pnotify');

    if($pnotify.size() <= 0) {
        return false;
    }

    $pnotify.each(function() {

        var $that = $(this);

        if($that.hasClass('alert-success')) {
            new PNotify({
                title: '成功提示',
                text: $that.html(),
                type: 'success',
                delay: 500,
                desktop: {
                    desktop: false
                }
            });
        } else if($that.hasClass('alert-danger')) {
            new PNotify({
                title: '错误提示',
                text: $that.html(),
                type: 'error',
                desktop: {
                    desktop: false
                }
            });
        } else if($that.hasClass('alert-info')) {
            new PNotify({
                title: '温馨提示',
                text: $that.html(),
                type: 'info',
                desktop: {
                    desktop: true
                }
            });
        } else if($that.hasClass('alert-warning')) {
            new PNotify({
                title: '警告',
                text: $that.html(),
                type: 'notice',
                desktop: {
                    desktop: false
                }
            });
        }

        $that.remove();
    });
}