<?php
	class ItemsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new ShopDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		function getAllItems()
		{
			return $this->dal->getAllItems();
		}
	}
