<?php
	declare(strict_types=1);

	class TagsController
	{
		private $tagsService;
		private $tagsViewModelBuilder;

		public function __construct()
		{
			$this->tagsService = new TagsService();
			$this->tagsViewModelBuilder = new TagsViewModelBuilder();
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
				$tagPrototype = new Tag();
				$tags = $this->tagsService->getAllTags();

				$this->tagsService->closeConnexion();

				$pageData =
				[
					'page_title' => 'Manage Tags',
					'template'   => 'views/tags/index.php',
					'page_data'  =>
					[
						'tagPrototype' => $tagPrototype,
						'tags'         => $tags,
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

		public function addTag(array $request) : ?array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = createTag($request);

				if (!entityIsValid($tag))
				{
					$dalResult->setException(new Exception("Invalid Tag"));

					$this->tagsService->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$tag = $this->tagsService->addTag($tag);
				$tags = $this->tagsService->getAllTags();

				$dalResult->setPartialView(getPartialView("TagListItems", ['items' => $tags]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e->getMessage());
			}

			$this->tagsService->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editTag(array $request) : ?array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = createTag($request);

				if (!entityIsValid($tag))
				{
					$dalResult->setException(new Exception("Invalid Tag"));

					$this->tagsService->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$tag = $this->tagsService->updateTag($tag);
				$tags = $this->tagsService->getAllTags();

				$dalResult->setPartialView(getPartialView("TagListItems", ['items' => $tags]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e->getMessage());
			}

			$this->tagsService->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null) : void
		{
			$tag = $all_items = null;
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => []
			];

			try
			{
				if (!is_numeric($request))
				{
					renderPage($pageData);
				}

				$tag = $this->tagsService->getTagById(intval($request));
				
				if (is_null($tag))
				{
					renderPage($pageData);
				}

				$itemList = $this->itemsService->getAllItemsNotInTag($tag->getId());

				$pageData =
				[
					'page_title' => 'Edit '.$tag->getName(),
					'breadcrumb' =>
					[
						[
							'link' => '/tags/',
							'text' => 'Tags',
						],
						[
							'text' => 'Edit',
						]
					],
					'template'   => 'views/tags/edit.php',
					'page_data'  =>
					[
						'tag'      => $tag,
						'item_list' => $itemList,
					],
				];

				$this->tagsService->closeConnexion();
				$this->itemsService->closeConnexion();

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function addItemToTag(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = $this->tagsService->verifyTagRequest($request);
				$item = $this->itemsService->verifyItemRequest($request);

				$tagItem = $this->tagsService->addItemToTag($tag, $item);
				$tag->addTagItem($tagItem);

				$params =
				[
					'tagId'    => $tag->getId(),
					'tagItems' => $tag->getTagItems($reSort = true),
				];

				$dalResult->setPartialView(getPartialView("TagItemListItems", $params));

				$this->tagsService->closeConnexion();
				$this->itemsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function updateTagItem(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tagItem = $this->tagsService->verifyTagItemRequest($request);

				$quantity = isset($request['tag_item_quantity']) && is_numeric($request['tag_item_quantity']) ? intval($request['tag_item_quantity']) : null;
				$tagItem->setQuantity($quantity);

				$dalResult->setResult($this->tagsService->updateTagItem($tagItem));

				$this->tagsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeItemFromTag(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = $this->tagsService->verifyTagRequest($request);
				$tagItem = $this->tagsService->verifyTagItemRequest($request);

				$dalResult->setResult($this->tagsService->removeItemFromTag($tagItem, $tag));
				$tag->removeTagItem($tagItem);

				$params =
				[
					'tagId'    => $tag->getId(),
					'tagItems' => $tag->getTagItems($reSort = true),
				];

				$dalResult->setPartialView(getPartialView("TagItemListItems", $params));

				$this->tagsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function removeTag(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = $this->tagsService->verifyTagRequest($request);

				if (sizeof($tag->getTagItems()) > 0)
				{
					$dalResult->setException(new Exception("Cannot remove Tag with TagItems associated"));

					$this->tagsService->closeConnexion();

					return $dalResult->jsonSerialize();
				}

				$dalResult->setResult($this->tagsService->removeTag($tag));

				$this->tagsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function restoreTag(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = $this->tagsService->verifyTagRequest($request);

				$dalResult->setResult($this->tagsService->restoreTag($tag)->jsonSerialize());

				$this->tagsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function Plans(array $request = null) : void
		{
			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
			];

			try
			{
				if (!isset($request['date']))
				{
					$date = new DateTime();
				}
				else
				{
					$date = sanitiseDate($request['date']);
				}

				if (!($date instanceof DateTime))
				{
					$pageData['page_data'] = ['message' => "Invalid date"];

					renderPage($pageData);
				}

				$origDate = DateTimeImmutable::createFromMutable($date);
				$calendarStart = $origDate->modify("-".($date->format("N") - 1)." day")->modify("-4 day");

				$dateArray = [];

				for ($i = 0; $i < 25; $i++)
				{
					if ($i == 0)
					{
						$dateArray[$i] = $calendarStart;
					}
					else
					{
						$dateArray[$i] = $dateArray[$i - 1]->modify("+1 day");
					}
				}

				$dateFrom = reset($dateArray);
				$dateTo = end($dateArray);

				$tagPlans = $this->tagsService->getTagPlansInDateRange($dateFrom, $dateTo);
				$tagPlanViewModels = $this->tagsViewModelBuilder->createTagPlanViewModels($dateArray, $tagPlans);

				$pageData =
				[
					'page_title' => 'Tag Plans',
					'template'   => 'views/tags/plans.php',
					'page_data'  => ['tagPlans' => $tagPlanViewModels],
				];

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function getTagPlanByDate(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tagPlan = $this->tagsService->getTagPlanByDate($request);
				$tags = $this->tagsService->getAllTags();

				$editTagPlanDayViewModel = $this->tagsViewModelBuilder->createEditTagPlanDayViewModel($tagPlan, $tags);

				$dalResult->setPartialView(getPartialView("EditTagPlanDay", ['model' => $editTagPlanDayViewModel]));

				$this->tagsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		public function updateTagPlanDay(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tagPlanDay = $this->tagsService->updateTagPlanDay($request);

				$tagPlanCalendarItem = $this->tagsViewModelBuilder->createTagPlanViewModel($tagPlanDay);

				$dalResult->setPartialView(getPartialView("TagPlansCalendarItem", ['tagPlan' => $tagPlanCalendarItem]));

				$this->tagsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		// public function getAllTags($request)
		// {
		// 	$tags = null;
		// 	$dalResult = $this->tagsService->getAllTags();

		// 	if (is_array($dalResult->getResult()))
		// 	{
		// 		$tags = [];

		// 		foreach ($dalResult->getResult() as $tag_id => $tag)
		// 		{
		// 			$tags[$tag->getId()] = $tag->jsonSerialize();
		// 		}

		// 		$dalResult->setResult($tags);
		// 	}

		// 	$this->tagsService->closeConnexion();

		// 	return $dalResult->jsonSerialize();
		// }

		// public function getTagByName($request) : ?array
		// {
		// 	if (!isset($request['tag_name']) || empty($request['tag_name']))
		// 	{
		// 		return null;
		// 	}

		// 	$tag = null;
		// 	$tag_name = $request['tag_name'];

		// 	if (strpos(strtolower($tag_name), "[tag]") !== false)
		// 	{
		// 		$tag_name = substr($tag_name, 11);

		// 		$dalResult = $this->tagsService->getTagByName($tag_name);

		// 		if (!is_null($dalResult->getResult()))
		// 		{
		// 			$tag = $dalResult->getResult();
		// 		}
		// 	}

		// 	$this->tagsService->closeConnexion();

		// 	return $tag->jsonSerialize();
		// }
	}
