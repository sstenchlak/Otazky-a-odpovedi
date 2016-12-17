<?php
/**
 * Třída zpracuje jednu otázku.
 */

// Uložím data přímo z POST, protože se o to metoda postará sama :)
if (questions::addQuestions($_POST, $err)) {
	echo "1";
} else {
	// Vypíšu případné chyby k nultému, jedinému řádku.
	echo implode("\n", $err[0]);
}

exit;
