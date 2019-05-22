<?php
	class HomeController
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
			$order = $this->orders_service->getCurrentOrder();
			$dalResult = $this->lists_service->getAllLists();

			$all_lists = $dalResult->getResult() ?: null;

			$this->orders_service->closeConnexion();
			$this->lists_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Current Order',
				'template'   => 'views/home/index.php',
				'page_data'  =>
				[
					'current_order' => $order,
					'all_lists'     => $all_lists
				]
			];

			renderPage($pageData);
		}
	}
