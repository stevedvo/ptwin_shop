<?php
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
				$item = null;

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

		public function addItem($item)
		{
			return $this->dal->addItem($item);
		}

		public function getAllItems()
		{
			return $this->dal->getAllItems();
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

		public function getAllSuggestedItems($interval, $period)
		{
			$suggested_items = [];

			$dalResult = $this->dal->getAllSuggestedItems();

			if (!is_null($dalResult->getResult()))
			{
				$all_items = $dalResult->getResult();

				if (is_array($all_items))
				{
					foreach ($all_items as $item_id => $item)
					{
						$item->calculateRecentOrders($interval, $period);
						$est_overall = $item->getStockLevelPrediction(7, 'overall');
						$est_recent = $item->getStockLevelPrediction(7, 'recent');

						if (($est_overall !== false && $est_overall < 0) || ($est_recent !== false && $est_recent < 0))
						{
							$suggested_items[$item->getId()] = $item;
						}
					}
				}
			}

			return $suggested_items;
		}

		public function getAllMutedSuggestedItems()
		{
			$muted_items = [];

			$dalResult = $this->dal->getAllMutedSuggestedItems();

			if (!is_null($dalResult->getResult()))
			{
				$muted_items = $dalResult->getResult();
			}

			return $muted_items;
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

		public function getItemsByDepartmentId($dept_id)
		{
			return $this->dal->getItemsByDepartmentId($dept_id);
		}

		public function getItemsByListId($list_id)
		{
			return $this->dal->getItemsByListId($list_id);
		}

		public function updateItem($item)
		{
			return $this->dal->updateItem($item);
		}

		public function addDepartmentToItem($department, $item)
		{
			return $this->dal->addDepartmentToItem($department, $item);
		}

		public function setItemPrimaryDepartment($department, $item)
		{
			$item->setPrimaryDept($department->getId());

			return $this->dal->updateItem($item);
		}

		public function removeDepartmentsFromItem($dept_ids, $item_id)
		{
			return $this->dal->removeDepartmentsFromItem($dept_ids, $item_id);
		}

		public function getItemDepartmentLookupArray()
		{
			return $this->dal->getItemDepartmentLookupArray();
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
