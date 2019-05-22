<?php
	class ShopDb
	{
		private $host = DB_HOST;
		private $username = DB_USER;
		private $password = DB_PASSWORD;
		private $database = DB_NAME;
		public $conn;

		public function __construct($host = false, $username = false, $password = false, $database = false)
		{
			if ($host !== false && $username !== false && $password !== false && $database !== false)
			{
				$this->host = $host;
				$this->username = $username;
				$this->password = $password;
				$this->database = $database;
			}

			try
			{
				$dsn = 'mysql:host='.$this->host.';dbname='.$this->database;

				$this->conn = new PDO($dsn, $this->username, $this->password);

				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$this->conn->exec("set names utf8");
			}
			catch(PDOException $e)
			{
				echo 'Connection failed '.$e->getMessage();
			}
		}
	}
