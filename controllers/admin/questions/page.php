<?php
/**
 * Hlavní strana pro správu otázek
 */

// Data, která se odešlou šabloně
	$data = array();

// Naplní se otázkama
	$data['questions'] = questions::getStatus();
	$data['questions_list'] = questions::getAllQuestions();

// Registrace pohledu
	$this->addView('adminQuestions', $data);

$this->setTitle('Seznam otázek');