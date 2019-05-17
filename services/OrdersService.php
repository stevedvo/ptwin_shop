<?php
	class OrdersService
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

		public function getCurrentOrder()
		{
			$this->dal->getCurrentOrder();
		}
	}
