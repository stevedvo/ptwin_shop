<?php
	declare(strict_types=1);

	class PackSizesController
	{
		private $packsizes_service;

		public function __construct()
		{
			$this->packsizes_service = new PackSizesService();
		}

		public function Index() : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				$packSizePrototype = new PackSize();
				$packSizes = $this->packsizes_service->getAllPackSizes();

				$this->packsizes_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Pack Sizes',
					'template'   => 'views/packsizes/index.php',
					'page_data'  =>
					[
						'packsize_prototype' => $packSizePrototype,
						'packsizes'          => $packSizes
					]
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function addPackSize($request)
		{
			$packsize = createPackSize($request);

			if (!entityIsValid($packsize))
			{
				return false;
			}

			$dalResult = $this->packsizes_service->getPackSizeByName($packsize->getName());

			if (!is_null($dalResult->getException()))
			{
				return $dalResult;
			}

			if ($dalResult->getResult() instanceof PackSize)
			{
				return false;
			}

			$dalResult = $this->packsizes_service->getPackSizeByShortName($packsize->getShortName());

			if (!is_null($dalResult->getException()))
			{
				return $dalResult->jsonSerialize();
			}

			if ($dalResult->getResult() instanceof PackSize)
			{
				return false;
			}

			$dalResult = $this->packsizes_service->addPackSize($packsize);

			if (!is_null($dalResult->getResult()))
			{
				$packsize = $dalResult->getResult();
				$dalResult->setPartialView(getPartialView("PackSizeListItem", ['item' => $packsize]));
			}

			$this->packsizes_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null)
		{
			$packsize = $all_packsizes = $all_items = false;

			if (is_numeric($request))
			{
				$dalResult = $this->packsizes_service->getPackSizeById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$packsize = $dalResult->getResult();
				}
			}

			$this->packsizes_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Pack Size',
				'breadcrumb' =>
				[
					[
						'link' => '/packsizes/',
						'text' => 'Pack Sizes'
					],
					[
						'text' => 'Edit'
					]
				],
				'template'   => 'views/packsizes/edit.php',
				'page_data'  =>
				[
					'packsize'      => $packsize,
					'all_packsizes' => $all_packsizes,
					'all_items'     => $all_items
				]
			];

			renderPage($pageData);
		}

		public function editPackSize($request)
		{
			$packsize_update = createPackSize($request);

			if (!entityIsValid($packsize_update))
			{
				return false;
			}

			$packsize = $this->packsizes_service->verifyPackSizeRequest($request);

			if (!$packsize)
			{
				return false;
			}

			$packsize->setName($packsize_update->getName());
			$packsize->setShortName($packsize_update->getShortName());

			$dalResult = $this->packsizes_service->updatePackSize($packsize);
			$this->packsizes_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removePackSize($request)
		{
			if (!isset($request['packsize_id']) || !is_numeric($request['packsize_id']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemsByPackSizeId(intval($request['packsize_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$items = $dalResult->getResult();

			if (is_array($items) && sizeof($items) > 0)
			{
				return false;
			}

			$dalResult = $this->packsizes_service->getPackSizeById(intval($request['packsize_id']));

			if (!is_null($dalResult->getResult()))
			{
				$packsize = $dalResult->getResult();
			}

			if (!$packsize)
			{
				return false;
			}

			$dalResult = $this->packsizes_service->removePackSize($packsize);
			$this->packsizes_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
