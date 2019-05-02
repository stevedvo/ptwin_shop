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
		global $ptwin_shopDB;

		$list = false;
		$query = $ptwin_shopDB->prepare("SELECT l.list_id, l.name AS list_name, i.item_id, i.description, i.comments, i.default_qty, i.total_qty, i.last_ordered, i.selected, i.link FROM lists AS l LEFT JOIN items AS i ON (l.list_id = i.list_id) WHERE l.list_id = ?");
		$query->bind_param("s", $list_id);
		$query->execute();
		$result = $query->get_result();

		if ($result->num_rows)
		{
			while ($row = $result->fetch_assoc())
			{
				if (!$list)
				{
					$list = createList($row);
				}

				$item = createItem($row);
				$list->addItem($item);
			}
		}

		return $list;
	}

	function getAllItems()
	{
		global $ptwin_shopDB;

		$items = false;
		$query = $ptwin_shopDB->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.total_qty, i.last_ordered, i.selected, i.list_id, i.link FROM items AS i");
		$query->execute();
		$result = $query->get_result();

		if ($result->num_rows)
		{
			$items = [];

			while ($row = $result->fetch_assoc())
			{
				$item = createItem($row);
				$items[$item->getId()] = $item;
			}
		}

		return $items;
	}

	function getItemById($item_id)
	{
		global $ptwin_shopDB;

		$item = false;
		$query = $ptwin_shopDB->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.total_qty, i.last_ordered, i.selected, i.list_id, i.link FROM items AS i WHERE i.item_id = ?");
		$query->bind_param("s", $item_id);
		$query->execute();
		$result = $query->get_result();

		if ($result->num_rows)
		{
			while ($row = $result->fetch_assoc())
			{
				if (!$item)
				{
					$item = createItem($row);
				}
			}
		}

		return $item;
	}

	function updateItem($item)
	{
		global $ptwin_shopDB;

		$result = false;

		$description = $item->getDescription();
		$comments = $item->getComments();
		$default_qty = $item->getDefaultQty();
		$total_qty = $item->getTotalQty();
		$last_ordered = $item->getLastOrdered() ? $item->getLastOrdered()->format('Y-m-d') : null;
		$selected = $item->getSelected();
		$list_id = $item->getListId();
		$link = $item->getLink();

		$query = $ptwin_shopDB->prepare("UPDATE items SET description = ?, comments = ?, default_qty = ?, total_qty = ?, last_ordered = ?, selected = ?, list_id = ?, link = ? WHERE item_id = ?");
		$query->bind_param("ssiisbisi", $description, $comments, $default_qty, $total_qty, $last_ordered, $selected, $list_id, $link, $item_id);
		$query->execute();
		$result = $query->affected_rows;

		return $result;
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
