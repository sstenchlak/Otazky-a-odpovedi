<?php
/**
 * Autoloader je funkce, která automaticky načítá neexistující třídy.
 * V tomto případě načte třídy z předem nadefinovaných adresářů pomocí
 * Autoloader::addDir($dir, $scanSubDirectories = TRUE).
 * @author já
 */

class Autoloader
{
	private static $classes = array();

	/**
	 * Na základě adresáře načte všechny třídy. Druhý parametr určuje
	 * zda se mají prozkoumat i podasresáře.
	 */
	public static function addDir($dir, $scanSubDirectories = TRUE)
	{
		$handle = opendir($dir);
		if ( !$handle ) return;

		while ( $entry = readdir($handle) )
		{
			if ( $entry=='.' || $entry=='..' ) continue;
			$entry = $dir.DIRECTORY_SEPARATOR.$entry;
			if ( is_file($entry) )
			{
				$info = pathinfo($entry);
				if ($info['extension'] == 'php')
					self::$classes[basename($entry, '.php')] = $entry;
			}
			else if ( is_dir($entry) )
			{
				self::addDir($entry, $scanSubDirectories);
			}
		}
		closedir($handle);
	}

	/**
	 * Metoda volána při načítání neexistující třídy.
	 */
	public static function load($class)
	{
		if (isset(self::$classes[$class])) {
			include_once ( self::$classes[$class] );
			return true;
		}
	}
}

// Registrace funkce nahoře
	spl_autoload_register('Autoloader::load');

