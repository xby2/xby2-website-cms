(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    $( window ).load(function() {

    	// Find elements
    	let serviceMeta = $('#service_meta .inside');
        let characterCountDesc = $('.remaining-characters');

        // If the service-meta box was found add listeners
        if (serviceMeta.length > 0)
        {
            let table = $('#service_meta table');
            let lastRow = table.find('tr').last();

            let lastRowClone = lastRow.clone();

            // Add point button listener
            $('#addPointButton').on('click', function () {
            	// Get the count of rows, don't allow adding a new point if there is already 6
            	let rowCount = $('.service-points-row').length;
            	if (rowCount < 6) {
                    let newRow = lastRowClone.clone();
                    newRow.find('th').html('');
                    newRow.find('input').attr("value", "");
                    table.append(newRow);
				}
            });

            // Remove point button listener
            $('#minusPointButton').on('click', function () {
                let pointsRows = table.find('tr.service-points-row');
                let deleteRow = pointsRows.last();
                if ( "service-points-row" === deleteRow.attr('class') && pointsRows.length > 1) {
                    deleteRow.remove();
                }
            });
		}

		// If the character count span was found,
		if (characterCountDesc.length > 0)
		{
        	// Find closest input
        	let input = characterCountDesc.siblings('input');
            let characterCountSpan = characterCountDesc.find('span');

            // Set maxlength attribute to 180
            input.attr('maxlength','180');

            // Find the starting value of remaining characters
            characterCountSpan.html(180 - input.val().length);

            // Set listener on keyup to adjust the value of remaining characters
			input.on('keyup', function () {
                characterCountSpan.html(180 - input.val().length);
            });
		}
    });

})( jQuery );
