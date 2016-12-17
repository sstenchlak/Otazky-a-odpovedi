<?php
// Data, která se odešlou šabloně
	$data = array();

// Naplní se otázkama
	$data['category_list'] = questions::getCategories();

$this->addView('adminQuestionsCategories', $data);
$this->setTitle('Seznam herních kategorií');