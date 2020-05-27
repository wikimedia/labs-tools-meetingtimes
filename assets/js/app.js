( function () {
	'use strict';

	var datalist = document.querySelector( '#tz-datalist' ),
		tzFieldset = document.querySelector( '#tz-fieldset' ),
		timezoneInputs = document.querySelectorAll( '.tz-input' );

	timezoneInputs.forEach( function ( tzInput ) {
		// Listen for changes to each input.
		// @TODO Add debouncing.
		tzInput.addEventListener( 'keyup', function () {
			var url = tzFieldset.dataset.searchUrl + '?q=' + tzInput.value;
			fetch( url )
				.then( function ( res ) {
					return res.json();
				} )
				.then( function ( out ) {
					datalist.innerText = '';
					out.forEach( function ( res ) {
						var opt = document.createElement( 'option' );
						opt.value = res;
						datalist.appendChild( opt );
					} );
				} )
				.catch( function ( err ) {
					throw err;
				} );
		} );
	} );
}() );
