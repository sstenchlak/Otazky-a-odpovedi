<?php
/**
 * Třída fungující jako správce otázek. Očekáváno statické zacházení.
 */

/*
CREATE TABLE `questions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `category` tinytext NOT NULL,
  `difficulty` smallint(6) NOT NULL,
  `result` text,
  `used` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
*/

class questions
{
	/**
	 * Popisky k veřejným sloupcům v databázi. Vychází se z nich při importu a dalších věcech.
	 * Pozor, při přidávání, nebo změně polí zkontroluj @see self::processQuestion
	 */
	public static $columns = array(
		'question' => array(
			'title' => "Znění otázky",
			'description' => "Znění samotné otázky.",
			'type' => 'textarea',
			'setDefault' => false, // Zda se hodnota z praktického hlediska nastavit jako defaultní pro většinu otázek
			'empty' => false, // Zda může být prázdná hodnota
		),
		'category' => array(
			'title' => "Kategorie otázky",
			'description' => "Každá otázka se týká určitého tématu, což je kategorie. Pokud špatně zadáváte názvy kategorií, lze je hromadně upravit.",
			'type' => 'text',
			'setDefault' => true,
			'empty' => false, // Zda může být prázdná hodnota
		),
		'difficulty' => array(
			'title' => "Obtížnost otázky",
			'description' => "Číselná hodnota udávající obtížnost otázky v dané kategorii. Otázky jsou poté řazeny dle obtížnosti. V případě, že jedna kategorie má více otázek se stejnou kategorií, vybere se jen jedna. V případě, že otázka s obtížnosti chybí v dané kategorii, nebude vůbec v soutěži.", /* + PHP na konci souboru. */
			'type' => 'number',
			'setDefault' => true,
			'empty' => false, // Zda může být prázdná hodnota
		),
		'result' => array(
			'title' => "Správná odpověď",
			'description' => "Správné znění odpovědi na otázku z možným komentářem k řešení.",
			'type' => 'textarea',
			'setDefault' => false,
			'empty' => true, // Zda může být prázdná hodnota
		),
		'used' => array(
			'title' => "Již použita v soutěži?",
			'description' => "Udává, zda již byla daná otázka použita v soutěži (ano - byla; ne - nebyla). Pokud ano, již zobrazena nebude, dokud ji znova neobnovíte. Při vkládání otázky proto zvolte možnost NE.",
			'type' => 'checkbox',
			'setDefault' => true,
			'empty' => false, // Zda může být prázdná hodnota
		),
	);

	/**
	 * Další sloupce, ale ty se normálně nepouživají.
	 */
	public static $hideColumns = array(
		'ID' => array(
			'title' => "#",
			'description' => "Pořadové číslo otázky pro vnitřní potřeby hry. Nedá se měnit.",
		)
	);

	/**
	 * Seznam možných obtížností otázek. Podle něj se validují otázky.
	 * Pokud se upraví, otázky s bodovým počtem mimo se ŠPATNĚ nezobrazí, bude je třeba upravit...
	 */
	public static $difficulty = array(10,20,30,40,50);

	/**
	 * Vratí ID otázky a označí ji za použitou.
	 */
	public static function getGameQuestionID($category, $difficulty)
	{
		$ID = DB::querySingle("SELECT `ID` FROM `questions` WHERE `category` = ? AND `difficulty` = ? AND `used` = 0 ORDER BY RAND() LIMIT 0,1", array($category, $difficulty));
		DB::query("UPDATE `questions` SET `used` = 1 WHERE `ID` = ?", array($ID));
		return $ID;
	}





	/**
	 * Podá informace o počtu otázek. Použito na indexu.
	 * @return array
	 */
	public static function getStatus()
	{
		// Získám požadované informace z databáze
		$table = DB::queryAll('SELECT questions.used, COUNT(*) AS `count` FROM `questions` GROUP BY questions.used');

		$used = 0;
		$notUsed = 0;

		// projdu informace z databáze
		foreach ($table as $line) {
			if ($line['used'])
				$used = (int) $line['count'];
			else
				$notUsed = (int) $line['count'];
		}

		return array(
			'used' => $used,
			'notUsed' => $notUsed,
			'total' => $used + $notUsed,
		);
	}

