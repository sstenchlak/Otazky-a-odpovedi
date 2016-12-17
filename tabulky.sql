--
-- SQL dotaz pro aplikaci Otázky a odpovědi.
--

DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `duel_columns`;
DROP TABLE IF EXISTS `duel_fields`;

CREATE TABLE IF NOT EXISTS `questions`
	(
		`ID` int AUTO_INCREMENT NOT NULL PRIMARY KEY COMMENT 'Konkrétní ID otázky',
		`question` text NOT NULL COMMENT 'Znění otázky',
		`category` tinytext NOT NULL COMMENT 'Název kategorie, ve které je otázka (čisté jméno)',
		`difficulty` smallint NOT NULL COMMENT 'Číselná obtížnost otázky',
		`result` text COMMENT 'Řešení otázky (odpověď)',
		`used` tinyint(1) NOT NULL COMMENT 'Boolean, zda již byla použita'

	) DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Seznam otázek s odpověďmi, obtížnostmi a kategoriemi.';

CREATE TABLE IF NOT EXISTS `duel_columns`
	(
		`category` tinytext NOT NULL COMMENT 'Název kategorie (viz questions)',
		`order` tinyint NOT NULL UNIQUE KEY COMMENT 'Pořadí , počítáno od nuly'

	) DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Pořadí sloupců (kategorií) ve hře.';

CREATE TABLE IF NOT EXISTS `duel_fields`
	(
		`x` tinyint NOT NULL COMMENT 'Souřadnice políčka',
		`y` tinyint NOT NULL COMMENT 'Souřadnice políčka',
		`question_ID` int NOT NULL COMMENT 'ID jaké otázky bylo vyvoláno',
		`status` tinyint COMMENT 'Status políčka (NULL - otevřené, nemá smysl; 0 - Otázku zodpověděli ti nalevo; 1 - Ti napravo; 2 - Nikdo ji nezodpověděl)',
		UNIQUE KEY `control` (`x`, `y`) COMMENT 'V tabulce by neměly být dva záznamy o stejných políčkách.'
	) DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Otevřená, nebo zodpovězená políčka ve hře se uloží do této tabulky se svým stavem.';
