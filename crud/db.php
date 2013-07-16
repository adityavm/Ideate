<?

error_reporting(E_ERROR | E_PARSE);

/**
* Basic class to handle CRUD
*/
class DB extends mysqli {
	public $username;
	public $password;
	public $database;

	function __construct(){
		$config = file_get_contents("./tb/crud/config.json"); // not ideas page
		if(!$config)
			$config = file_get_contents("../crud/config.json");

		$CONFIG = json_decode($config, true);

		$this->username = $CONFIG['db_user'];
		$this->password = $CONFIG['db_pass'];
		$this->database = $CONFIG['db_db'];

		parent::__construct('localhost', $this->username, $this->password, $this->database);
		if(mysqli_connect_error())
			die("Unconnected Database. (" . mysqli_connect_errno() . " - " . mysqli_connect_error() . ")");
		$this->set_charset("utf8");
	}

	function _query($str){
		$res = parent::query($str);
		if($res)
			return $res->fetch_assoc();
	}
}

?>
