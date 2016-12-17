/**
 * JS soubor pro herní plátno a pro zobrazování otázek a odpovědí.
 */

// Odešle data serveru a přitom server pošle nové herní pole.
// Například se odešle požadavek na zobrazení otázky a server odpoví zasláním HTML s otázkou
function getGameBoard(data){
	$.extend(data, {
		action: 'game_board',
		AJAX: true
	});
	$.ajax({
		type: "POST",
		url: '',
		data: data,
		success: function( result ) {
			openScreen();
			$('#gameBoard').html(result);
		}
	});
}

/**
 * Na základě kliknutí na otázku vyvolá nový GameBoard
 */
function openQuestion(htmlButton, isOld){
	closeScreen(function(){
		var data = { x: $(htmlButton).data('x'), y: $(htmlButton).data('y'), subaction: ( isOld ? 'open_old_question' : 'open_question') };
		getGameBoard(data);
	});
}

/**
 * Po zobrazení odpovědi má na výběr tři možnosti, co udělat.
 * 0 - Odpověděl tým nalevo
 * 1 - Odpověděl tým napravo
 * 2 - Neodpověděl nikdo, políčko bude pr
 */
function closeQuestion(team)
{
	closeScreen(function(){
		getGameBoard({
			subaction: 'close_question',
			team: team,
		});
	});
}

/**
 * Dvě funkce na zatemnění a odtemnění celého horního plátna při načítání nových dat getGameBoard.
 * Funkce closeScreen po dokončení zavolá funkci f.
 */
function openScreen() {
	$("#darkness").fadeTo(200,0,function(){$(this).css('display','none');});
}
function closeScreen(f) {
	$("#darkness").css('display','block').fadeTo(200,1,f);
}

/**
 * Obsluha klávesových zkratek
 */
$(document).keydown(function(e) {
	if(e.which == 13 || e.which == 32) {
		$('#question').click();
	}

	if(e.which == 37) {
		$('.buttonLeft').click();
	}

	if(e.which == 40) {
		$('.buttonCenter').click();
	}

	if(e.which == 39) {
	console.log(e);
		$('.buttonRight').click();
	}

});
