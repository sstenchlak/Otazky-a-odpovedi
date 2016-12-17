<?php
/**
 * Třída starající se o nastavení. Má určité výchozí a poté čerpá z databázové tabulky...
 */
class Setting
{
	/**
	 * Výchozí nastavení. Zíroveň klíče jsou předlohou pro to, jaké nastavení může existovat.
	 */
	private static $defaultSetting = array(
		'columns' => 10, // Výchozí počet sloupců (0 - všechny)
		'max_same_categories' => 2, // Maximální počet zobrazení stejné kategorie ve hře (0 je vše)
	);

	/**
	 * Nastavení, které bylo získáno z databáze k aktuální hře, která v rámci relace může být jen jedna (je nesmysl, aby se hra uprostřed PHP skriptu změnila..)
	 * Pokud nebylo ještě z db získáno, tak je null.
	 */
	private static $dbSetting = null;

	/**
	 * Vrátí všechno nastavení k probíhající hře.
	 * @final
	 */
	public static function getSetting()
	{
		if (self::$dbSetting) {
			return self::$dbSetting;
		}

		$data = DB::queryAll('SELECT * FROM `setting`');

		$setting = array();
		foreach ($data as $value) {
			$setting[ $value['option'] ] = $value['value'];
		}

		self::$dbSetting = self::$defaultSetting;

		// Uloží nastavení do dbSetting
		foreach (self::$dbSetting as $key => &$value) {
			if ( isset($setting[$key]) ) {
				$value = $setting[$key];
			}
		}

		// Rekurze
		return self::getSetting();
	}

	/**
	 * Nastaví nové nastavení.
	 * @final
	 */
	public static function setSetting($setting)
	{
		// Profiltruje pole, aby zůstaly jen hodnoty, které mají smysl a existují
		$setting = array_intersect_key($setting, array_flip(self::$defaultSetting));

		$toSet = array(); // Hodnoty na uložení

		foreach ($setting as $option => $value) {
			if ( $value != self::$defaultSetting ) {
				$toSet[] = array(
					'option' => $option,
					'value' => $value,
				);
			}
		}

		// Vše smaže z databáze a uloží tam nové hodnoty, ty, které jsou v rozporu s defaultním nastavením
		DB::queryAll('DELETE FROM `setting`');
		DB::multiInsert('setting', $toSet);

		// Uloží do nastavení (nevadí, že v $setting je něco navíc, vždyť je to default)
		self::$dbSetting = array_merge( self::$defaultSetting, $setting );
	}

}
