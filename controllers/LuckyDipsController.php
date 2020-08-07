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
			$this->lists_service = new ListsService();
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

				$luckyDip = $dalResult->getResult();

				if (!is_null($luckyDip))
				{
					$dalResult = $this->items_service->getAllItemsNotInLuckyDip($luckyDip->getId());
					$all_items = $dalResult->getResult();

					$dalResult = $this->lists_service->getAllLists();
					$lists = $dalResult->getResult();
				}
			}

			$this->luckyDips_service->closeConnexion();
			$this->items_service->closeConnexion();
			$this->lists_service->closeConnexion();

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
					'all_items' => $all_items,
					'lists'     => $lists
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
			$luckyDipUpdate->setListId($luckyDip->getListId());

			$dalResult = $this->luckyDips_service->updateLuckyDip($luckyDipUpdate);
			$this->luckyDips_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function removeLuckyDip($request)
		{
			$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);

			if (!($luckyDip instanceof LuckyDip))
			{
				return false;
			}

			if (sizeof($luckyDip->getItems()) > 0)
			{
				return false;
			}

			$dalResult = $this->luckyDips_service->removeLuckyDip($luckyDip);

			$this->luckyDips_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function getAllLuckyDips($request)
		{
			$luckyDips = null;
			$dalResult = $this->luckyDips_service->getAllLuckyDips();

			if (is_array($dalResult->getResult()))
			{
				$luckyDips = [];

				foreach ($dalResult->getResult() as $luckyDip_id => $luckyDip)
				{
					$luckyDips[$luckyDip->getId()] = $luckyDip->jsonSerialize();
				}

				$dalResult->setResult($luckyDips);
			}

			$this->luckyDips_service->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function getLuckyDipByName($request) : ?array
		{
			if (!isset($request['luckyDip_name']) || empty($request['luckyDip_name']))
			{
				return null;
			}

			$luckyDip = null;
			$luckyDip_name = $request['luckyDip_name'];

			if (strpos(strtolower($luckyDip_name), "[luckydip]") !== false)
			{
				$luckyDip_name = substr($luckyDip_name, 11);

				$dalResult = $this->luckyDips_service->getLuckyDipByName($luckyDip_name);

				if (!is_null($dalResult->getResult()))
				{
					$luckyDip = $dalResult->getResult();
				}
			}

			$this->luckyDips_service->closeConnexion();

			return $luckyDip->jsonSerialize();
		}
	}
