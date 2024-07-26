/**
 * Used on the general settings page to make the color scheme picker work.
 *
 * @since 1.0.0
 * @package PluginPizza\DefaultAdminColorScheme
 */

 /* global ajaxurl */

( function( $ ){
	'use strict';
	$( function() {

		var $colorpicker, $stylesheet, currentStylesheetUrl;

		$( '.color-palette' ).click( function() {
			$( this ).siblings( 'input[name="admin_color"]' ).prop( 'checked', true );
		});

		$colorpicker         = $( '#color-picker' );
		$stylesheet          = $( '#colors-css' );
		currentStylesheetUrl = $( '#colors-css' ).attr( 'href' );

		$colorpicker.on( 'click.colorpicker', '.color-option', function() {
			var colors,
				$this = $(this);

			if ( $this.hasClass( 'selected' ) ) {
				return;
			}

			$this.siblings( '.selected' ).removeClass( 'selected' );
			$this.addClass( 'selected' ).find( 'input[type="radio"]' ).prop( 'checked', true );

			// Load the colors stylesheet.
			// The default color scheme won't have one, so we'll need to create an element.
			if ( 0 === $stylesheet.length ) {
				$stylesheet = $( '<link rel="stylesheet" />' ).appendTo( 'head' );
			}
			$stylesheet.attr( 'href', $this.children( '.css_url' ).val() );

			// Repaint icons.
			if ( typeof wp !== 'undefined' && wp.svgPainter ) {
				try {
					colors = JSON.parse( $this.children( '.icon_colors' ).val() );
				} catch ( error ) {}

				if ( colors ) {
					wp.svgPainter.setColors( colors );
					wp.svgPainter.paint();
				}
			}

			// Update default color scheme.
			$.post( ajaxurl, {
				action:       'save-default-color-scheme',
				color_scheme: $this.children( 'input[name="admin_color"]' ).val(),
				nonce:        $( '#color-nonce' ).val()
			});
		});
	});
})( jQuery );
