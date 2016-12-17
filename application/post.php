<?php
/**
 * Skript, který je přiřazen v indexu, tedy ke každé stránce a jeho úkolem
 * je odchytat všecny POST požadavky, které zpracuje. Podle toho poté rozhodne,
 * co se stane, jestli ho volá ajax, nebo ne.
 */

// Kód akce, která by se měla provést
	$action = isset($_POST['action']) ? $_POST['action'] : null;

// Rozhodnutí o akci a zvolání hledaného souboru
if ($action) {
	// Zda byl použit AJAX
	$AJAX = isset($_POST['AJAX']) ? $_POST['AJAX'] : null;

	// Vytvoření routeru
		$Router = new Router();

	if (preg_match("/^([a-zA-Z0-9_])*/", $action) AND file_exists(DOCUMENT_ROOT . '/posts/' . $action . '.php')) {
		$Router->open(DOCUMENT_ROOT . '/posts/' . $action . '.php');
	} elseif ($AJAX) {
		echo "Neznámá akce k provedení. Zřejmě chyba programu!";
	}

	if ($AJAX) {
		$Router->run(true);
		exit;
	} else {
		// Zaktualizuje stránku
		Router::redirect();
	}
}
