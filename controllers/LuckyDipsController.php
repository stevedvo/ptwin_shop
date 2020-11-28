<?php
	declare(strict_types=1);

	class LuckyDipsController
	{
		private $luckyDips_service;
		private $items_service;
		private $lists_service;

		public function __construct()
		{
			$this->luckyDips_service = new LuckyDipsService();
			$this->items_service = new ItemsService();
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
				$luckyDipPrototype = new LuckyDip();
				$luckyDips = $this->luckyDips_service->getAllLuckyDips();

				$this->luckyDips_service->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Lucky Dips',
					'template'   => 'views/luckyDips/index.php',
					'page_data'  =>
					[
						'luckyDipPrototype' => $luckyDipPrototype,
						'luckyDips'         => $luckyDips,
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

		public function addLuckyDip(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$luckyDip = createLuckyDip($request);

				if (!entityIsValid($luckyDip))
				{
					$dalResult->setException(new Exception("Invalid LuckyDip"));

					return $dalResult->jsonSerialize();
				}

				if (!$this->luckyDips_service->luckyDipDoesNotExist($luckyDip->getName()))
				{
					$dalResult->setException(new Exception("LuckyDip '".$luckyDip->getName()."' already exists"));

					return $dalResult->jsonSerialize();
				}

				$luckyDip = $this->luckyDips_service->addLuckyDip($luckyDip);

				$dalResult->setPartialView(getPartialView("LuckyDipListItem", ['item' => $luckyDip]));

				$this->luckyDips_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function Edit(?int $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest(['luckyDip_id' => $request]);
				$allItems = $this->items_service->getAllItemsNotInLuckyDip($luckyDip->getId());
				$lists = $this->lists_service->getAllLists();

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
						],
					],
					'template'   => 'views/luckyDips/edit.php',
					'page_data'  =>
					[
						'luckyDip'  => $luckyDip,
						'all_items' => $allItems,
						'lists'     => $lists,
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

		public function addItemToLuckyDip(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);

				$dalResult->setResult($this->luckyDips_service->addItemToLuckyDip($item, $luckyDip));
				$dalResult->setPartialView(getPartialView("LuckyDipItem", ['item' => $item]));

				$this->luckyDips_service->closeConnexion();
				$this->items_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeItemFromLuckyDip(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$item = $this->items_service->verifyItemRequest($request);
				$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);

				$dalResult->setResult($this->luckyDips_service->removeItemFromLuckyDip($item));

				$this->luckyDips_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function editLuckyDip(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$luckyDip = createLuckyDip($request);

				if (!entityIsValid($luckyDip))
				{
					$dalResult->setException(new Exception("Invalid Lucky Dip"));

					return $dalResult->jsonSerialize();
				}

				$luckyDipUpdate = $this->luckyDips_service->verifyLuckyDipRequest($request);

				$luckyDipUpdate->setName($luckyDip->getName());
				$luckyDipUpdate->setListId($luckyDip->getListId());

				$dalResult->setResult($this->luckyDips_service->updateLuckyDip($luckyDipUpdate));

				$this->luckyDips_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeLuckyDip(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$luckyDip = $this->luckyDips_service->verifyLuckyDipRequest($request);

				if (sizeof($luckyDip->getItems()) > 0)
				{
					$dalResult->setException(new Exception("Cannot remove Lucky Dip whilst it contains Items"));

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($this->luckyDips_service->removeLuckyDip($luckyDip));

				$this->luckyDips_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function getAllLuckyDips(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				$luckyDips = $this->luckyDips_service->getAllLuckyDips();

				foreach ($luckyDips as $luckyDipId => $luckyDip)
				{
					$luckyDips[$luckyDip->getId()] = $luckyDip->jsonSerialize();
				}

				$dalResult->setResult($luckyDips);

				$this->luckyDips_service->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function getLuckyDipByName(array $request) : string
		{
			$dalResult = new DalResult();

			try
			{
				if (!isset($request['luckyDip_name']) || empty($request['luckyDip_name']))
				{
					$dalResult->setException(new Exception("LuckyDip Name not set"));

					return $dalResult->jsonSerialize();
				}

				$luckyDipName = $request['luckyDip_name'];

				if (strpos(strtolower($luckyDipName), "[luckydip]") === false)
				{
					$dalResult->setException(new Exception("Invalid LuckyDip Name"));

					return $dalResult->jsonSerialize();
				}

				$luckyDipName = substr($luckyDipName, 11);

				$luckyDip = $this->luckyDips_service->getLuckyDipByName($luckyDipName);

				$this->luckyDips_service->closeConnexion();

				$dalResult->setResult($luckydip->jsonSerialize());

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}
	}
