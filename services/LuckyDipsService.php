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

		public function verifyLuckyDipRequest($request) : LuckyDip
		{
			try
			{
				$luckyDip = null;

				if (!is_numeric($request['luckyDip_id']))
				{
					throw new Exception("Invalid LuckyDip ID");
				}

				$luckyDip = $this->dal->getLuckyDipById(intval($request['luckyDip_id']));

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

		public function getLuckyDipById($luckyDip_id) : DalResult
		{
			return $this->dal->getLuckyDipById($luckyDip_id);
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

		public function addItemToLuckyDip(Item $item, LuckyDip $luckyDip) : DalResult
		{
			return $this->dal->addItemToLuckyDip($item, $luckyDip);
		}

		public function removeItemFromLuckyDip(Item $item, LuckyDip $luckyDip) : DalResult
		{
			return $this->dal->removeItemFromLuckyDip($item, $luckyDip);
		}

		public function updateLuckyDip(LuckyDip $luckyDip) : DalResult
		{
			return $this->dal->updateLuckyDip($luckyDip);
		}

		public function removeLuckyDip(LuckyDip $luckyDip) : DalResult
		{
			return $this->dal->removeLuckyDip($luckyDip);
		}
	}
