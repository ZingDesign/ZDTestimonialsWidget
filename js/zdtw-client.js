/**
 * Created by Sam on 6/06/14.
 */

jQuery(document).ready(function($){
    var prefix = 'zdtw'
        ,testimonialWidget = $('.' + prefix + '-testimonial')
        ,sidebar = testimonialWidget.first().parent();

    if( testimonialWidget.first().hasClass('show-multiple') ) {
        sidebar.addClass(prefix + '-parent-element');
    }
    
});