	/**
	 * Vrátí všechny otázky.
	 * @return array
	 */
	public static function getAllQuestions($where=null)
	{
		return DB::queryAll('SELECT * FROM `questions` ');
	}

	/**
	 * Vrátí jednu otázku.
	 * @param int $ID
	 * @return array Otázka
	 */
	public static function getQuestion($ID)
	{
		return DB::queryOne('SELECT * FROM `questions` WHERE `ID` = ? LIMIT 0,1', array($ID));
	}

	/**
	 * Metoda vrátí seznam kategorií společne s počtem otázek
	 * @return array
	 */
	public static function getCategories()
	{
		return DB::queryAll('SELECT `category`, COUNT(*) AS \'count\' FROM `questions` GROUP BY `category`');
	}





	/**
	 * Vloží jednu nebo více otázek..
	 * @param array $questions Data pro vkládání otázek ve formě pole, nebo multidimenzionálního.
	 * 	Mohou být neošetřená a dokonce obsahovat i něco navíc. Vhodné použít přímo z $_POST
	 * @param @return null|array $err Seznam chyb při operaci ve formě pole.
	 * @return false|int Počet editovaných řádků.
	 */
	public static function addQuestions($questions, &$err)
	{
		// Předělá do multidimenzionálního pole
		if (count($questions) == count($questions, COUNT_RECURSIVE))
			$questions = array($questions);

		// Najde chyby a zvaliduje otázky
		$err = array();
		foreach ($questions as $key => &$oneQuestion) {
			// Otázka se ošetří
			$oneQuestion = self::processQuestion($oneQuestion, $checks);

			if (!$oneQuestion) {
				$err[$key] = $checks;
			}
		}

		// V případš, že byla chyba
		if ($err) return FALSE;

		// Pokud ne, vloží se dotaz
		return DB::multiInsert('questions', $questions);
	}





	/**
	 * Updatne otázky, data se vkládají neošetřená.
	 * @param array $values Data pro vkládání otázek ve formě pole.
	 * 	Mohou být neošetřená a dokonce obsahovat i něco navíc. Vhodné použít přímo z $_POST
	 * @param null|array $where Seznam s ID, které otázky se mají upravit.
	 * @param @return null|array $err Seznam chyb při operaci ve formě pole.
	 * @return false|int Počet editovaných řádků.
	 */
	public static function update($values = array(), $where=null, &$err)
	{
		// Ověří se správnost dat
			$values = questions::processQuestion($values, $err, array_keys($values));

		if (!$values) return FALSE;

		// Uloží se
			return questions::setValues($values, $where);
	}

	/**
	 * Metoda speciálně aktualizuje název kategorie u jiných kategorií. Jsou zde vyjímky
	 * @param string $name Název nové kategorie, neošetřený.
	 * @param null|array $category_names Názvy všech kategorií k přepsání
	 * @param @return null|array $err Seznam chyb při operaci ve formě pole.
	 * @return false|int Počet editovaných řádků.
	 */
	public static function updateCategories ($name, $category_names=null, &$err)
	{
		// Ověří se správnost dat
			$values = questions::processQuestion(array('category'=>$name), $err, array('category'));

		if (!$values) return FALSE;

		// Uloží se
			return questions::setValues($values, $category_names, 'category');

	}

	/**
	 * Metoda nastaví použitelnost otázek.
	 * @param bool $used Nastavit, zda už otázka byla použita (true), nebo ještě ne.
	 * @param null|array $where Seznam s ID, které otázky se mají upravit.
	 * @return false|int Počet editovaných řádků.
	 */
	public static function setUsed($used=false, $where = null)
	{
		return self::setValues(array('used'=>(bool)$used), $where);
	}





