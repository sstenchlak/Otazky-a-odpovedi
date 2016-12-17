/**
 * Javascriptový soubor pro oblusu stránek s importem nových otázek...
 */


// OBSLUHA MODAL DIALOGU PŘI ODESÍLÁNÍ FORMULÁŘE

formToModal = function() {
	$(this).ajaxSubmit({
		target: '#modalImport .modal-body',
		beforeSubmit: function(){
			// Předtím, než se odešle formulář!
			$('#modalImport .modal-body').html('<h2 style=\'text-align: center;\'>Počkejte prosím...</h2>');
			$('#modalImport').modal('show');
		},
	});

	// always return false to prevent standard browser submit and page navigation
	return false;
}

$("#firstInputFrom").submit(formToModal);


// OBSLUHA IMPORTU OTÁZEK Z TABULKY Z EXCELU

function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
    '\n': '<br>'
  };

 				/*	$.each(lines, function(index, value){
						trimmedLines[] = $.trim(value);
					});*/

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}


/**
 * Tabulka se aktualizovala, budou se získávat data...
 */
function tableRefresh(){
	// Vytvoří array z tabulky
		var myTableArray = [];
		$("#questionsSource table tbody tr").each(function() {
			var arrayOfThisRow = [];
			var tableData = $(this).find('td');

			if (tableData.length > 0) {
				tableData.each(function() {

					// Převedu html kód na řádky
					var lines = $(this).html().split("\n");

					// Každý řádek otrimuji
					var trimmedLines = [];
					lines.map(function(val){trimmedLines.push( $.trim(val) );});

					// Získám výsledný text tak, že uložím jako html a stáhnu jako text.
					var result = $('<div></div>').html(trimmedLines.join('').replace(/<br\s*[\/]?>/gi, "\n")).text();

					// Nyní otrimuji konce
					result = $.trim(result);

					// Uložím do finálního pole
					arrayOfThisRow.push( result );
				});
				// Uložím do většího pole
				myTableArray.push(arrayOfThisRow);
			}
		});

	if (myTableArray.length == 0) return;

	// Vytvoření tabulky z array
		$('#questionsView tbody').html('');

		var row = '';
		for (var i = 1; i <= myTableArray[0].length; i++) {
			row = row + '<th>' + i + '. sloupec</th>';
		}
		$('#questionsView thead tr').html(row);

		$.each(myTableArray, function(index, value){
			var row = '';
			$.each(value, function(index2, value2){
				row = row + '<td>' + escapeHtml(value2) + '</td>';
			});

			$('#questionsView tbody').append('<tr>!' + row + '</tr>');
		});

	// vytvoření zaškrtávacích políček pro sloupce
		$('.setColumns').html('').each(function(){
			// Projde každý element
			var col_name = $(this).attr('data-columnName');

			var row = '';
			for (var i = 1; i <= myTableArray[0].length; i++) {
				row = row + '<label class="radio-inline"><input type="radio" name="col_' + col_name + '" value="' + i + '"> ' + i + '</label> ';
			}

			$(this).html(row);
		});

	// Uložení do formuláře
	$('#dataInputForm').attr('value', JSON.stringify(myTableArray));
};