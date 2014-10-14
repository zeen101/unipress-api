var $unipress = jQuery.noConflict();

$unipress(document).ready(function($) {
		
	$( 'div#unipress-device-options' ).on( 'click', 'a.unipress-add-new-device', function( event ) {
		event.preventDefault();
        var data = {
            'action': 'unipress-api-add-new-device-row',
        }
        $.post( ajax_object.ajax_url, data, function( response ) {
            $( 'div#unipress-device-list' ).append( response );
        });
	});
	
	$( 'div#unipress-device-list' ).on( 'click', 'span.delete-device', function( event ) {
		event.preventDefault();
		parent = this;
        var data = {
            'action': 'unipress-api-delect-device-row',
            'device-id': $( this ).data( 'device-id' )
        }
        console.log( data );
        $.post( ajax_object.ajax_url, data, function( response ) {
	        $( parent ).closest( '.unipress-device-row' ).remove();
        });
	});
		
});