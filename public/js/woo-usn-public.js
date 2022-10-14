(function( $ ) {
	'use strict';

	/**
	 * This init the phone validator on the checkout page.
	 *
	 * @param country_code the country code that will be used for the phone number field.
	 */
	function woo_usn_init_pn_validator( country_code ) {
		var iti;
		var woousn_phone_number_validator = document.querySelector( "#billing_phone" );
		try {
			iti = window.intlTelInput(
				woousn_phone_number_validator,
				{
					initialCountry: country_code,
					utilsScript : woo_usn_ajax_object.woo_usn_phone_utils_path
				}
			);

			woousn_phone_number_validator.addEventListener("countrychange", function() {
				woo_usn_check_pn_validity( iti );
			});
		} catch (e) {
			console.info( e );
		}
		return iti;
	}

	function woo_usn_check_pn_validity( iti ){
		var isValid  = iti.isValidNumber();
		var selector = $( 'input.woo-usn-pn-is-valid' );
		if ( ! isValid ) {
			selector.val( 'no' );
		} else {
			selector.val( 'yes' );
		}
	}
	$( document ).ready(
		function(){
			if ( woo_usn_ajax_object.woo_usn_phone_utils_path ) {

				var country_code = 'us';
				if ( woo_usn_ajax_object.user_country_code ) {
					country_code = woo_usn_ajax_object.user_country_code;
				}
				var iti_pn;
				$( document.body ).on(
					'change',
					'#billing_country',
					function() {
						iti_pn = woo_usn_init_pn_validator( $( this ).val() );
					}
				);

				$( document.body ).on(
					'change keydown',
					'#billing_phone',
					function() {
						woo_usn_check_pn_validity( iti_pn );
					}
				);

				iti_pn = woo_usn_init_pn_validator( country_code );
				$( "#billing_country" ).trigger( 'change' );
				$( "#billing_phone" ).trigger( 'change' );
			}
		}
	);

})( jQuery );
