(function($) {
	$(document).ready( function() {
		if ( $.isFunction( $.fn.wpColorPicker ) ) {
			var colorPickerOptions = {
				change: function( event, ui ) {
					if( typeof bws_show_settings_notice == "function" )
						bws_show_settings_notice();
				}
			}

			$( '.rltdpstsplgn_colorpicker' ).each( function() {
				$( this ).wpColorPicker( colorPickerOptions );
			});
		}
	});
})(jQuery);