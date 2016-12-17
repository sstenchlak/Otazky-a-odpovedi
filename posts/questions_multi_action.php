<?php

// Získání seznamu otázek
$questionsIDs = json_decode($_POST['data']);
if (!$questionsIDs) exit('Nic nebylo vybráno. Veberte prosím alespoň jeden řádek kliknutím.');

// Získání akce
$action = $_POST['multi_action'];

// Rozhodování, co se provede...
switch ($action) {

	case 'del': // Odstraní se
		$status = questions::delete( $questionsIDs );
	break;

	case 'setUsed': // Nastaví se použito
		$status = questions::setUsed( true, $questionsIDs );
	break;

	case 'setNotUsed':
		$status = questions::setUsed( false, $questionsIDs );
	break;

	case 'edit':
		// Upraví se
			$data = array();
			foreach (questions::$columns as $col_name => $__) {
				if (isset($_POST['set_' . $col_name]) AND $_POST['set_' . $col_name]) {
					$data[$col_name] = $_POST[$col_name];
				}
			}
			if (!$data) exit('Musíte něco upravit!');

		// Uloží se
			$status = questions::update($data, $questionsIDs, $err);
			if ($status === 0) $status = true;

			if (!$status) // Nastala chyba v datech tabulky!
				exit( @implode("<br>", $err) );
	break;

	case 'single_edit':
		// Upraví se
			$data = array();
			foreach (questions::$columns as $col_name => $__)
				$data[$col_name] = $_POST[$col_name];

		// Uloží se
			$status = questions::update($data, $questionsIDs, $err);
			if ($status === 0) $status = true;

			if (!$status) // Nastala chyba v datech tabulky!
				exit( @implode("<br>", $err) );
	break;

	case 'categories': // Ze souboru pro správu otázek..
		$status = questions::updateCategories( $_POST['name'], $questionsIDs /* Jako ty kategorie*/, $err );

		if (!$status) // Nastala chyba
			exit( @implode("<br>", $err) );
	break;

	default: // Chybný
		exit('Chybná hodnota akce!');
	break;
}

if ($status or $status === 0) {
	exit("1");
} else {
	echo("Nastala neznámá chyba.\n");
	var_dump($status);
}

exit;
