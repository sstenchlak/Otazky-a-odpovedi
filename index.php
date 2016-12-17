<?php
/**
 * Hra Otázky a odpovědi pro dva týmy, nebo hráče, jejich úkolem
 * je odpovídat na otázky, které si mohou zvolit z herního plánu.
 * Na něm se nacházejí tlačítka umístěna pod herními kategoriemi
 * s různými obtížnostmi.
 *
 * Administrace programu obsahuje rozhraní, kde se dají vkládat,
 * nebo importovat otázky a snadno volit herní kategorie.
 *
 * Kompatabilní s PHP verzí 5.4.24, MySQL verzí 5.6.15 a Apache
 * 2.4.7. Aplikace vyžaduje databázi (nastavení v tomto souboru,
 * dotaz na vytvoření tabulek přiložen v souboru hra.sql)
 *
 * @name Otázky a odpovědi
 * @version 1.0.3 Final
 * @author Štěpán Stenchlák <s.stenchlak@gmail.com>
 * @date 2015/05/01 Modified 2015/12/20
 */

/*
	1.0.3	2015/12/20
		Menší změny vzhledu v administraci.
		Prostředí hry je černé, přizpůsobené na projektor.

	1.0.2	2015/12/05
		Funkce na import dat z excelu se spustí až po stisknutí zeleného
			tlačítka. Mnohonásobná optimalizace.
 */

/**
 * Na soubor index.php jsou směřovány všechny URL adresy, které
 * nemají fyzické zastaoupení na serveru, tedy všchny požadavky
 * kromě css sourorů, js skriptů a obrázků.
 */

/**
 * Definice pracovního adresáře podle umístění tohoto souboru.
 */
	define('DOCUMENT_ROOT', getcwd());

/**
 * Definice pracovní URL adresy. Určuje hlavní webovou stránku (adresář)
 * bez posledního lomítka. Pokud se tedy aplikace nahraje do zákldního
 * adresáře, bude URL_ROOT == '', pokud bude ve složce, tak '/slozka'
 */
	define('URL_ROOT', ''); // Hlavní adresář je hlavní stránka webu

// Načtení souboru se skripty pro přizpůsobení php
	require ('application/system.php');

/**
 * Připojení k databázi
 * @param string Hostitel
 * @param string Uživatelské jméno
 * @param string Heslo
 * @param string Název databáze
 */
	DB::connect('localhost', 'root', '', 'qaa');

// Před zavolání routeru je třeba ověřit, zda se neodeslal požadavek POST,
// který by měl být vyřizován pouze javascriptem
	$_POST AND require_once('application/post.php');

// Vytvoření routeru
	$Router = new Router();

// Zpracování
	$Router->process( $_SERVER['REQUEST_URI'] );

// Vykreslení (pokus TRUE)
	$Router->run(true);