	/**
	 * Smaže otázky.
	 * @param null|array $where Seznam s ID, které otázky se mají odstranit.
	 * @return false|int Počet smazaných řádků.
	 */
	public static function delete($where = null)
	{
		$condition = '';

		if ($where != null)
			$condition = "WHERE questions.ID IN (" . str_repeat('?,', sizeOf($where)-1) . "?)";

		return DB::query('DELETE FROM questions ' . $condition, $where==null ? array() : $where);
	}





	/**
	 * Metoda dostane pole a zněj si získá data pro otázky, které
	 * upraví, natrimuje a vrátí čistý výsledek. V případě chyby
	 * FALSE, potom vrátí do $err..
	 * @param array $question_input JEDNA otázka jako pole, neošetřená data, mohou zde být i data navíc.
	 * @param @return null|array $err Seznam chyb při operaci ve formě pole.
	 * @param null|array $customColumns Lze ověřit jen některé sloupce, ne všechny. Vhodné při updatu
	 * @return array|FALSE Navrátí upravenou otázku, nebo FALSE
	 */
	private static function processQuestion($question_input, &$err, $customColumns = null)
	{
		if ($customColumns) {
			$listOfColumns = array();
			foreach ($customColumns as $value) {
				@$listOfColumns[$value] = self::$columns[$value];
			}
		} else {
			$listOfColumns = self::$columns;
		}

		$err = array();


		// Nejprve získám všechny sloupce bez ohledu na to, zda něco bylo vyplněno
		$questions = array();
		foreach ($listOfColumns as $colName => $col) {
			@$questions[$colName] = $question_input[$colName];
			// Úprava dat
				$questions[$colName] = trim($a = $questions[$colName]);
				$questions[$colName] = str_replace("\r", '', $questions[$colName]);
			if ( !$col['empty'] AND (!isset($question_input[$colName]) OR $questions[$colName] === '') )
				$err[] = "Pole '" . $col['title'] . "' nic neobsahuje!";
		}

		// V poli $questions jsou skoro ošetřené data otázky, jen některé, záleží na $customColumns

		// Zpracování used
		if (isset($listOfColumns['used'])) { // Pokud se má validovat tento sloupëc
			$questions['used'] = strtr($questions['used'], array('ano'=>1, 'yes'=>1, 'ne'=>0, 'no'=>0));
			if ($questions['used'] != 0 AND $questions['used'] != 1)
				$err[] = "Pole '" . self::$columns['used']['title'] . "' je špatně vyplněné!";
		}

		// Zpracování difficulty
		if (isset($listOfColumns['difficulty']))
			if (!in_array( $questions['difficulty'], self::$difficulty))
				$err[] = "Pole '" . self::$columns['difficulty']['title'] . "' je špatně vyplněné! Smí obsahovat pouze hodnoty " . implode(', ', self::$difficulty) . ".";


		return $err ? FALSE : $questions;
	}


	/**
	 * Metoda updatne otázky pomocí určitých pravidel.
	 * @param array $values Hodnoty a klíče, které se aktualizují, již musí být validní!
	 * @param null|array $where Možnost aktualizovat jen některé otázky, jejíž $compareColumn je v tomto poli.
	 * @param string $compareColumn Podle jakého sloupce se bude updatovat.
	 * @return false|int Počet editovaných řádků.
	 */
	private static function setValues($values = array(), $where=null, $compareColumn='ID')
	{
		$condition = '';

		if ($where != null)
			$condition = "WHERE questions.$compareColumn IN (" . str_repeat('?,', sizeOf($where)-1) . "?)";

		return DB::update('questions', $values, $condition, $where==null ? array() : $where);
	}
}

questions::$columns['difficulty']['description'] .= " Kategorie jsou " . implode(", ", questions::$difficulty) . ".";

