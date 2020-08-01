<?php
	declare(strict_types=1);

	class LuckyDipsController
	{
		private $luckyDips_service;
		private $items_service;

		public function __construct()
		{
			$this->luckyDips_service = new LuckyDipsService();
			$this->items_service = new ItemsService();
		}

		public function Index() : void
		{
			$luckyDipPrototype = new LuckyDip();
			$dalResult = $this->luckyDips_service->getAllLuckyDips();
			$luckyDips = false;

			if (!is_null($dalResult->getResult()))
			{
				$luckyDips = $dalResult->getResult();
			}

			$this->luckyDips_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Manage Lucky Dips',
				'template'   => 'views/luckyDips/index.php',
				'page_data'  =>
				[
					'luckyDipPrototype' => $luckyDipPrototype,
					'luckyDips'         => $luckyDips
				]
			];

			renderPage($pageData);
		}

		public function addLuckyDip($request)
		{
			$luckyDip = createLuckyDip($request);

			if (!entityIsValid($luckyDip))
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->getLuckyDipByName($luckyDip->getName());

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if ($dalResult->getResult() instanceof LuckyDip)
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->addLuckyDip($luckyDip);

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			if (!is_null($dalResult->getResult()))
			{
				$luckyDip->setId($dalResult->getResult());
				$dalResult->setPartialView(getPartialView("LuckyDipListItem", ['item' => $luckyDip]));
			}

			$this->luckyDips_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null) : void
		{
			$luckyDip = $all_items = null;

			if (is_numeric($request))
			{
				$dalResult = $this->luckyDips_service->getLuckyDipById(intval($request));

				if (!is_null($dalResult->getResult()))
				{
					$luckyDip = $dalResult->getResult();
				}

				$dalResult = $this->items_service->getAllItems();

				if (!is_null($dalResult->getResult()))
				{
					$all_items = $dalResult->getResult();
				}
			}

			$this->luckyDips_service->closeConnexion();
			$this->items_service->closeConnexion();

			$pageData =
			[
				'page_title' => 'Edit Lucky Dip',
				'breadcrumb' =>
				[
					[
						'link' => '/luckydips/',
						'text' => 'Lucky Dips'
					],
					[
						'text' => 'Edit'
					]
				],
				'template'   => 'views/luckyDips/edit.php',
				'page_data'  =>
				[
					'luckyDip'  => $luckyDip,
					'all_items' => $all_items
				]
			];

			renderPage($pageData);
		}

		public function addItemToLuckyDip($request)
		{
			$item = $this->items_service->verifyItemRequest($request);
			$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);

			if (!($item instanceof Item) || !($luckyDip instanceof LuckyDip))
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->addItemToLuckyDip($item, $luckyDip);

			if (!is_null($dalResult->getResult()))
			{
				$dalResult->setPartialView(getPartialView("LuckyDipItem", ['item' => $item]));
			}

			$this->luckyDips_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeItemFromLuckyDip($request)
		{
			$item = $this->items_service->verifyItemRequest($request);
			$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);

			if (!($item instanceof Item) || !($luckyDip instanceof LuckyDip))
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->removeItemFromLuckyDip($item, $luckyDip);

			$this->luckyDips_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editLuckyDip($request)
		{
			$luckyDip = createLuckyDip($request);

			if (!entityIsValid($luckyDip))
			{
				return false;
			}

			$luckyDipUpdate = $this->luckyDips_service->verifyLuckyDipRequest($request);

			if (is_null($luckyDipUpdate))
			{
				return false;
			}

			$luckyDipUpdate->setName($luckyDip->getName());

			$dalResult = $this->luckyDips_service->updateLuckyDip($luckyDipUpdate);
			$this->luckyDips_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeLuckyDip($request)
		{
			if (!isset($request['dept_id']) || !is_numeric($request['dept_id']))
			{
				return false;
			}

			$dalResult = $this->items_service->getItemsByLuckyDipId(intval($request['dept_id']));

			if (!is_null($dalResult->getException()))
			{
				return false;
			}

			$items = $dalResult->getResult();

			if (is_array($items) && sizeof($items) > 0)
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->getLuckyDipById(intval($request['dept_id']));

			if (!is_null($dalResult->getResult()))
			{
				$luckyDip = $dalResult->getResult();
			}

			if (!$luckyDip)
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->removeLuckyDip($luckyDip);
			$this->luckyDips_service->closeConnexion();
			$this->items_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}
	}
