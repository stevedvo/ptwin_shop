<?php
	function var_debug($variable)
	{
		echo "<pre>";
		var_dump($variable);
		echo "</pre>";
	}

	function getAllLists()
	{
		$ShopDAL = new ShopDAL();

		return $ShopDAL->getAllLists();
	}

	function addList($request)
	{
		$list = createList($request);

		if (!entityIsValid($list))
		{
			return false;
		}

		if (getListByName($list->getName()))
		{
			return false;
		}

		$ShopDAL = new ShopDAL();

		return $ShopDAL->addList($list);
	}

	function createList($request)
	{
		$id = isset($request['list_id']) ? $request['list_id'] : null;
		$name = isset($request['list_name']) ? $request['list_name'] : null;

		$list = new ShopList($id, $name);

		return $list;
	}

	function addItemToList($request)
	{
		$item_id = (isset($request['item_id']) && is_numeric($request['item_id'])) ? intval($request['item_id']) : null;
		$list_id = (isset($request['list_id']) && is_numeric($request['list_id'])) ? intval($request['list_id']) : null;

		if (is_null($item_id) || is_null($list_id))
		{
			return false;
		}

		$item = getItemById($item_id);

		if (!$item)
		{
			return false;
		}

		$list = getListById($list_id);

		if (!$list)
		{
			return false;
		}

		$item->setListId($list->getId());

		return updateItem($item);
	}

	function moveItemsToList($request)
	{
		$item_ids = (isset($request['item_ids']) && is_array($request['item_ids'])) ? $request['item_ids'] : null;
		$list_id = (isset($request['list_id']) && is_numeric($request['list_id'])) ? intval($request['list_id']) : null;

		if (is_null($item_ids) || is_null($list_id))
		{
			return false;
		}

		if (sizeof($item_ids) == 0)
		{
			return false;
		}

		$sanitised_ids = [];

		foreach ($item_ids as $item_id)
		{
			$sanitised_ids[] = intval($item_id);
		}

		sort($sanitised_ids);

		$items = getItemsById($item_ids);

		if ($sanitised_ids !== array_keys($items))
		{
			return false;
		}

		$list = getListById($list_id);

		if (!$list)
		{
			return false;
		}

		$ShopDAL = new ShopDAL();

		return $ShopDAL->moveItemsToList($items, $list);
	}

	function getItemsById($item_ids)
	{
		if (!is_array($item_ids))
		{
			return false;
		}

		if (sizeof($item_ids) == 0)
		{
			return false;
		}

		$ShopDAL = new ShopDAL();

		return $ShopDAL->getItemsById($item_ids);
	}

	function createItem($request)
	{
		$id = isset($request['item_id']) ? $request['item_id'] : null;
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

	function getListByName($list_name)
	{
		$ShopDAL = new ShopDAL();

		return $ShopDAL->getListByName($list_name);
	}

	function getListById($list_id)
	{
		$ShopDAL = new ShopDAL();
		$list = $ShopDAL->getListById($list_id);

		return ($list && entityIsValid($list) ? $list : false);
	}

	function removeList($request)
	{
		$list_id = (isset($request['list_id']) && is_numeric($request['list_id'])) ? intval($request['list_id']) : null;

		if (is_null($list_id))
		{
			return false;
		}

		$items = getItemsByListId($list_id);

		if (is_array($items) && sizeof($items) > 0)
		{
			return false;
		}

		$list = getListById($list_id);

		if (!$list)
		{
			return false;
		}

		$ShopDAL = new ShopDAL();

		return $ShopDAL->removeList($list);
	}

	function getItemsByListId($list_id)
	{
		if (!is_numeric($list_id))
		{
			return false;
		}

		$ShopDAL = new ShopDAL();

		return $ShopDAL->getItemsByListId($list_id);
	}

	function getAllItems()
	{
		$ShopDAL = new ShopDAL();

		return $ShopDAL->getAllItems();
	}

	function getItemById($item_id)
	{
		$ShopDAL = new ShopDAL();

		return $ShopDAL->getItemById($item_id);
	}

	function updateItem($item)
	{
		$ShopDAL = new ShopDAL();

		return $ShopDAL->updateItem($item);
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
