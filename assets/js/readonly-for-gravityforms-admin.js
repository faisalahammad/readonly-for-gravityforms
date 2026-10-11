( function ( $ ) {
	'use strict';

	var supportedTypes = [ 'text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'email', 'website', 'date', 'time', 'phone', 'address', 'name', 'list', 'multiselect', 'password' ];
	var unsupportedTypes = [ 'hidden', 'html', 'captcha', 'page', 'section', 'form', 'fileupload' ];

	function toggleReadonlySetting( field ) {
		var isSupported = supportedTypes.indexOf( field.type ) !== -1 && unsupportedTypes.indexOf( field.type ) === -1;
		var isReadOnly = field.gf_readonly_enable === true || field.gf_readonly_enable === 'true' || field.gf_readonly_enable === 1 || field.gf_readonly_enable === '1';

		if ( isSupported ) {
			$( '.readonly_setting' ).show();
		} else {
			$( '.readonly_setting' ).hide();
		}

		$( '#field_gf_readonly_enable' ).prop( 'checked', isReadOnly );
	}

	$( document ).on( 'gform_load_field_settings', function ( event, field ) {
		toggleReadonlySetting( field );
	} );
}( jQuery ) );
