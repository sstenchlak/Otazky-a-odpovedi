<?php
/**
 * Soubor načte okno pro novou hru.
 */

// Registrace pohledu
$this->addView('adminNewGame', array(
	'columns' => Game::getAvailableCategories(),
));