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

				return $this->getItemById(intval($request['item_id']));
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

		public function getAllItemsNotInLuckyDip(int $luckyDipId) : array
		{
			try
			{
				$items = $this->dal->getAllItemsNotInLuckyDip($luckyDipId);

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

					if ($item->getMealPlanCheck() || (is_int($estOverall) && $estOverall < 0) || (is_int($estRecent) && $estRecent < 0))
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

		public function getItemById($itemId) : Item
		{
			try
			{
				$item = $this->dal->getItemById($itemId);

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

		public function getItemsById(array $item_ids) : array
		{
			try
			{
				$items = $this->dal->getItemsById($item_ids);

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

		public function updateMealPlanChecks(array $itemIds) : bool
		{
			try
			{
				if (count($itemIds) == 0)
				{
					return false;
				}

				return $this->dal->updateMealPlanChecks($itemIds);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
