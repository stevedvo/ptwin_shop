<?php
	function removeDepartment($request)
	{
		global $ptwin_shopDB;
		$result = false;

		if (isset($request['item_id']) && is_numeric($request['item_id']) && isset($request['dept_id']) && is_numeric($request['dept_id']))
		{
			$item_id = $request['item_id'];
			$dept_id = $request['dept_id'];

			try
			{
				$query = $ptwin_shopDB->prepare("DELETE FROM item_dept_link WHERE item_id = ? AND dept_id = ?");
				$query->bind_param("ii", $item_id, $dept_id);
				$query->execute();

				$result = $query->affected_rows > 0 ? true : false;
			}
			catch(Exception $e)
			{
				return $e;
			}

		}

		return $result;
	}
