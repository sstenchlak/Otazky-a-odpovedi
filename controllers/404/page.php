<?php
/**
 * 404 stránka zastupující i 403 a chyby.
 */

if ($_POST) {
	echo "Stránka nebyla nalezena. Zřejmě chyba programu.";
	exit;
}

$this->addView('404');
$this->setTitle('Stránka nenalezena');