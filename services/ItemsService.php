<?php
	class ItemsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new ShopDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function addItem($item)
		{
			return $this->dal->addItem($item);
		}

		public function getAllItems()
		{
			return $this->dal->getAllItems();
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
	}
