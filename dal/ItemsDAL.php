<?php
	declare(strict_types=1);

	class ItemsDAL
	{
		private $ShopDb;

		public function __construct()
		{
			$this->ShopDb = new ShopDb();
		}

		public function closeConnexion()
		{
			$this->ShopDb = null;
		}

		private function getConsumptionPeriodSqlUnit(string $period) : string
		{
			switch ($period)
			{
				case 'day':
					return 'DAY';
				case 'week':
					return 'WEEK';
				case 'month':
					return 'MONTH';
				case 'year':
					return 'YEAR';
			}

			throw new InvalidArgumentException("Invalid Consumption Period");
		}

		public function addItem(Item $item) : Item
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO items (description, comments, default_qty, list_id, link, primary_dept, packsize_id) VALUES (:description, :comments, :default_qty, :list_id, :link, :primary_dept, :packsize_id)");
				$query->execute(
				[
					':description'  => $item->getDescription(),
					':comments'     => $item->getComments(),
					':default_qty'  => !is_null($item->getDefaultQty()) ? $item->getDefaultQty() : 1,
					':list_id'      => $item->getListId(),
					':primary_dept' => $item->getPrimaryDept(),
					':link'         => $item->getLink(),
					':packsize_id'  => $item->getPackSizeId()
				]);

				$item->setId(intval($this->ShopDb->conn->lastInsertId()));

				return $item;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getItemById($item_id) : ?Item
		{
			try
			{
				$item = null;

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check, ps.name AS packsize_name, ps.short_name AS packsize_short_name, idl.dept_id, d.dept_name, d.seq FROM items AS i LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) LEFT JOIN item_dept_link AS idl ON (idl.item_id = i.item_id) LEFT JOIN departments AS d ON (d.dept_id = idl.dept_id) WHERE i.item_id = :item_id ORDER BY d.seq, d.dept_name");
				$query->execute([':item_id' => $item_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$departments = [];

					foreach ($rows as $row)
					{
						if (!($item instanceof Item))
						{
							$item = createItem($row);
							$packsize = createPackSize($row);
							$item->setPackSize($packsize);
						}

						$department = createDepartment($row);

						if (entityIsValid($department))
						{
							$item->addDepartment($department);
						}
					}
				}

				return $item;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getItemsById(array $item_ids) : ?array
		{
			try
			{
				$items = null;

				$query_string = "";
				$query_values = [];

				foreach ($item_ids as $key => $item_id)
				{
					$query_string.= ":id_".$key.", ";
					$query_values[":id_".$key] = $item_id;
				}

				$query_string = rtrim($query_string, ", ");

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM items AS i WHERE i.item_id IN (".$query_string.")");
				$query->execute($query_values);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);

						$items[$item->getId()] = $item;
					}
				}

				return $items;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getItemByDescription(string $description) : ?Item
		{
			try
			{
				$item = null;

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check, ps.name AS packsize_name, ps.short_name AS packsize_short_name FROM items AS i LEFT JOIN pack_sizes AS ps ON (ps.id = i.packsize_id) WHERE i.description = :description");
				$query->execute([':description' => $description]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$item = createItem($row);
					$packsize = createPackSize($row);
					$item->setPackSize($packsize);
				}

				return $item;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getAllItems() : ?array
		{
			try
			{
				$items = null;

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM items AS i ORDER BY i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);
						$items[$item->getId()] = $item;
					}
				}

				return $items;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getAllItemsNotInLuckyDip(int $luckyDipId) : ?array
		{
			try
			{
				$items = null;

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM items AS i WHERE i.luckydip_id != :luckydip_id OR luckydip_id IS NULL ORDER BY i.description");
				$query->execute([':luckydip_id' => $luckyDipId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);
						$items[$item->getId()] = $item;
					}
				}

				return $items;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getAllItemsNotInMeal(int $mealId) : array
		{
			try
			{
				$items = [];

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check, mi.id AS meal_item_id, mi.meal_id, mi.quantity FROM items AS i LEFT JOIN meal_items AS mi ON (i.item_id = mi.item_id) WHERE mi.meal_id IS NULL OR mi.meal_id != :meal_id ORDER BY i.description");
				$query->execute([':meal_id' => $mealId]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					foreach ($rows as $row)
					{
						$item = createItem($row);
						$items[$item->getId()] = $item;
					}
				}

				return $items;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getAllSuggestedItems(int $interval = DEFAULT_CONSUMPTION_INTERVAL, string $period = DEFAULT_CONSUMPTION_PERIOD) : ?array
		{
			try
			{
				$items = [];
				$interval = intval($interval);

				if ($interval < 1)
				{
					throw new InvalidArgumentException("Invalid Consumption Interval");
				}

				$periodSqlUnit = $this->getConsumptionPeriodSqlUnit($period);
				$cutoffSql = "DATE_SUB(CURDATE(), INTERVAL ".$interval." ".$periodSqlUnit.")";

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM items AS i LEFT JOIN (SELECT oi.item_id, SUM(oi.quantity) AS total_ordered, MIN(o.date_ordered) AS first_ordered FROM order_items AS oi INNER JOIN orders AS o ON (o.id = oi.order_id) WHERE o.date_ordered IS NOT NULL GROUP BY oi.item_id) AS order_stats ON (order_stats.item_id = i.item_id) LEFT JOIN order_items AS last_oi ON (last_oi.id = (SELECT oi2.id FROM order_items AS oi2 INNER JOIN orders AS o2 ON (o2.id = oi2.order_id) WHERE oi2.item_id = i.item_id AND o2.date_ordered IS NOT NULL ORDER BY o2.date_ordered DESC, oi2.id DESC LIMIT 1)) LEFT JOIN orders AS last_o ON (last_o.id = last_oi.order_id) LEFT JOIN (SELECT recent_orders.item_id, SUM(recent_orders.quantity) AS total_recent_ordered, MIN(recent_orders.date_ordered) AS first_recent_ordered FROM (SELECT oi.item_id, oi.quantity, o.date_ordered FROM order_items AS oi INNER JOIN orders AS o ON (o.id = oi.order_id) LEFT JOIN (SELECT oi2.item_id, MAX(o2.date_ordered) AS boundary_ordered FROM order_items AS oi2 INNER JOIN orders AS o2 ON (o2.id = oi2.order_id) WHERE o2.date_ordered IS NOT NULL AND o2.date_ordered < ".$cutoffSql." GROUP BY oi2.item_id) AS recent_boundary ON (recent_boundary.item_id = oi.item_id) WHERE o.date_ordered IS NOT NULL AND (o.date_ordered >= ".$cutoffSql." OR o.date_ordered = recent_boundary.boundary_ordered)) AS recent_orders GROUP BY recent_orders.item_id) AS recent_order_stats ON (recent_order_stats.item_id = i.item_id) WHERE EXISTS (SELECT 1 FROM meal_items AS mi INNER JOIN meal_plan_days AS mpd ON (mpd.meal_id = mi.meal_id) WHERE mi.item_id = i.item_id AND mpd.date > CURDATE() AND mpd.date < ADDDATE(CURDATE(), INTERVAL 14 DAY)) OR (i.mute_temp = 0 AND i.mute_perm = 0 AND last_oi.id IS NOT NULL AND ((order_stats.first_ordered IS NOT NULL AND ABS(DATEDIFF(CURDATE(), order_stats.first_ordered)) > 0 AND last_oi.quantity - ROUND((order_stats.total_ordered / ABS(DATEDIFF(CURDATE(), order_stats.first_ordered))) * (ABS(DATEDIFF(CURDATE(), last_o.date_ordered)) + 7)) < 0) OR (recent_order_stats.first_recent_ordered IS NOT NULL AND ABS(DATEDIFF(CURDATE(), recent_order_stats.first_recent_ordered)) > 0 AND last_oi.quantity - ROUND((recent_order_stats.total_recent_ordered / ABS(DATEDIFF(CURDATE(), recent_order_stats.first_recent_ordered))) * (ABS(DATEDIFF(CURDATE(), last_o.date_ordered)) + 7)) < 0))) ORDER BY i.description");
				$query->execute();

				while ($row = $query->fetch(PDO::FETCH_ASSOC))
				{
					$item = createItem($row);
					$items[$item->getId()] = $item;
				}

				if (count($items) > 0)
				{
					$queryString = "";
					$queryValues = [];

					foreach (array_keys($items) as $key => $itemId)
					{
						$queryString.= ":item_id_".$key.", ";
						$queryValues[":item_id_".$key] = $itemId;
					}

					$queryString = rtrim($queryString, ", ");

					$query = $this->ShopDb->conn->prepare("SELECT mi.id AS meal_item_id, mi.meal_id, mi.item_id, mi.quantity AS meal_item_quantity, mpd.id AS meal_plan_day_id, mpd.date AS meal_plan_date, mpd.order_item_status FROM meal_items AS mi INNER JOIN meal_plan_days AS mpd ON (mpd.meal_id = mi.meal_id) WHERE mi.item_id IN (".$queryString.") AND mpd.date > CURDATE() AND mpd.date < ADDDATE(CURDATE(), INTERVAL 14 DAY) ORDER BY mpd.date");
					$query->execute($queryValues);

					while ($row = $query->fetch(PDO::FETCH_ASSOC))
					{
						if (array_key_exists($row['item_id'], $items) && !$items[$row['item_id']]->hasMealItem($row['meal_plan_date']))
						{
							$mealItem = createMealItem($row);
							$items[$row['item_id']]->addMealItem($row['meal_plan_date'], $mealItem);
						}
					}
				}

				return $items;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getAllMutedSuggestedItems() : ?array
		{
			try
			{
				$items = null;

				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM items AS i WHERE i.mute_temp = 1 OR i.mute_perm = 1 ORDER BY i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$items = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['item_id'], $items))
						{
							$item = createItem($row);
							$items[$item->getId()] = $item;
						}
					}
				}

				return $items;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getItemsByDepartmentId($dept_id)
		{
			$result = new DalResult();
			$items = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT idl.dept_id, idl.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM item_dept_link AS idl LEFT JOIN items AS i ON (idl.item_id = i.item_id) WHERE idl.dept_id = :dept_id");
				$query->execute([':dept_id' => $dept_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);
						$items[$item->getId()] = $item;
					}
				}

				$result->setResult($items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getItemsByListId($list_id)
		{
			$result = new DalResult();
			$items = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.packsize_id, i.luckydip_id, i.meal_plan_check FROM items AS i WHERE i.list_id = :list_id");
				$query->execute([':list_id' => $list_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$items = [];

					foreach ($rows as $row)
					{
						$item = createItem($row);

						$items[$item->getId()] = $item;
					}
				}

				$result->setResult($items);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateItem(Item $item) : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET description = :description, comments = :comments, default_qty = :default_qty, list_id = :list_id, link = :link, primary_dept = :primary_dept, mute_temp = :mute_temp, mute_perm = :mute_perm, packsize_id = :packsize_id WHERE item_id = :item_id");
				$success = $query->execute(
				[
					':item_id'      => $item->getId(),
					':description'  => $item->getDescription(),
					':comments'     => $item->getComments(),
					':default_qty'  => $item->getDefaultQty(),
					':list_id'      => $item->getListId(),
					':link'         => $item->getLink(),
					':primary_dept' => $item->getPrimaryDept(),
					':mute_temp'    => $item->getMuteTemp(),
					':mute_perm'    => $item->getMutePerm(),
					':packsize_id'  => $item->getPackSizeId()
				]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function addDepartmentToItem(Department $department, Item $item) : ?int
		{
			try
			{
				$result = null;

				$query = $this->ShopDb->conn->prepare("INSERT INTO item_dept_link (dept_id, item_id) VALUES (:dept_id, :item_id)");
				$query->execute(
				[
					':dept_id' => $department->getId(),
					':item_id' => $item->getId()
				]);

				$result = intval($this->ShopDb->conn->lastInsertId());

				return $result;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function removeDepartmentsFromItem(array $deptIds, int $itemId) : bool
		{
			$queryString = "";
			$queryValues = [':item_id' => $itemId];

			foreach ($deptIds as $key => $deptId)
			{
				$queryString.= ":dept_id".$key.", ";
				$queryValues[":dept_id".$key] = $deptId;
			}

			$queryString = rtrim($queryString, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM item_dept_link WHERE dept_id IN (".$queryString.") AND item_id = :item_id");
				$success = $query->execute($queryValues);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function getItemDepartmentLookupArray() : ?array
		{
			try
			{
				$departments_lookup = null;

				$query = $this->ShopDb->conn->prepare("SELECT idl.dept_id, idl.item_id FROM item_dept_link AS idl");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if (is_array($rows))
				{
					$departments_lookup = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['item_id'], $departments_lookup))
						{
							$departments_lookup[$row['item_id']] = $row['dept_id'];
						}
					}
				}

				return $departments_lookup;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function resetMuteTemps() : bool
		{
			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE items SET mute_temp = :mute_temp, meal_plan_check = :meal_plan_check");
				$success = $query->execute(
				[
					'mute_temp'       => 0,
					'meal_plan_check' => 0,
				]);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}

		public function updateMealPlanChecks(array $itemIds) : bool
		{
			try
			{
				foreach ($itemIds as $itemId)
				{
					$args[':item_'.$itemId] = $itemId;
				}

				$inClause = implode(", ", array_keys($args));

				$args[':meal_plan_check'] = 1;

				$query = $this->ShopDb->conn->prepare("UPDATE items SET meal_plan_check = :meal_plan_check WHERE item_id IN (".$inClause.")");
				$success = $query->execute($args);

				return $success;
			}
			catch(PDOException $PdoException)
			{
				throw $PdoException;
			}
			catch(Exception $exception)
			{
				throw $exception;
			}
		}
	}
