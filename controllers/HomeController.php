<?php
	declare(strict_types=1);

	class HomeController
	{
		private $orders_service;
		private $lists_service;

		public function __construct()
		{
			$this->orders_service = new OrdersService();
			$this->lists_service = new ListsService();
		}

		public function Index() : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$order = $this->orders_service->getCurrentOrder();
				$allLists = $this->lists_service->getAllLists();

				$this->orders_service->closeConnexion();
				$this->lists_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Current Order',
					'template'   => 'views/home/index.php',
					'page_data'  =>
					[
						'current_order' => $order,
						'all_lists'     => $allLists,
					],
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}
	}
