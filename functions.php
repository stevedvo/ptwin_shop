<?php
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

	function getListByName($list_name)
	{
		global $ptwin_shopDB;

		$list = false;
		$query = $ptwin_shopDB->prepare("SELECT list_id, name FROM lists WHERE name = ?");
		$query->bind_param("s", $list_name);
		$query->execute();
		$result = $query->get_result();

		if ($result->num_rows)
		{
			while ($row = $result->fetch_assoc())
			{
				if (!$list)
				{
					$list = new ShopList($row['list_id']);
					$list->setName($row['name']);
				}
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
