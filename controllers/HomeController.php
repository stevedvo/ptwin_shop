<?php
	class HomeController
	{
		private $departments_service;
		private $items_service;
		private $orders_service;

		public function __construct()
		{
			$this->departments_service = new DepartmentsService();
			$this->items_service = new ItemsService();
			$this->orders_service = new OrdersService();
		}

		public function Index()
		{
			$order = $this->orders_service->getCurrentOrder();

			$this->orders_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Current Order',
				'template'   => 'views/home/index.php',
				'page_data'  => ['current_order' => $order]
			];

			renderPage($pageData);
		}
	}
