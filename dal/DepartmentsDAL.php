<?php
	class DepartmentsDAL
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

		public function addDepartment($department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO departments (dept_name) VALUES (:name)");
				$query->execute([':name' => $department->getName()]);
				$result->setResult($this->ShopDb->conn->lastInsertId());
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getDepartmentById($dept_id)
		{
			$result = new DalResult();
			$department = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT d.dept_id, d.dept_name, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm FROM departments AS d LEFT JOIN item_dept_link AS idl ON (d.dept_id = idl.dept_id) LEFT JOIN items AS i ON (idl.item_id = i.item_id) WHERE d.dept_id = :dept_id ORDER BY i.description");
				$query->execute([':dept_id' => $dept_id]);
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					foreach ($rows as $row)
					{
						if (!$department)
						{
							$department = createDepartment($row);
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$department->addItem($item);
						}
					}
				}

				$result->setResult($department);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getDepartmentByName($dept_name)
		{
			$result = new DalResult();
			$department = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT dept_id, dept_name FROM departments WHERE dept_name = :name");
				$query->execute([':name' => $dept_name]);
				$row = $query->fetch(PDO::FETCH_ASSOC);

				if ($row)
				{
					$department = createDepartment($row);
				}

				$result->setResult($department);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllDepartments()
		{
			$result = new DalResult();
			$departments = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT dept_id, dept_name FROM departments ORDER BY dept_name");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						$department = createDepartment($row);

						$departments[$department->getId()] = $department;
					}
				}

				$result->setResult($departments);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getAllDepartmentsWithItems()
		{
			$result = new DalResult();
			$departments = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT d.dept_id, d.dept_name, i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, i.mute_temp, i.mute_perm FROM departments AS d LEFT JOIN item_dept_link AS idl ON (d.dept_id = idl.dept_id) LEFT JOIN items AS i ON (idl.item_id = i.item_id) ORDER BY d.dept_name, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						if (!array_key_exists($row['dept_id'], $departments))
						{
							$department = createDepartment($row);
							$departments[$department->getId()] = $department;
						}

						$item = createItem($row);

						if (entityIsValid($item))
						{
							$departments[$row['dept_id']]->addItem($item);
						}
					}
				}

				$result->setResult($departments);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function addItemToDepartment($item, $department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("INSERT INTO item_dept_link (dept_id, item_id) VALUES (:dept_id, :item_id)");
				$result->setResult($query->execute(
				[
					':dept_id' => $department->getId(),
					':item_id' => $item->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeItemsFromDepartment($item_ids, $dept_id)
		{
			$result = new DalResult();

			$query_string = "";
			$query_values = [':dept_id' => $dept_id];

			foreach ($item_ids as $key => $item_id)
			{
				$query_string.= ":item_id_".$key.", ";
				$query_values[":item_id_".$key] = $item_id;
			}

			$query_string = rtrim($query_string, ", ");

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM item_dept_link WHERE item_id IN (".$query_string.") AND dept_id = :dept_id");
				$result->setResult($query->execute($query_values));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function updateDepartment($department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("UPDATE departments SET dept_name = :dept_name WHERE dept_id = :dept_id");
				$result->setResult($query->execute(
				[
					':dept_name' => $department->getName(),
					':dept_id'   => $department->getId()
				]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function removeDepartment($department)
		{
			$result = new DalResult();

			try
			{
				$query = $this->ShopDb->conn->prepare("DELETE FROM departments WHERE dept_id = :dept_id");
				$result->setResult($query->execute([':dept_id' => $department->getId()]));
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}

		public function getPrimaryDepartments()
		{
			$result = new DalResult();
			$departments = false;

			try
			{
				$query = $this->ShopDb->conn->prepare("SELECT i.item_id, i.description, i.comments, i.default_qty, i.list_id, i.link, i.primary_dept, i.mute_temp, i.mute_perm, idl.dept_id, d.dept_name FROM items AS i LEFT JOIN item_dept_link AS idl ON (idl.item_id = i.item_id) LEFT JOIN departments AS d ON (d.dept_id = idl.dept_id) ORDER BY ISNULL(i.primary_dept), d.dept_name, i.description");
				$query->execute();
				$rows = $query->fetchAll(PDO::FETCH_ASSOC);

				if ($rows)
				{
					$departments = [];

					foreach ($rows as $row)
					{
						if (is_null($row['primary_dept']))
						{
							$key = 0;

							if (!array_key_exists($key, $departments))
							{
								$departments[$key] = new Department();
							}

							$item = createItem($row);
							$departments[$key]->addItem($item);
						}
						else
						{
							$key = $row['primary_dept'];

							if ($key == $row['dept_id'])
							{
								if (!array_key_exists($key, $departments))
								{
									$department = createDepartment($row);
									$departments[$department->getId()] = $department;
								}

								$item = createItem($row);
								$departments[$key]->addItem($item);
							}
						}
					}
				}

				$result->setResult($departments);
			}
			catch(PDOException $e)
			{
				$result->setException($e);
			}

			return $result;
		}
	}
