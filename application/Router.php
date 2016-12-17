<?php
/**
 * Router na základě URL adresy projde indexy ve složce controllers.
 * Ty fungují jako kontrolery, mohou volat nové pohledy, ukládat
 * data a spoustu dalšího.
 * @author itnetwork.cz
 * @author já
 */

class Router
{
	/**
	 * Seznam pohledů, které se postupně vypíšou do stránky.
	 */
	private $views = array();

	/**
	 * Proměnné, které se hromadně předají všem pohledům a budou
	 * ošetřená. Dále zde budou prefixy neošetřené $_variable.
	 */
	private $data = array();

	/**
	 *  Seznam zpráv. Předávají se pomocí SESSION. Když se přesměruje na jinou stránku, zpráva zůstane...
	 *  */
	/** Konstanty pro typy zpráv **/
	const MSG_ERROR = 0;
	const MSG_WARNING = 1;
	const MSG_INFO = 2;
	const MSG_OK = 3;

	/** CSS třídy **/
	protected $MSG_NAMES = array(
		"danger",
		"warning",
		"info",
		"success",
	);


	/**
	 * Metoda ošetří pole s daty pomocí funkce htmlspecialchars().
	 */
	private function osetri ($x=null)
	{
		if (!isset($x))
			return null;
		elseif (is_string($x))
			return htmlspecialchars($x, ENT_QUOTES);
		elseif (is_array($x))
		{
			foreach($x as $k => $v)
			{
				$x[$k] = $this->osetri($v);
			}
			return $x;
		}
		else
			return $x;
	}

	/**
	 * Naparsuje URL adresu podle lomítek a vrátí pole parametrů. Pokud vrátí false,
	 * URL obsahovala nevhodné znaky.
	 */
	private function parsujURL($url, $root)
	{
		// Pokusí odstranit ze začátku root url adresu
		if ($root == substr($url, 0, strlen($root)))
			$url = substr($url, strlen($root));
		else {
			exit('URL adresa by měla začínat podle URL_ROOT!');
			return false;
		}
		// Naparsuje jednotlivé části URL adresy do asociativního pole
		$naparsovanaURL = parse_url($url);
		// Odstranění počátečního lomítka
		$naparsovanaURL["path"] = trim($naparsovanaURL["path"], "/");
		// Ověří správnost URL adresy
		if (!preg_match("/^([a-z_0-9]+\/)*[a-z_0-9]*$/", $naparsovanaURL["path"]))
			return false;
		// Rozbití řetězce podle lomítek
		$rozdelenaCesta = explode("/", $naparsovanaURL["path"]);
		if ($rozdelenaCesta == array('')) $rozdelenaCesta = array();
		return $rozdelenaCesta;
	}

	/**
	 * Naparsuje URL a začne brouzdat po ostatních kontrolerech.
	 */
	public function process($parametry)
	{
// PARSUJE, OVĚŘUJE URL A STARÁ SE O LOGIN A O CHYBY
		// Získá URL v poli, nebo FALSE
		$naparsovanaURL = $this->parsujURL($parametry, URL_ROOT);

		// Adresář s kontrolery, který bude postupně narůstat.
		$dir = DOCUMENT_ROOT . '/controllers';

		// Ověří, že taková stránka existuje
		if ($naparsovanaURL===false OR !file_exists($dir . '/' . implode('/', $naparsovanaURL))){
			$naparsovanaURL = false;
		}

		// Nyní, pokud je $naparsovanaURL===false, stránka neexistuje, jinak určitě existuje a je v $naparsovanaURL

		// Místo false to dá chybovou stránku
		if ($naparsovanaURL===false) {
			$naparsovanaURL = array('404');
		}

// NYNÍ ZAČNE NAČÍTAT KONTROLERY

		// Trik, do URL zadá celý adresář a při procházení se načte i
		// hlavní soubor index.php ve složce controllers.
		// Na začátek přiřadí prázdný string
		array_unshift($naparsovanaURL, '');

		// Načítá podsoubory v kontrolerech
		foreach ($naparsovanaURL as $value) {
			$dir .= '/' . $value;
			if (file_exists($dir . '/index.php'))
				include_once($dir . '/index.php');
		}

		// Načte koncovou stránku
		if (file_exists($dir . '/page.php'))
			include_once($dir . '/page.php');
	}

	/**
	 * Otevře pouze jeden kontroler, který se zvolí.
	 */
	public function open($file)
	{
		return include_once( $file );
	}

	/**
	 * Zaregistruje pohled a s ním i data pro celou stránku.
	 * @param $view Název pohledu ze složky views
	 */
	private function addView($view, $data = array())
	{
		$this->views[] = $view;
		$this->data = array_merge($this->data, $data);
	}

	/**
	 * Setter, který nastaví titulek HTML stránce.
	 */
	public function setTitle($title)
	{
		$this->data['title'] = $title;
	}

	/**
	 * Vypíše pohled. Je volán jak hlavní stránkou /index.php, tak i jednotlivými
	 * pohledy na vypsání dalšího pohledu.
	 */
	public function run($try=false)
	{
		extract($this->osetri($this->data)); // Ošetřená data
		extract($this->data, EXTR_PREFIX_ALL, ""); // Neošetřená data s prefixem "_"

		if (!$this->views)
			if($try)
				return false;
			else
				trigger_error("Pokus o vypsání pohledu, který se v zásobníku nenachází!");

		include_once(DOCUMENT_ROOT . '/views/' . array_shift($this->views) . '.phtml');
	}

	/**
	 *  Metoda volána při vykreslování stránky - vypíše zprávy ze session
	 *  */
	protected function getMessages ()
	{
		if (isset($_SESSION['zpravy']))
		{
			$x = $_SESSION['zpravy'];
			unset($_SESSION['zpravy']);
			return $x;
		} else {
			return array();
		}
	}

	/**
	 * Přidá zprávu do session a vypíše KDYKOLIV později.
	 */
	public static function addMessage ( $zprava, $type )
	{
		if (isset($_SESSION['zpravy']))
			$_SESSION['zpravy'][] = array($zprava, $type);
		else
			$_SESSION['zpravy'] = array( array($zprava, $type) );
	}

	/**
	 * Metody vypíšou zprávy do dokumentu jako text.
	 */
	protected function echoMessages ()
	{
		foreach ($this->getMessages() as $zprava)
			$this->echoMessage ( $zprava[0], $zprava[1] );
	}

	protected function echoMessage ( $msg, $type )
	{
		echo "\t<div class=\"alert alert-" . $this->MSG_NAMES[$type] . "\" role=\"alert\">$msg</div>\n";
	}

	/**
	  * Přesměruje na dané URL (na začátku by mělo být lomítko!)
	  */
	public static function redirect($url=null)
	{
		if ($url) {
			header("Location: " . URL_ROOT . $url);
		} else {
			header("Location: " . $_SERVER['REQUEST_URI']);
		}
		header("Connection: close");
        exit;
	}
}
