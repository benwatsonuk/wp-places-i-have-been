jQuery( document ).ready( function( $ ) {
    // console.log('Hey');
    jQuery('form fieldset').each(function(){
        jQuery(this).find('.countryContainer').slideUp();
        jQuery(this).find('legend').append('<a class="icon-container"><span class="plus-icon"></span></a>');
    });

    jQuery('form legend').on('click', function(e) {
        jQuery(this).toggleClass('active');
        jQuery(this).next().slideToggle();
    });
});