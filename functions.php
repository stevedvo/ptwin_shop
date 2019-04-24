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
