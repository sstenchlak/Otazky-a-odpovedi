<?php
/**
 * Konkrétní stránka administrace. Hlavní stránka
 */

// Data, která se odešlou šabloně
	$data = array();

// Naplní se otázkama
	$data['questions'] = questions::getStatus();
	$data['questions']['categories'] = count(questions::getCategories());
	$data['isGame'] = Game::isGame();

// Registrace pohledu
	$this->addView('adminIndex', $data);

$this->setTitle('Nastavení hry');