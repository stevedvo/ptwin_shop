<?php
	declare(strict_types=1);

	class ItemsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new ItemsDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function verifyItemRequest(array $request) : Item
		{
			try
			{
				if (!is_numeric($request['item_id']))
				{
					throw new Exception("Invalid Item ID");
				}

				$item = $this->dal->getItemById(intval($request['item_id']));

				if (!($item instanceof Item))
				{
					throw new Exception("Item not found");
				}

				return $item;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addItem(Item $item) : Item
		{
			try
			{
				return $this->dal->addItem($item);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllItems() : array
		{
			try
			{
				$items = $this->dal->getAllItems();

				if (!is_array($items))
				{
					throw new Exception("Items not found");
				}

				return $items;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllItemsNotInLuckyDip(int $luckyDip_id) : DalResult
		{
			return $this->dal->getAllItemsNotInLuckyDip($luckyDip_id);
		}

		public function getAllItemsNotInMeal(int $mealId) : array
		{
			try
			{
				return $this->dal->getAllItemsNotInMeal($mealId);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllSuggestedItems(int $interval, string $period) : array
		{
			try
			{
				$suggestedItems = [];

				$allItems = $this->dal->getAllSuggestedItems();

				if (!is_array($allItems))
				{
					throw new Exception("Suggested Items not found.");
				}

				foreach ($allItems as $itemId => $item)
				{
					$item->calculateRecentOrders($interval, $period);
					$estOverall = $item->getStockLevelPrediction(7, 'overall');
					$estRecent = $item->getStockLevelPrediction(7, 'recent');

					if ((is_int($estOverall) && $estOverall < 1) || (is_int($estRecent) && $estRecent < 1))
					{
						$suggestedItems[$item->getId()] = $item;
					}
				}

				return $suggestedItems;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllMutedSuggestedItems() : array
		{
			try
			{
				$mutedItems = $this->dal->getAllMutedSuggestedItems();

				if (!is_array($mutedItems))
				{
					throw new Exception("Muted Suggestions not found");
				}

				return $mutedItems;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getItemById($item_id) : ?Item
		{
			try
			{
				return $this->dal->getItemById($item_id);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getItemsById($item_ids)
		{
			return $this->dal->getItemsById($item_ids);
		}

		public function getItemByDescription(string $description) : Item
		{
			try
			{
				$item = $this->dal->getItemByDescription($description);

				if (!($item instanceof Item))
				{
					throw new Exception("Item not found");
				}

				return $item;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function itemDoesNotExist(string $description) : bool
		{
			try
			{
				$item = $this->dal->getItemByDescription($description);

				return !($item instanceof Item);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getItemsByDepartmentId($dept_id)
		{
			return $this->dal->getItemsByDepartmentId($dept_id);
		}

		public function getItemsByListId($list_id)
		{
			return $this->dal->getItemsByListId($list_id);
		}

		public function updateItem(Item $item) : bool
		{
			try
			{
				$success = $this->dal->updateItem($item);

				if (!$success)
				{
					throw new Exception("Error updating Item");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addDepartmentToItem(Department $department, Item $item) : int
		{
			try
			{
				$result = $this->dal->addDepartmentToItem($department, $item);

				if (!is_numeric($result))
				{
					throw new Exception("Error adding Department to Item");
				}

				return $result;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function setItemPrimaryDepartment(Department $department, Item $item) : bool
		{
			try
			{
				$item->setPrimaryDept($department->getId());

				$success = $this->dal->updateItem($item);

				if (!$success)
				{
					throw new Exception("Error setting Primary Department to Item");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeDepartmentsFromItem(array $deptIds, int $itemId) : bool
		{
			try
			{
				$success = $this->dal->removeDepartmentsFromItem($deptIds, $itemId);

				if (!$success)
				{
					throw new Exception("Error removing Departments from Item");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getItemDepartmentLookupArray() : array
		{
			try
			{
				$departments_lookup = $this->dal->getItemDepartmentLookupArray();

				if (!is_array($departments_lookup))
				{
					throw new Exception("Error getting Item/Department lookups");
				}

				return $departments_lookup;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function resetMuteTemps() : bool
		{
			try
			{
				return $this->dal->resetMuteTemps();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
