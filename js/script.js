(function($) {
	$(document).ready( function() {
		$( '#rltdpstsplgn_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#rltdpstsplgn_settings_notice' ).css( 'display', 'block' );
			};
		});
	});
})(jQuery);
