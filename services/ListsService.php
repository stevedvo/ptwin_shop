<?php
	class ListsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new ListsDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function addList($list)
		{
			return $this->dal->addList($list);
		}

		public function getAllLists()
		{
			return $this->dal->getAllLists();
		}

		public function getAllListsWithItems()
		{
			return $this->dal->getAllListsWithItems();
		}

		public function getListById($list_id)
		{
			return $this->dal->getListById($list_id);
		}

		public function getListByName($list_name)
		{
			return $this->dal->getListByName($list_name);
		}

		public function addItemToList($item, $list)
		{
			return $this->dal->addItemToList($item, $list);
		}

		public function updateList($list)
		{
			return $this->dal->updateList($list);
		}

		public function removeList($list)
		{
			return $this->dal->removeList($list);
		}

		public function moveItemsToList($items, $list)
		{
			return $this->dal->moveItemsToList($items, $list);
		}
	}
