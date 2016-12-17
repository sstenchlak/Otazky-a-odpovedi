<?php
/**
 * Kontroller stránky, kde se hraje hra.
 * (Všechny post požadavky na tuto stránku zpravuje soubor post/game:board.php a poté zavolá tento soubor)
 */

// Získá status o hře (game jsou vlastně data)
$data = Game::getGame();

$this->setTitle('Hra');

// Zaregistruje jeden z několika pohledů
if (!isset($data['status'])) {
	$this->addView('gameBoardNoGame');
} elseif ($data['status'] == 'question') {
	$this->addView('gameBoardQuestion', $data);
} else {
	$this->addView('gameBoard', $data);
}
