<?php
	declare(strict_types=1);

	class LuckyDipsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new LuckyDipsDAL();
		}

		public function closeConnexion() : void
		{
			$this->dal->closeConnexion();
		}

		public function verifyLuckyDipRequest(array $request) : LuckyDip
		{
			try
			{
				$luckyDip = null;

				if (!is_numeric($request['luckyDip_id']))
				{
					throw new Exception("Invalid LuckyDip ID");
				}

				return $this->getLuckyDipById(intval($request['luckyDip_id']));
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addLuckyDip(LuckyDip $luckyDip) : LuckyDip
		{
			try
			{
				return $this->dal->addLuckyDip($luckyDip);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getAllLuckyDips() : DalResult
		{
			return $this->dal->getAllLuckyDips();
		}

		// public function getAllLuckyDipsWithItems() : DalResult
		// {
		// 	return $this->dal->getAllLuckyDipsWithItems();
		// }

		public function getLuckyDipById(int $luckyDipId) : LuckyDip
		{
			try
			{
				$luckyDip = $this->dal->getLuckyDipById($luckyDipId);

				if (!($luckyDip instanceof LuckyDip))
				{
					throw new Exception("LuckyDip not found");
				}

				return $luckyDip;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getLuckyDipByName(string $luckyDipName) : LuckyDip
		{
			try
			{
				$luckyDip = $this->dal->getLuckyDipByName($luckyDipName);

				if (!($luckyDip instanceof LuckyDip))
				{
					throw new Exception("LuckyDip not found");
				}

				return $luckyDip;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function luckyDipDoesNotExist(string $luckyDipName) : bool
		{
			try
			{
				$luckyDip = $this->dal->getLuckyDipByName($luckyDipName);

				return !($luckyDip instanceof LuckyDip);
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function getLuckyDipsByListId(int $list_id) : array
		{
			try
			{
				$luckyDips = $this->dal->getLuckyDipsByListId($list_id);

				if (!is_array($luckyDips))
				{
					throw new Exception("Lucky Dips not found");
				}

				return $luckyDips;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function addItemToLuckyDip(Item $item, LuckyDip $luckyDip) : bool
		{
			try
			{
				$success = $this->dal->addItemToLuckyDip($item, $luckyDip);

				if (!$success)
				{
					throw new Exception("Error adding Item to LuckyDip");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeItemFromLuckyDip(Item $item) : bool
		{
			try
			{
				$success = $this->dal->removeItemFromLuckyDip($item);

				if (!$success)
				{
					throw new Exception("Error removing Item from LuckyDip");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function updateLuckyDip(LuckyDip $luckyDip) : bool
		{
			try
			{
				$success = $this->dal->updateLuckyDip($luckyDip);

				if (!$success)
				{
					throw new Exception("Error updating LuckyDip");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		public function removeLuckyDip(LuckyDip $luckyDip) : bool
		{
			try
			{
				$success = $this->dal->removeLuckyDip($luckyDip);

				if (!$success)
				{
					throw new Exception("Error removing LuckyDip");
				}

				return $success;
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}
