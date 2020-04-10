'use strict';

;( function ( document, window, index )
{
	var inputs = document.querySelectorAll( '.inputfilechoice' );
	Array.prototype.forEach.call( inputs, function( input )
	{
		var label	 = input.nextElementSibling,
			labelVal = label.innerHTML;

		var updatedcheckbox = label.nextElementSibling;

		input.addEventListener( 'change', function( e )
		{
			var fileName = '';
			if( this.files && this.files.length > 1 )
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
			else
				fileName = e.target.value.split( '\\' ).pop();

			if( fileName )
			{
				label.querySelector( 'span' ).innerHTML = fileName;
				updatedcheckbox.checked = true;
				updatedcheckbox.value = "1";
			}
			else
			{
				label.innerHTML = labelVal;
				updatedcheckbox.checked = false;
				updatedcheckbox.value = "0";
			}
		});

		// Firefox bug fix
		input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
		input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
	});
}( document, window, 0 ));