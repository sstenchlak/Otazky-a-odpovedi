<?php
/**
 * PHP stránka, na které se zpracují otázky.
 */

// Kontrola dat na výběr sloupců
$used_cols = array();
foreach (questions::$columns as $col_name => $col_data) {
	if (!isset($_POST['col_' . $col_name])) exit('<div class="alert alert-danger">Nebyly označeny všechny sloupce v tabulce. Prosím zkontrolujte tabulku <b>Označení sloupců v tabulce</b>.</div>');
	if ($_POST['col_' . $col_name] != 'def') {
		if (in_array($_POST['col_' . $col_name], $used_cols))
			exit('<div class="alert alert-danger">Pro sloupec číslo ' . $_POST['col_' . $col_name] . ' bylo vybráno více hodnot. Prosím, napravte to.</div>');
		else
			$used_cols[] = $_POST['col_' . $col_name];
	}
}



// Data tabulky se stáhnou
	$data_input = json_decode($_POST['data']);
	if (!$data_input) exit('<div class="alert alert-danger">Vložte prosím tabulku, aby bylo možné pokračovat! Nic importováno nebylo.</div>');
	// Úprava dat
/*	foreach ($data_input as &$value)
		foreach ($value as &$subValue) {
		//	$subValue = str_replace("\n", '', $subValue);
			$subValue = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $subValue);
			$subValue = preg_replace('/ +/', " ", $subValue);
		}*/

// Začnou se sestavovat opravdová data na import
	$data = array();

	foreach ($data_input as $number => $line) {
		$newDataLine = array();

		// Všechny potřebné sloupce budou zahrnuty!
		foreach (questions::$columns as $col_name => $col_data) {
			$newDataLine[$col_name] = $_POST['col_' . $col_name] != 'def' ? $line[$_POST['col_' . $col_name] - 1] : $_POST['def_' . $col_name];
		}

		// Uložení řádku do finálního seznamu..
		$data[] = $newDataLine;
	}

// Spustí se dotaz, pokud bude úspěšný, pokračuje se
	$result = questions::addQuestions($data, $err);
	if (!$result) {
		// Nastala chyba v datech tabulky!
		echo '<div class="alert alert-danger"><h4>Nastalo několik chyb v datech tabulky!</h4>';
		foreach ($err as $key => $value) {
			echo "<hr><b>Na řádku " . ($key+1) . "</b><br>";
			echo implode("<br>", $value);
		}
		exit;
	}

// Příprava na pohled
	$vars = array();

// Uložení dat do databáze
	$vars['newCount'] = $result;

// Pomocné data a registrace pohledu
	$vars = array_merge($vars, questions::getStatus());
	// Zaregistrování pohledu
		$this->addView('adminQuestionsImportTableOK', $vars);
