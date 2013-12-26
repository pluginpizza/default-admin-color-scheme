// Add 'Default' to scheme currently selected as default?

(function($){
	$(document).ready( function() {
		var $colorpicker, $stylesheet, currentStylesheetUrl;

		$('.color-palette').click( function() {
			$(this).siblings('input[name="admin_color"]').prop('checked', true);
		});

		$colorpicker = $( '#color-picker' );
		$stylesheet = $( '#colors-css' );
		currentStylesheetUrl = $( '#colors-css' ).attr( 'href' );

		$colorpicker.on( 'hover.colorpicker', '.color-option', function() {
			var colors;
			var $this = $(this);

			// Load the colors stylesheet
			$stylesheet.attr( 'href', $this.children( '.css_url' ).val() );

			// Repaint icons
			if ( typeof wp !== 'undefined' && wp.svgPainter ) {
				try {
					colors = $.parseJSON( $this.children( '.icon_colors' ).val() );
				} catch ( error ) {}

				if ( colors ) {
					wp.svgPainter.setColors( colors );
					wp.svgPainter.paint();
				}
			}
		});

		$colorpicker.on( 'mouseleave.colorpicker', '.color-option', function() {
			$stylesheet.attr( 'href', currentStylesheetUrl );
		});

		$colorpicker.on( 'click.colorpicker', '.color-option', function() {
			var $this = $(this);

			if ( $this.hasClass( 'selected' ) ) {
				return;
			}

			currentStylesheetUrl = $this.children( '.css_url' ).val();

			$this.siblings( '.selected' ).removeClass( 'selected' );
			$this.addClass( 'selected' ).find( 'input[type="radio"]' ).prop( 'checked', true );

			// Update default color scheme
			$.post( ajaxurl, {
				action:       'save-default-color-scheme',
				color_scheme: $this.children( 'input[name="admin_color"]' ).val(),
				nonce:        $('#color-nonce').val()
				});
		});
	});

})(jQuery);
