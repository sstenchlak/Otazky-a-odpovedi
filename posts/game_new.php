<?php
/**
 * Je volán z modal dialogu admin_newGame a slouží pro vytvoření nové hry,
 * bez ohledu na to, zda probíhá hra stará. Prozatím jediné informace, které
 * přijme, je pořadí sloupců ve hře, které by mělo být asi zkontrolováno.
 */

// Získám seznam sloupců
$columns = (isset($_POST['columns']) AND is_array($_POST['columns'])) ? $_POST['columns'] : array();
/*
// Ověřím počet
if (count($columns) > 20 OR count($columns) < 3) {
	echo "Počet vybraných sloupců musí být v rozmezí od tří do dvaceti. Hra by totiž vypadala špatně, kdyby zde bylo příliš málo, nebo hodně sloupců!";
	exit;
}
*/
/**
 * Tabulka duel_columns.
 */

// Úprava na multidimenzionální pole pro SQL
$toInsert = array();
foreach ($columns as $key => $value) {
	$toInsert[] = array(
		'order' => $key,
		'category' => $value,
		//'game_ID' => 1,
	);
}

// Smažu předchozí data
Game::endGame();

// Uložím data nová
DB::multiInsert('duel_columns', $toInsert);

/**
 * Hra je plně připravena..
 */
