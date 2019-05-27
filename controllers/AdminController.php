<?php
	class AdminController
	{
		private $orders_service;
		private $lists_service;

		public function __construct()
		{
			$this->orders_service = new OrdersService();
			$this->lists_service = new ListsService();
		}

		public function Index()
		{
			$pageData =
			[
				'page_title' => 'Admin',
				'template'   => 'views/admin/index.php',
				'page_data'  => []
			];

			renderPage($pageData);
		}
	}
