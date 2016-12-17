<?php
/**
 * Hlavní soubor starajícíse o základní věci ohledně php.
 * @author já
 */

// Nastavení kódování
	header('Content-Type: text/html; charset=utf-8');
	mb_internal_encoding("utf-8");
	SetLocale(LC_TIME, "cs_CZ");

// Zapnutí session pro použití se zprávami
	session_start();

// Načtení souboru obsahující registraci autoloadu
	require_once ( "autoload.php" );
	Autoloader::addDir(DOCUMENT_ROOT . '/models/');
	Autoloader::addDir(DOCUMENT_ROOT . '/application/');
