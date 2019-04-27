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
		if (!isset($request['list_name']))
		{
			return false;
		}

		if (empty($request['list_name']))
		{
			return false;
		}

		$list_name = $request['list_name'];

		if (getListByName($list_name))
		{
			return false;
		}

		global $ptwin_shopDB;

		$list_id = false;
		$query = $ptwin_shopDB->prepare("INSERT INTO lists (name) VALUES (?)");
		$query->bind_param("s", $list_name);
		$query->execute();

		return $list_id;
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
