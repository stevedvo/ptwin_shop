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
		$id = isset($request['dept_id']) ? intval($request['dept_id']) : null;
		$name = isset($request['dept_name']) ? $request['dept_name'] : null;
		$seq = isset($request['seq']) ? intval($request['seq']) : null;

		$department = new Department($id, $name, $seq);

		return $department;
	}

	function createItem($request)
	{
		$id = isset($request['item_id']) ? intval($request['item_id']) : null;
		$description = isset($request['description']) ? $request['description'] : null;
		$comments = isset($request['comments']) ? $request['comments'] : null;
		$default_qty = isset($request['default_qty']) ? intval($request['default_qty']) : null;
		$list_id = isset($request['list_id']) ? intval($request['list_id']) : null;
		$link = isset($request['link']) ? $request['link'] : null;
		$primary_dept = isset($request['primary_dept']) ? intval($request['primary_dept']) : null;
		$mute_temp = isset($request['mute_temp']) ? intval($request['mute_temp']) : 0;
		$mute_perm = isset($request['mute_perm']) ? intval($request['mute_perm']) : 0;
		$packsize_id = isset($request['packsize_id']) ? intval($request['packsize_id']) : null;

		$item = new Item($id, $description, $comments, $default_qty, $list_id, $link, $primary_dept, $mute_temp, $mute_perm, $packsize_id);

		return $item;
	}

	function createOrder($request)
	{
		$id = isset($request['order_id']) ? intval($request['order_id']) : null;
		$date_ordered = isset($request['date_ordered']) ? sanitiseDate($request['date_ordered']) : null;

		$order = new Order($id, $date_ordered);

		return $order;
	}

	function createOrderItem($request)
	{
		$id = isset($request['order_item_id']) ? intval($request['order_item_id']) : null;
		$order_id = isset($request['order_id']) ? $request['order_id'] : null;
		$item_id = isset($request['item_id']) ? $request['item_id'] : null;
		$quantity = isset($request['quantity']) ? intval($request['quantity']) : null;
		$checked = isset($request['checked']) ? intval($request['checked']) : null;

		$order_item = new OrderItem($id, $order_id, $item_id, $quantity, $checked);

		return $order_item;
	}

	function createPackSize($request)
	{
		$id = isset($request['packsize_id']) ? intval($request['packsize_id']) : null;
		$name = isset($request['packsize_name']) ? $request['packsize_name'] : null;
		$short_name = isset($request['packsize_short_name']) ? $request['packsize_short_name'] : null;

		$packsize = new PackSize($id, $name, $short_name);

		return $packsize;
	}

	function createLuckyDip($request) : LuckyDip
	{
		$id = isset($request['luckyDip_id']) ? intval($request['luckyDip_id']) : null;
		$name = isset($request['luckyDip_name']) ? $request['luckyDip_name'] : null;

		$luckyDip = new LuckyDip($id, $name);

		return $luckyDip;
	}

	function getValidationString($object, $property)
	{
		if (is_null($object) || !is_object($object) || is_null($property) || empty($property))
		{
			return false;
		}

		if (!isset($object->getValidation()[$property]))
		{
			return false;
		}

		$validation = "{";

		foreach ($object->getValidation()[$property] as $key => $value)
		{
			$validation.= "&quot;".$key."&quot;:&quot;".$value."&quot;,";
		}

		$validation = rtrim($validation, ",")."}";

		return $validation;
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
							if (is_null($property_value) || (is_string($property_value) && strlen($property_value) < 1))
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

	function sanitiseDate($date_string)
	{
		$date = DateTime::createFromFormat('Y-m-d', $date_string);

		if ($date)
		{
			$validDate = checkdate(substr($date_string, 5, 2), substr($date_string, 8, 2), substr($date_string, 0, 4));

			if (!$validDate)
			{
				$date = false;
			}
		}

		if (!$date)
		{
			$date = DateTime::createFromFormat('d-m-Y', $date_string);

			if ($date)
			{
				$validDate = checkdate(substr($date_string, 3, 2), substr($date_string, 0, 2), substr($date_string, 6, 4));

				if (!$validDate)
				{
					$date = false;
				}
			}
		}

		return $date;
	}

	function renderPage($pageData)
	{
		$page_title = $pageData['page_title'];
		$breadcrumb = (isset($pageData['breadcrumb']) && is_array($pageData['breadcrumb'])) ? renderBreadcrumb($pageData['breadcrumb']) : $page_title;
		$template = $pageData['template'];
		$response = $pageData['page_data'];
		include_once('views/shared/header.php');
		include_once($template);
		include_once('views/shared/footer.php');
		exit;
	}

	function renderPrint($pageData)
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
