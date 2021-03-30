<?php
	declare(strict_types=1);

	class ShopDb
	{
		private $host = DB_HOST;
		private $username = DB_USER;
		private $password = DB_PASSWORD;
		private $database = DB_NAME;
		public $conn;

		public function __construct(?string $host = null, ?string $username = null, ?string $password = null, ?string $database = null)
		{
			if (!is_null($host) && !is_null($username) && !is_null($password) && !is_null($database))
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
