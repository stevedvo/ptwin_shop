<?php
	function var_debug($variable)
	{
		echo "<pre>";
		var_dump($variable);
		echo "</pre>";
	}

	function createList($request)
	{
		$id = isset($request['list_id']) ? $request['list_id'] : null;
		$name = isset($request['list_name']) ? $request['list_name'] : null;

		$list = new ShopList($id, $name);

		return $list;
	}

	function createDepartment($request)
	{
		$id = isset($request['dept_id']) ? $request['dept_id'] : null;
		$name = isset($request['dept_name']) ? $request['dept_name'] : null;

		$department = new Department($id, $name);

		return $department;
	}

	function createItem($request)
	{
		$id = isset($request['item_id']) ? intval($request['item_id']) : null;
		$description = isset($request['description']) ? $request['description'] : null;
		$comments = isset($request['comments']) ? $request['comments'] : null;
		$default_qty = isset($request['default_qty']) ? intval($request['default_qty']) : null;
		$total_qty = isset($request['total_qty']) ? intval($request['total_qty']) : null;
		$last_ordered = isset($request['last_ordered']) ? sanitiseDate($request['last_ordered']) : null;
		$selected = isset($request['selected']) ? $request['selected'] : null;
		$list_id = isset($request['list_id']) ? intval($request['list_id']) : null;
		$link = isset($request['link']) ? $request['link'] : null;

		$item = new Item($id, $description, $comments, $default_qty, $total_qty, $last_ordered, $selected, $list_id, $link);

		return $item;
	}

	function entityIsValid($entity)
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
							if (is_null($property_value) || empty($property_value))
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
					}
				}
			}
		}

		return true;
	}

	function sanitiseDate($date_string)
	{
		$date = DateTime::createFromFormat('Y-m-d', $date_string);

		if (!$date)
		{
			$date = DateTime::createFromFormat('d-m-Y', $date_string);
		}

		return $date;
	}

	function renderPage($pageData)
	{
		$page_title = $pageData['page_title'];
		$template = $pageData['template'];
		$response = $pageData['page_data'];
		include_once('views/shared/header.php');
		include_once($template);
		include_once('views/shared/footer.php');
	}
