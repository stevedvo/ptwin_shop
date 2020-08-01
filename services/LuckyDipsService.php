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

		public function verifyLuckyDipRequest($request) : ?LuckyDip
		{
			$luckyDip = null;

			if (!is_numeric($request['luckyDip_id']))
			{
				return null;
			}

			$dalResult = $this->dal->getLuckyDipById(intval($request['luckyDip_id']));

			if ($dalResult->getResult() instanceof LuckyDip)
			{
				$luckyDip = $dalResult->getResult();
			}

			return $luckyDip;
		}

		public function addLuckyDip(LuckyDip $luckyDip) : DalResult
		{
			return $this->dal->addLuckyDip($luckyDip);
		}

		public function getAllLuckyDips()
		{
			return $this->dal->getAllLuckyDips();
		}

		public function getAllLuckyDipsWithItems()
		{
			return $this->dal->getAllLuckyDipsWithItems();
		}

		public function getLuckyDipById($dept_id)
		{
			return $this->dal->getLuckyDipById($dept_id);
		}

		public function getLuckyDipByName(string $luckyDip_name) : DalResult
		{
			return $this->dal->getLuckyDipByName($luckyDip_name);
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

		public function removeLuckyDip($luckyDip)
		{
			return $this->dal->removeLuckyDip($luckyDip);
		}

		public function getPrimaryLuckyDips()
		{
			return $this->dal->getPrimaryLuckyDips();
		}
	}
