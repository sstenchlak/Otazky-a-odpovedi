/**
 * JS třída pro správu tabulky tablesorter...
 */

$(function(){
	/**
	 * Inicializace pluginu pro řazení tabulky
	 */
	$(".tablesorter").tablesorter();


	/**
	 * Obsluha označování v tabulce
	 */
	last_row=-1;
	$('.tablesorter tbody tr').click(function(evt){
		// Kliknutí na tabulku
		var ctrl = evt.ctrlKey;
		var shift = evt.shiftKey;

		var hadClass = $(this).hasClass('selectedRow');

		if (!ctrl)
			$('.tablesorter tbody tr').removeClass( 'selectedRow' );

		if (shift) {
			if (last_row < $(this).index()) {
				var gt = last_row;
				var lt = $(this).index();
			} else {
				var gt = $(this).index();
				var lt = last_row;
			}

			var gt = gt-1;
			var gt_text = '';
			if (gt != -1) var gt_text = ':gt(' + gt + ')';

			$('.tablesorter tbody tr:lt(' + (lt+1) + ')' + gt_text).addClass( 'selectedRow' );

		} else {
			$(this).toggleClass( 'selectedRow', !hadClass );
		}

		updateTablesorterSelect();

		last_row = $(this).index();
	});


});

function updateTablesorterSelect() {
	// Proběhne uložení otázek do proměnné
		arrayOfQuestions = [];
		$('table.tablesorter tbody tr.selectedRow').each(function(){
			arrayOfQuestions[arrayOfQuestions.length] = $(this).attr('data-ID');
		});
		$('.dataInputForm').attr('value', JSON.stringify(arrayOfQuestions));
		$('.dataCount').text(arrayOfQuestions.length);


	// Oznámím, že se změnil výběr
	$('.tablesorter').trigger('selectedRowsChanged');
}

/**	Vybere, nebo zruší výběr na všechny položky **/
function selectAll(select) {
	$('.tablesorter tbody tr').toggleClass( 'selectedRow', select );
	updateTablesorterSelect();
}