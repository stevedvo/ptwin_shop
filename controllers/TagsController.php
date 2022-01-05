<?php
	declare(strict_types=1);

	class TagsController
	{
		private $tagsService;
		private $mealsService;
		private $tagsViewModelBuilder;

		public function __construct()
		{
			$this->tagsService = new TagsService();
			$this->mealsService = new MealsService();
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

		public function addTag(array $request) : array
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

				$dalResult->setPartialView(getPartialView("TagListItems", ['tags' => $tags]));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);
			}

			$this->tagsService->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function editTag(array $request) : array
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

				$dalResult->setResult($this->tagsService->updateTag($tag));
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);
			}

			$this->tagsService->closeConnexion();

			return $dalResult->jsonSerialize();
		}

		public function Edit($request = null) : void
		{
			$tag = $mealList = null;

			$pageData =
			[
				'page_title' => 'Not Found',
				'template'   => 'views/404.php',
				'page_data'  => [],
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

				$mealList = $this->tagsService->getAllMealsNotWithTag($tag->getId());

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
						'tag'       => $tag,
						'meal_list' => $mealList,
					],
				];

				$this->tagsService->closeConnexion();

				renderPage($pageData);
			}
			catch (Exception $e)
			{
				$pageData['page_data'] = ['message' => $e->getMessage()];

				renderPage($pageData);
			}
		}

		public function addTagToMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = $this->tagsService->verifyTagRequest($request);
				$meal = $this->mealsService->verifyMealRequest($request);

				$this->tagsService->addTagToMeal($tag, $meal);
				$tag->addMeal($meal);

				$dalResult->setPartialView(getPartialView("TagMealListItems", ['tagId' => $tag->getId(), 'tagMeals' => $tag->getMeals($reSort = true)]));

				$this->tagsService->closeConnexion();
				$this->mealsService->closeConnexion();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);
			}

			return $dalResult->jsonSerialize();
		}

		public function removeTagFromMeal(array $request) : array
		{
			$dalResult = new DalResult();

			try
			{
				$tag = $this->tagsService->verifyTagRequest($request);
				$meal = $this->mealsService->verifyMealRequest($request);

				$this->tagsService->removeTagFromMeal($tag, $meal);

				$mealList = $this->tagsService->getAllMealsNotWithTag($tag->getId());

				$dalResult->setPartialView(getPartialView("TagMealSelection", ['meal_list' => $mealList]));

				$this->tagsService->closeConnexion();
				$this->mealsService->closeConnexion();

				return $dalResult->jsonSerialize();
			}
			catch (Exception $e)
			{
				$dalResult->setException($e);

				return $dalResult->jsonSerialize();
			}
		}

		// public function removeTag(array $request) : array
		// {
		// 	$dalResult = new DalResult();

		// 	try
		// 	{
		// 		$tag = $this->tagsService->verifyTagRequest($request);

		// 		if (sizeof($tag->getTagItems()) > 0)
		// 		{
		// 			$dalResult->setException(new Exception("Cannot remove Tag with TagItems associated"));

		// 			$this->tagsService->closeConnexion();

		// 			return $dalResult->jsonSerialize();
		// 		}

		// 		$dalResult->setResult($this->tagsService->removeTag($tag));

		// 		$this->tagsService->closeConnexion();

		// 		return $dalResult->jsonSerialize();
		// 	}
		// 	catch (Exception $e)
		// 	{
		// 		$dalResult->setException($e);

		// 		return $dalResult->jsonSerialize();
		// 	}
		// }

		// public function restoreTag(array $request) : array
		// {
		// 	$dalResult = new DalResult();

		// 	try
		// 	{
		// 		$tag = $this->tagsService->verifyTagRequest($request);

		// 		$dalResult->setResult($this->tagsService->restoreTag($tag)->jsonSerialize());

		// 		$this->tagsService->closeConnexion();

		// 		return $dalResult->jsonSerialize();
		// 	}
		// 	catch (Exception $e)
		// 	{
		// 		$dalResult->setException($e);

		// 		return $dalResult->jsonSerialize();
		// 	}
		// }

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
