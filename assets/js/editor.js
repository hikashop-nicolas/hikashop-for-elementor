/* global jQuery, elementor, hikashopElementor */
( function ( $ ) {
	'use strict';

	function request( data ) {
		return $.getJSON(
			hikashopElementor.ajaxUrl,
			$.extend( { action: 'hikashop_elementor_products', nonce: hikashopElementor.nonce }, data )
		);
	}

	$( window ).on( 'elementor:init', function () {
		var Select2 = elementor.modules.controls.Select2;

		var ProductSelect = Select2.extend( {
			getSelect2DefaultOptions: function () {
				return $.extend( Select2.prototype.getSelect2DefaultOptions.apply( this, arguments ), {
					minimumInputLength: 0,
					ajax: {
						url: hikashopElementor.ajaxUrl,
						dataType: 'json',
						delay: 250,
						data: function ( params ) {
							return {
								action: 'hikashop_elementor_products',
								nonce: hikashopElementor.nonce,
								q: params.term || ''
							};
						},
						processResults: function ( response ) {
							return { results: response && response.success ? response.data : [] };
						}
					}
				} );
			},

			onReady: function () {
				var self = this;
				var ids = this.getControlValue();
				if ( ids && ! Array.isArray( ids ) ) {
					ids = [ ids ];
				}
				// The saved products need their names before select2 can show them
				if ( ids && ids.length ) {
					request( { ids: ids.join( ',' ) } ).done( function ( response ) {
						if ( ! response || ! response.success ) {
							return;
						}
						response.data.forEach( function ( item ) {
							if ( ! self.ui.select.find( 'option[value="' + item.id + '"]' ).length ) {
								self.ui.select.append( new Option( item.text, item.id, true, true ) );
							}
						} );
						self.ui.select.trigger( 'change.select2' );
					} );
				}
				Select2.prototype.onReady.apply( this, arguments );
			}
		} );

		elementor.addControlView( 'hikashop_product_select', ProductSelect );

		var edited = null;
		elementor.hooks.addAction( 'panel/open_editor/widget', function ( panel, model ) {
			edited = model;
		} );

		// The HikaShop module settings are saved by HikaShop under an id derived from the element id
		$( document ).on( 'click', '.hikashop-elementor-module-settings', function () {
			if ( ! edited || ! /^[0-9a-f]+$/i.test( edited.id ) ) {
				return;
			}
			var numericId = 300000000 + ( parseInt( edited.id.substr( 0, 7 ), 16 ) % 600000000 );
			var url = hikashopElementor.settingsUrl
				+ '&module_name=' + encodeURIComponent( this.getAttribute( 'data-module' ) )
				+ '&options_view=' + encodeURIComponent( this.getAttribute( 'data-view' ) )
				+ '&options_field=' + encodeURIComponent( this.getAttribute( 'data-field' ) )
				+ '&numeric_id=' + numericId;
			var model = edited;
			var popup = window.open( url, 'hikashop_module_settings', 'width=1000,height=760,scrollbars=yes' );
			var timer = setInterval( function () {
				if ( popup && ! popup.closed ) {
					return;
				}
				clearInterval( timer );
				if ( model && typeof model.renderRemoteServer === 'function' ) {
					model.renderRemoteServer();
				}
			}, 700 );
		} );
	} );
}( jQuery ) );
