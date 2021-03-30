<?php
	declare(strict_types=1);

	class AdminController
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
				$pageData =
				[
					'page_title' => 'Admin',
					'template'   => 'views/admin/index.php',
					'page_data'  => [],
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
