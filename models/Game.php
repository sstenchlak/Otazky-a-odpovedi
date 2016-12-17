<?php
/**
 * Třída fungující jako správce hry.
 */

class Game
{

	/**
	 * Pro pomocnou metodu sezna sloupců.
	 */
	private static $columns = null;

	/**
	 * Metoda vyplivne v poli všechny možné sloupce, se kterými je
	 * možno počítat ve hře, neboť obsahují všechny otázky. Může
	 * nastat případ, že se zobrazí vícekrát, neboť otázek bude hodně.
	 * @return array of 2 arrays Kategorie po jedné, zbytek...
	 */
	public static function getAvailableCategories()
	{
		// Získá otázky, které nebyly použity
		$questions = DB::queryAll('SELECT * FROM `questions` WHERE `used` = 0');

		// Převede otázky do pole podle kategorií
		$categories = array();
		foreach ($questions as $question) {
			if (!isset($categories[ $question['category'] ]))
				$categories[ $question['category'] ] = array($question);
			else
				$categories[ $question['category'] ][] = $question;
		}

		// Vytvoří pole obsahující jako klíče kategorie a hodnoty jsou počet možných sloupců z tohoto pole.
		$avaiableCategories = array();
		foreach ($categories as $categoryName => $questions) {
			$minCount = null;
			foreach (questions::$difficulty as $value) {
				$count = 0;
				foreach ($questions as $question) {
					if ($question['difficulty'] == $value) $count++;
				}
				if ($minCount === null OR $count < $minCount)
					$minCount = $count;
			}
			$avaiableCategories[$categoryName] = $minCount;
		}

		// Omezení počtu stejných sloupců
		$finalResidue = array();
		$finalUnique = array();
			foreach ($avaiableCategories as $category => &$value) {
				for ($i=0; $i < $value; $i++) {
					if ($i)
						$finalResidue[] = $category;
					else
						$finalUnique[] = $category;
				}
			}

		// Navrátí hodnoty. Pole, kde první hodnota je pole s unikátními sloupci (max 1 svého druhu)
		// Dhuhá hodnota obsahuje zbytek. Ty dvě pole jsou de fakto výsledek rozdělený na dvě části.
		return array($finalUnique, $finalResidue);
	}

	/**
	 * Metoda vrátí hru, pokud je otevřená otázka, tak vrátí otázku..
	 */
	public static function getGame()
	{
		$fields = DB::queryAll("SELECT * FROM `duel_fields` ORDER BY `status`");

		// Ověření, zda existuje hra
		if (!$fields AND !DB::querySingle("SELECT COUNT(*) FROM `duel_columns`")) {
			return false;
		}

		// Pokud je otevřená otázka, měl by mít první záznam status===null

		if (isset($fields[0]) AND $fields[0]['status'] === null) {
			// Je otevřena otázka

			$ID = $fields[0]['question_ID'];
			$question = questions::getQuestion($ID);

			return array(
				'status' => 'question',
				'question' => $question,
			);
		} else {
			// Je otevřena herní plocha

			$cells = array();
			foreach ($fields as $value)
				$cells[] = array($value['x'], $value['y']);

			$lScore = $rScore = 0;
			foreach ($fields as $value) {
				if ($value['status'] === 0) {
					$lScore += questions::$difficulty[$value['y']];
				}
				if ($value['status'] === 1) {
					$rScore += questions::$difficulty[$value['y']];
				}
			}

			return array(
				'status' => 'gameBoard',
				'columns' => self::getColumns(),
				'disabledCells' => $cells,
				'lScore' => $lScore,
				'rScore' => $rScore,
			);
		}
	}

	/**
	 * Metoda zjistí, zda probíhá hra, čistě pro informační účely.
	 * @return bool Probíhá hra?
	 */
	public static function isGame()
	{
		return (bool)DB::querySingle("SELECT COUNT(*) FROM `duel_columns`");
	}

	/**
	 * Metoda ukončí hru, tedy smaže tabulky. Použití jak vnitřní, tak i vnější.
	 */
	public static function endGame()
	{
		DB::query('TRUNCATE TABLE `duel_columns`'); // Pro více her se musí použít DELETE FROM
		DB::query('TRUNCATE TABLE `duel_fields`'); // Pro více her se musí použít DELETE FROM
	}

	/**
	 * Otevře novou herní otázku.
	 */
	public static function openQuestion($x, $y)
	{
		$ID = questions::getGameQuestionID(self::getColumns()[$x], questions::$difficulty[$y]);
		DB::query("INSERT INTO `duel_fields` SET `x` = ?, `y` = ?, `question_ID` = ?", array($x, $y, $ID));
	}

	/**
	 * Znovu otevře již zavřenou otázku
	 */
	public static function reopenQuestion($x, $y)
	{
		DB::query("UPDATE `duel_fields` SET `status` = NULL WHERE `x` = ? AND `y` = ?", array($x, $y));
	}

	/**
	 * Zavře herní otázku
	 */
	public static function closeQuestion($team)
	{
		DB::query("UPDATE `duel_fields` SET `status` = ? WHERE `status` IS NULL", array($team));
	}

	/**
	 * Pomocná metoda, vrátí herní kategorie.
	 */
	private static function getColumns()
	{
		if (self::$columns !== null)
			return self::$columns;

		$columnsDB = DB::queryAll("SELECT * FROM `duel_columns`"); // ORDER BY `order`
		$columns = array();
		foreach ($columnsDB as $value)
			$columns[] = $value['category'];

		return self::$columns = $columns;
	}

}
