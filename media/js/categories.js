/**
 * Soubor, který se přiřadí ke správě kategorií
 */

$(function(){
	/**
	 * Obsluha multi_edit
	 */
	$('#categories_edit form').ajaxForm({
		beforeSubmit: function() {
			$('#categories_edit .question-alert').css('display', 'none');
			$('#categories_edit .question-alert-load').css('display', 'block');
			$('#categories_edit form *').prop('disabled', true);
		},

		// success identifies the function to invoke when the server response
		// has been received
		success: function(data) {
			$('#categories_edit form *').prop('disabled', false);
			$('#categories_edit form *').change();
			$('#categories_edit .question-alert').css('display', 'none');
			if (data == '1') {
				// Refresh stránky!
				window.location.href = window.location.href;
			}
			else {
				$('#categories_edit .question-alert-fail').css('display', 'block');
				$('#categories_edit .question-alert-fail pre').html(data);
			}
		}
	});
	// Šedé pole
	$('#categories_edit table tbody tr .disabler').change(function(){
		$(this).closest( "tr" ).find('*:not(.disabler)').prop('disabled',!$(this).prop('checked'));
	}).change();

	/**
	 * Při změně výběru řádku si uloží kategorie pro našeptávač...
	 */
	$('#categories').bind('selectedRowsChanged',  function(){
		$('#categories_list').html('');
		$('#categories tbody tr.selectedRow').each(function(){
			$('#categories_list').append('<option value="' + $('[data-field="category"]', this).text() + '">');
		});
	});
});