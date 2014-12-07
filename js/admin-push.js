var unipress_push_jquery = jQuery.noConflict();

unipress_push_jquery(document).ready(function($) {
	
	$( '#unipress-push-metabox' ).on( 'change', 'select#unipress-push-type', function( event ) {
		max_length = ( 'android' === $( 'option:selected', this ).val() ) ? UNIPRESS_API_ANDROID_MAX_CHAR : UNIPRESS_API_IOS_MAX_CHAR;
		$( 'span#push-max-length' ).text( max_length );
	});

	$( '#unipress-push-metabox' ).on( 'keyup paste', 'textarea#unipress-push-content', function( event ) {
		content_length = $( this ).val().length;
		max_length = $( 'span#push-max-length' ).text();
		$( 'span#push-current-length' ).text( content_length );
		remaining = max_length - content_length;
		if ( 10 > remaining ) {
			jQuery( 'span#push-current-length' ).removeClass();
			jQuery( 'span#push-current-length' ).addClass( 'unipress-push-count-superwarn' );
		} else if ( 20 > remaining ) {
			jQuery( 'span#push-current-length' ).removeClass();
			jQuery( 'span#push-current-length' ).addClass( 'unipress-push-count-warn' );
		} else {
			jQuery( 'span#push-current-length' ).removeClass();
			jQuery( 'span#push-current-length' ).addClass( 'unipress-push-count' );
		}
	});

});