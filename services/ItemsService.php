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

		public function verifyItemRequest($request)
		{
			$item = false;

			if (!is_numeric($request['item_id']))
			{
				return false;
			}

			$dalResult = $this->dal->getItemById(intval($request['item_id']));

			if (!is_null($dalResult->getResult()))
			{
				$item = $dalResult->getResult();
			}

			if (!$item)
			{
				return false;
			}

			return $item;
		}

		public function addItem($item)
		{
			return $this->dal->addItem($item);
		}

		public function getAllItems()
		{
			return $this->dal->getAllItems();
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

		public function getItemById($item_id)
		{
			return $this->dal->getItemById($item_id);
		}

		public function getItemsById($item_ids)
		{
			return $this->dal->getItemsById($item_ids);
		}

		public function getItemByDescription($description)
		{
			return $this->dal->getItemByDescription($description);
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

		public function resetMuteTemps()
		{
			return $this->dal->resetMuteTemps();
		}
	}
