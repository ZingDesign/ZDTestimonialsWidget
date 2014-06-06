/**
 * Created by Sam on 6/06/14.
 */

jQuery(document).ready(function($){

    var _w = window
        ,prefix = 'zdtw'
        ,clicks = 0
        ,testimonialWidgets = $('.' + prefix + '-testimonial')
        ,tallestWidget = getTallest( testimonialWidgets )
        ,firstTestimonial = testimonialWidgets.first();
//        ,sidebar = firstTestimonial.parent();



    if( firstTestimonial.hasClass('show-multiple') ) {

        firstTestimonial.addClass('active');

        testimonialWidgets.wrapAll('<div class="' + prefix + '-container" />');

        var sidebar = $('.' +prefix + '-container');


        sidebar.css({
            'width': sidebar.parent().width(),
            'height': tallestWidget
        });

//        sidebar.addClass(prefix + '-parent-element');

        $('<a class="'+prefix+'-nav '+prefix+'-prev" href="#"><span></span></a>'
            + '<a class="'+prefix+'-nav '+prefix+'-next" href="#"><span></span></a>').appendTo(sidebar);

        $('.'+prefix+'-nav').css({
            'top': tallestWidget / 2 - 10 + 'px'
        });

        testimonialWidgets.css({
            'width': sidebar.width()
            ,'height': tallestWidget
            ,'position': 'absolute'
        });

        $(document).on('click', '.'+prefix+'-nav', function() {

            var $ = jQuery
                ,$this = $(this)
                ,sidebar = $this.parent()
                ,allSlides = sidebar.find('.' + prefix + '-testimonial');

//        console.log(allSlides[0]);

            if( $this.hasClass(prefix+'-next')) {
                clicks ++;
            }
            else {
                clicks --;
            }

            if( clicks < 0 ) {
                clicks = allSlides.length - 1;
            }
            else if( clicks >= allSlides.length ) {
                clicks = 0
            }

//        console.log( clicks );

            var activeSlide = sidebar.find('.active')
                ,nextSlide = $(allSlides[clicks]);

            activeSlide.addClass('animate-out').removeClass('active');
            nextSlide.addClass('animate-in active');

//        _w.setTimeout(function() {
//            activeSlide.removeClass('animate-out');
//            nextSlide.removeClass('animate-in');
//        }, 500);

//    console.log('nav clicked');
        });
    }

    function getTallest( $objs ) {
        var tallest = 0;
        $objs.each(function(){
            var $this = $(this);
            if( $this.height() > tallest ) {
                tallest = $this.height();
            }
        });

        return tallest;
    }

});