<?php
	function var_debug($variable)
	{
		echo "<pre>";
		var_dump($variable);
		echo "</pre>";
	}

	function getAllLists()
	{
		global $ptwin_shopDB;

		$lists = false;
		$query = $ptwin_shopDB->prepare("SELECT list_id, name FROM lists");
		$query->execute();
		$result = $query->get_result();

		if ($result->num_rows)
		{
			$lists = [];

			while ($row = $result->fetch_assoc())
			{
				$list = new ShopList($row['list_id']);
				$list->setName($row['name']);

				$lists[$list->getId()] = $list;
			}
		}

		return $lists;
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

		global $ptwin_shopDB;

		$list_id = false;
		$name = $list->getName();

		$query = $ptwin_shopDB->prepare("INSERT INTO lists (name) VALUES (?)");
		$query->bind_param("s", $name);
		$query->execute();

		$list_id = $query->insert_id;

		return $list_id;
	}

	function createList($request)
	{
		$id = isset($request['list_id']) ? $request['list_id'] : null;
		$name = isset($request['list_name']) ? $request['list_name'] : null;

		$list = new ShopList($id, $name);

		return $list;
	}

	function createItem($request)
	{
		$id = isset($request['item_id']) ? $request['item_id'] : null;
		$description = isset($request['description']) ? $request['description'] : null;
		$comments = isset($request['comments']) ? $request['comments'] : null;
		$default_qty = isset($request['default_qty']) ? $request['default_qty'] : null;
		$total_qty = isset($request['total_qty']) ? $request['total_qty'] : null;
		$last_ordered = isset($request['last_ordered']) ? $request['last_ordered'] : null;
		$selected = isset($request['selected']) ? $request['selected'] : null;
		$list_id = isset($request['list_id']) ? $request['list_id'] : null;
		$link = isset($request['link']) ? $request['link'] : null;

		$item = new Item($id, $description, $comments, $default_qty, $total_qty, $last_ordered, $selected, $list_id, $link);

		return $item;
	}

	function getListByName($list_name)
	{
		global $ptwin_shopDB;

		$list = false;
		$query = $ptwin_shopDB->prepare("SELECT list_id, name AS list_name FROM lists WHERE name = ?");
		$query->bind_param("s", $list_name);
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
			}
		}

		return $list;
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
