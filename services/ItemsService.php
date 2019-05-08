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

		public function getAllItems()
		{
			return $this->dal->getAllItems();
		}

		public function getItemById($item_id)
		{
			return $this->dal->getItemById($item_id);
		}
	}
