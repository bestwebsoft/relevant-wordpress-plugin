(function($) {
	$(document).ready( function() {
		if ( $.isFunction( $.fn.wpColorPicker ) ) {
			var colorPickerOptions = {
				change: function( event, ui ) {
					if( 'function' == typeof bws_show_settings_notice )
						bws_show_settings_notice();
				}
			}

			$( '.rltdpstsplgn_colorpicker' ).each( function() {
				$( this ).wpColorPicker( colorPickerOptions );
			} );
		}
		var windowWidth = $( window ).width();
		if( 350 >= windowWidth ) {
			$( '.iris-square' ).css( {'margin-right': '2%', 'width': '163px'} );
			$( '.iris-picker-inner' ).css( 'width', '196px' );
			$( '.iris-picker' ).css( 'width', '209px' );
			$( '.iris-palette' ).css( 'width', '17px' );
		}
	} );
})(jQuery);