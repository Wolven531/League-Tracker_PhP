jQuery(function(){
    if ((document.location.host.indexOf('.dev') > -1) || (document.location.host.indexOf('modernui') > -1) ) {
        jQuery("<script/>").attr('src', 'js/metro/metro-loader.js').appendTo($('head'));
    } else {
        jQuery("<script/>").attr('src', 'js/metro.min.js').appendTo(jQuery('head'));
    }
})

function headerPosition(){
    if (jQuery(window).scrollTop() > jQuery('header').height()) {
        jQuery("header .navigation-bar")
            .addClass("fixed-top")
            .addClass(" shadow")
        ;
    } else {
        jQuery("header .navigation-bar")
            .removeClass("fixed-top")
            .removeClass(" shadow")
        ;
    }
}

jQuery(function(){
    setTimeout(function(){headerPosition();}, 100);
});

jQuery(window).scroll(function(){
    headerPosition();
});