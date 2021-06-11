<?php
	function var_debug($variable)
	{
		echo "<pre>";
		var_dump($variable);
		echo "</pre>";
	}

	function createList(array $request) : ShopList
	{
		$id = isset($request['list_id']) ? $request['list_id'] : null;
		$name = isset($request['list_name']) ? $request['list_name'] : null;

		$list = new ShopList($id, $name);

		return $list;
	}

	function createDepartment(array $request) : Department
	{
		$id = isset($request['dept_id']) ? intval($request['dept_id']) : null;
		$name = isset($request['dept_name']) ? $request['dept_name'] : null;
		$seq = isset($request['seq']) ? intval($request['seq']) : null;

		$department = new Department($id, $name, $seq);

		return $department;
	}

	function createItem(array $request) : Item
	{
		$id = isset($request['item_id']) ? intval($request['item_id']) : null;
		$description = isset($request['description']) ? $request['description'] : null;
		$comments = isset($request['comments']) ? $request['comments'] : null;
		$defaultQty = isset($request['default_qty']) ? intval($request['default_qty']) : null;
		$listId = isset($request['list_id']) ? intval($request['list_id']) : null;
		$link = isset($request['link']) ? $request['link'] : null;
		$primaryDept = isset($request['primary_dept']) ? intval($request['primary_dept']) : null;
		$muteTemp = isset($request['mute_temp']) ? intval($request['mute_temp']) : 0;
		$mutePerm = isset($request['mute_perm']) ? intval($request['mute_perm']) : 0;
		$packSizeId = isset($request['packsize_id']) ? intval($request['packsize_id']) : null;
		$luckyDipId = isset($request['luckydip_id']) ? intval($request['luckydip_id']) : null;
		$mealPlanCheck = isset($request['meal_plan_check']) ? intval($request['meal_plan_check']) : null;

		$item = new Item($id, $description, $comments, $defaultQty, $listId, $link, $primaryDept, $muteTemp, $mutePerm, $packSizeId, $luckyDipId, $mealPlanCheck);

		return $item;
	}

	function createOrder(array $request) : Order
	{
		$id = isset($request['order_id']) ? intval($request['order_id']) : null;
		$date_ordered = isset($request['date_ordered']) ? sanitiseDate($request['date_ordered']) : null;

		$order = new Order($id, $date_ordered);

		return $order;
	}

	function createOrderItem(array $request) : OrderItem
	{
		$id = isset($request['order_item_id']) ? intval($request['order_item_id']) : null;
		$orderId = isset($request['order_id']) ? $request['order_id'] : null;
		$itemId = isset($request['item_id']) ? $request['item_id'] : null;
		$quantity = isset($request['quantity']) ? intval($request['quantity']) : null;
		$checked = isset($request['checked']) ? intval($request['checked']) : null;

		$orderItem = new OrderItem($id, $orderId, $itemId, $quantity, $checked);

		return $orderItem;
	}

	function createPackSize(array $request) : PackSize
	{
		$id = isset($request['packsize_id']) ? intval($request['packsize_id']) : null;
		$name = isset($request['packsize_name']) ? $request['packsize_name'] : null;
		$short_name = isset($request['packsize_short_name']) ? $request['packsize_short_name'] : null;

		$packsize = new PackSize($id, $name, $short_name);

		return $packsize;
	}

	function createLuckyDip(array $request) : LuckyDip
	{
		$id = isset($request['luckyDip_id']) ? intval($request['luckyDip_id']) : null;
		$name = isset($request['luckyDip_name']) ? $request['luckyDip_name'] : null;
		$list_id = isset($request['luckyDip_list_id']) && !empty($request['luckyDip_list_id']) ? intval($request['luckyDip_list_id']) : null;

		$luckyDip = new LuckyDip($id, $name, $list_id);

		return $luckyDip;
	}

	function createMeal(array $request) : Meal
	{
		try
		{
			$id = isset($request['meal_id']) ? intval($request['meal_id']) : null;
			$name = isset($request['meal_name']) ? trim($request['meal_name']) : null;
			$isDeleted = isset($request['meal_isDeleted']) && $request['meal_isDeleted'];

			$meal = new Meal($id, $name, $isDeleted);

			return $meal;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	function createMealItem(array $request) : MealItem
	{
		try
		{
			$id = isset($request['meal_item_id']) ? intval($request['meal_item_id']) : null;
			$mealId = isset($request['meal_id']) ? intval($request['meal_id']) : null;
			$itemId = isset($request['item_id']) ? intval($request['item_id']) : null;
			$quantity = isset($request['meal_item_quantity']) ? intval($request['meal_item_quantity']) : null;

			$mealItem = new MealItem($id, $mealId, $itemId, $quantity);

			return $mealItem;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	function createMealPlanDay(array $request) : MealPlanDay
	{
		try
		{
			$id = isset($request['meal_plan_day_id']) ? intval($request['meal_plan_day_id']) : null;
			$date = isset($request['meal_plan_date']) ? sanitiseDate($request['meal_plan_date']) : null;
			$mealId = (isset($request['meal_id']) && $request['meal_id'] != -1) ? intval($request['meal_id']) : null;
			$orderItemStatus = isset($request['order_item_status']) ? intval($request['order_item_status']) : null;

			$mealPlanDay = new MealPlanDay($id, $date, $mealId, $orderItemStatus);

			return $mealPlanDay;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	function createTag(array $request) : Tag
	{
		try
		{
			$id = isset($request['tag_id']) ? intval($request['tag_id']) : null;
			$name = isset($request['tag_name']) ? trim($request['tag_name']) : null;

			$tag = new Tag($id, $name);

			return $tag;
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	function getValidationString(object $object, string $property) : string
	{
		$validation = "";

		if (is_null($object) || !is_object($object) || is_null($property) || empty($property))
		{
			return $validation;
		}

		$validationArray = [];

		if (method_exists($object, "getAllValidation"))
		{
			if (!isset($object->getAllValidation()[$property]))
			{
				return $validation;
			}

			$validationArray = $object->getAllValidation()[$property];
		}
		else
		{
			if (!isset($object->getValidation()[$property]))
			{
				return $validation;
			}

			$validationArray = $object->getValidation()[$property];
		}

		$validation.= "{";

		foreach ($validationArray as $key => $value)
		{
			$validation.= "&quot;".$key."&quot;:&quot;".$value."&quot;,";
		}

		$validation = rtrim($validation, ",")."}";

		return $validation;
	}

	function entityIsValid($entity) : bool
	{
		if (is_array($entity->getValidation()))
		{
			foreach ($entity->getValidation() as $property => $criteria)
			{
				$method = "get".$property;
				$property_value = $entity->{$method}();

				foreach ($criteria as $criterion => $value)
				{
					switch ($criterion)
					{
						case 'required':
							if (is_null($property_value) || (is_string($property_value) && strlen(trim($property_value)) < 1))
							{
								return false;
							}
							break;
						case 'min-length':
							if (strlen($property_value) < $value)
							{
								return false;
							}
							break;
						case 'max-length':
							if (strlen($property_value) > $value)
							{
								return false;
							}
							break;
						case 'min-value':
							if (intval($property_value) < intval($value))
							{
								return false;
							}
							break;
						case 'datatype':
							{
								switch ($value)
								{
									case 'boolean':
										if ($property_value != 0 && $property_value != 1)
										{
											return false;
										}
									break;
								}
							}
							break;
					}
				}
			}
		}

		return true;
	}

	function sanitiseDate(string $dateString) : ?DateTime
	{
		$date = DateTime::createFromFormat('Y-m-d', $dateString);

		if ($date)
		{
			$validDate = checkdate(substr($dateString, 5, 2), substr($dateString, 8, 2), substr($dateString, 0, 4));

			if (!$validDate)
			{
				$date = false;
			}
		}

		if (!$date)
		{
			$date = DateTime::createFromFormat('d-m-Y', $dateString);

			if ($date)
			{
				$validDate = checkdate(substr($dateString, 3, 2), substr($dateString, 0, 2), substr($dateString, 6, 4));

				if (!$validDate)
				{
					$date = false;
				}
			}
		}

		return $date ?? null;
	}

	function renderPage(array $pageData) : void
	{
		$page_title = isset($pageData['page_title']) ? $pageData['page_title'] : null;
		$breadcrumb = (isset($pageData['breadcrumb']) && is_array($pageData['breadcrumb'])) ? renderBreadcrumb($pageData['breadcrumb']) : $page_title;
		$template = isset($pageData['template']) ? $pageData['template'] : null;
		$response = isset($pageData['page_data']) ? $pageData['page_data'] : null;
		include_once('views/shared/header.php');
		include_once($template);
		include_once('views/shared/footer.php');
		exit;
	}

	function renderPrint(array $pageData) : void
	{
		$page_title = $pageData['page_title'];
		$template = $pageData['template'];
		$response = $pageData['page_data'];
		include_once('views/shared/print-header.php');
		include_once($template);
		include_once('views/shared/print-footer.php');
	}

	function getPartialView(string $template, array $params) : string
	{
		ob_start();
		include('views/partial/'.$template.'.php');
		$partial_view = ob_get_contents();
		ob_end_clean();

		return $partial_view;
	}

	function renderBreadcrumb($breadcrumb)
	{
		$breadcrumb_string = "";

		foreach ($breadcrumb as $element)
		{
			if (isset($element['link']))
			{
				$breadcrumb_string.= "<a href='".SITEURL.$element['link']."'>".$element['text']."</a> > ";
			}
			else
			{
				$breadcrumb_string.= $element['text'];
			}
		}

		return $breadcrumb_string;
	}

	function createSelectListItem(int $value, string $text, array $dataAttributes = []) : SelectListItem
	{
		$selectListItem = new SelectListItem($value, $text, $dataAttributes);

		return $selectListItem;
	}
