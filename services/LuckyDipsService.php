<?php
	declare(strict_types=1);

	class LuckyDipsService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new LuckyDipsDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function verifyLuckyDipRequest($request)
		{
			$luckyDip = false;

			if (!is_numeric($request['luckyDip_id']))
			{
				return false;
			}

			$dalResult = $this->dal->getLuckyDipById(intval($request['luckyDip_id']));

			if ($dalResult->getResult() instanceof LuckyDip)
			{
				$luckyDip = $dalResult->getResult();
			}

			if (!$luckyDip)
			{
				return false;
			}

			return $luckyDip;
		}

		public function addLuckyDip($luckyDip)
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

		public function getLuckyDipByName($dept_name)
		{
			return $this->dal->getLuckyDipByName($dept_name);
		}

		public function addItemToLuckyDip($item, $luckyDip)
		{
			return $this->dal->addItemToLuckyDip($item, $luckyDip);
		}

		public function removeItemsFromLuckyDip($item_ids, $dept_id)
		{
			return $this->dal->removeItemsFromLuckyDip($item_ids, $dept_id);
		}

		public function updateLuckyDip($luckyDip)
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
