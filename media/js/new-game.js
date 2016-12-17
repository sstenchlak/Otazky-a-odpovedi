/**
 * Skript pro stránku, kde se nachází výběr nové hry.
 */

 $(function() {

// Obsluha přesouvání položek
	$( "#columnSelect ul" ).sortable({
		connectWith: "#columnSelect ul",
		placeholder: "placeholder"
	}).disableSelection();

// Obsluha stisku tlačítka na novou hru
	$( "#columnForm" ).ajaxForm({
		success: function(){ window.location.href = "../game/"; },
		beforeSubmit: function(){ $("#modalNewGame .modal-body").html('<h2 style=\'text-align: center;\'>Počkejte prosím...</h2>'); }
	});

});