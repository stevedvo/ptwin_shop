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

		public function verifyListRequest(array $request) : ShopList
		{
			try
			{
				$list = null;

				if (!is_numeric($request['list_id']))
				{
					throw new Exception("Invalid List ID");
				}

				$list = $this->dal->getListById(intval($request['list_id']));

				if (!($list instanceof ShopList))
				{
					throw new Exception("List not found");
				}

				return $list;
			}
			catch (Exception $e)
			{
				throw $e;
			}
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

		public function addItemToList(Item $item, ShopList $list) : bool
		{
			try
			{
				return $this->dal->addItemToList($item, $list);
			}
			catch (Exception $e)
			{
				throw $e;
			}
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
