<?php
/**
 * Otevře se tato adresa, kde se zadá nová instrukce, například otevření otázky,
 * nebo zodpovězení otázky. Soubor akci zpracuje a vrátí GameBoard, což je podobné, jako by zmáčkl F5.
 */

// Zjištění, jaká akce je požadována
	$action = $_POST['subaction'];

switch ($action) {
	case 'open_question': // Žádá otázku z tabulky o určitých souřadnicích, kategorii a obtížnosti.
		Game::openQuestion($_POST['x'], $_POST['y']);
	break;

	case 'close_question': // Posílá zprávu o tom, kdo na otázku odpověděl a žádá herní plátno
		Game::closeQuestion($_POST['team']);
	break;

	case 'open_old_question':
		Game::reopenQuestion($_POST['x'], $_POST['y']);
	break;
}

// Dojde k vrácení herní plochy, tedy gameBoard
	include(DOCUMENT_ROOT . '/controllers/game/page.php');