/**
 * Soubor, který se přiřadí k seznam otázek.
 */

$(function(){
	/**
	 * Obsluha odeslání akce
	 */
	$('#multi_action_form').submit(function(){
		// Musí se rozhodnout, co se má provést
		var val = $('[name="multi_action"]').fieldValue()[0];

		// Hromadná editace dat, sloupců
		if (val == 'edit') {
			$('#questions_multi_edit').modal('show');
			return false;
		}

		// Odešle se ajaxem
		$(this).ajaxSubmit({
			beforeSubmit: function() {
				$('#multi_action_form .question-alert').css('display', 'none');
				$('#multi_action_form .question-alert-load').css('display', 'block');
				$('#multi_action_form form *').prop('disabled', true);
			},
			success: function(data) {
				$('#multi_action_form form *').prop('disabled', false);
				$('#multi_action_form form *').change();
				$('#multi_action_form .question-alert').css('display', 'none');
				if (data == '1') {
					// Refresh stránky!
					window.location.href = window.location.href;
				}
				else {
					$('#multi_action_form .question-alert-fail').css('display', 'block');
					$('#multi_action_form .question-alert-fail pre').html(data);
				}
			}
		});

		// Nakonec se formulář neodešle
			return false;
	});


	/**
	 * Obsluha multi_edit
	 */
	$('#questions_multi_edit form').ajaxForm({
		beforeSubmit: function() {
			$('#questions_multi_edit .question-alert').css('display', 'none');
			$('#questions_multi_edit .question-alert-load').css('display', 'block');
			$('#questions_multi_edit form *').prop('disabled', true);
		},

		// success identifies the function to invoke when the server response
		// has been received
		success: function(data) {
			$('#questions_multi_edit form *').prop('disabled', false);
			$('#questions_multi_edit form *').change();
			$('#questions_multi_edit .question-alert').css('display', 'none');
			if (data == '1') {
				// Refresh stránky!
				window.location.href = window.location.href;
			}
			else {
				$('#questions_multi_edit .question-alert-fail').css('display', 'block');
				$('#questions_multi_edit .question-alert-fail pre').html(data);
			}
		}
	});
	// Šedé pole
	$('#questions_multi_edit table tbody tr .disabler').change(function(){
		$(this).closest( "tr" ).find('*:not(.disabler)').prop('disabled',!$(this).prop('checked'));
	}).change();



	/**
	 * Obsluha single edit.
	 */
	$('#question_edit form').ajaxForm({
		beforeSubmit: function() {
			$('#question_edit .question-alert').css('display', 'none');
			$('#question_edit .question-alert-load').css('display', 'block');
			$('#question_edit form *').prop('disabled', true);
		},

		// success identifies the function to invoke when the server response
		// has been received
		success: function(data) {
			$('#question_edit form *').prop('disabled', false);
			$('#question_edit form *').change();
			$('#question_edit .question-alert').css('display', 'none');
			if (data == '1') {
				// Úspěch
				window.location.href = window.location.href;
			}
			else {
				$('#question_edit .question-alert-fail').css('display', 'block');
				$('#question_edit .question-alert-fail pre').html(data);
			}
		}
	});

	$('#questions tbody tr').dblclick( function(){

		// Vybere pouze položku na kterou se kliklo
			$('.tablesorter tbody tr').toggleClass( 'selectedRow', false );
			$(this).toggleClass( 'selectedRow', true );
			updateTablesorterSelect();

		var questionLine = this;
		// Projdu každé pole v modal dialogu pro editaci
		$('#question_edit .field').each(function(i, obj){
			// Musím získat hodnotu z tabulky
			var columnIndex;
			$('#questions thead th').each(function(i2, obj2){
				if ($(obj).data('field') == $(obj2).data('field'))
					columnIndex = i2;
			});
			// Hodnota políčka z tabulky se získá z pole, nebo z data-value
			var value = $('td', questionLine).eq(columnIndex).is("[data-value]") ? $('td', questionLine).eq(columnIndex).data('value') : $('td', questionLine).eq(columnIndex).text();

			if ($(this).is('input')) {
				$(this).attr('value', value);
			}

			if ($(this).is('textarea')) {
				$(this).text(value);
			}

			if ($(this).is('.radio')) {
				if (value) {
					$('[value=1]').attr('checked',true);
				} else {
					$('[value=0]').attr('checked',true);
				}
			}
		});

		$('#question_edit .question-alert').css('display', 'none');
		$('#question_edit .question-alert-load').css('display', 'none');
		$('#question_edit').modal('show');
	});

});