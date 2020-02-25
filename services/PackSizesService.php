<?php
	class PackSizesService
	{
		private $dal;

		public function __construct()
		{
			$this->dal = new PackSizesDAL();
		}

		public function closeConnexion()
		{
			$this->dal->closeConnexion();
		}

		public function verifyPackSizeRequest($request)
		{
			$packsize = false;

			if (!is_numeric($request['packsize_id']))
			{
				return false;
			}

			$dalResult = $this->dal->getPackSizeById(intval($request['packsize_id']));

			if (!is_null($dalResult->getResult()))
			{
				$packsize = $dalResult->getResult();
			}

			if (!$packsize)
			{
				return false;
			}

			return $packsize;
		}

		public function getAllPackSizes()
		{
			return $this->dal->getAllPackSizes();
		}

		public function getPackSizeByName($packsize_name)
		{
			return $this->dal->getPackSizeByName($packsize_name);
		}

		public function getPackSizeByShortName($packsize_short_name)
		{
			return $this->dal->getPackSizeByShortName($packsize_short_name);
		}

		public function addPackSize($packsize)
		{
			return $this->dal->addPackSize($packsize);
		}

		public function getPackSizeById($packsize_id)
		{
			return $this->dal->getPackSizeById($packsize_id);
		}

		public function updatePackSize($packsize)
		{
			return $this->dal->updatePackSize($packsize);
		}

		// public function removePackSize($packsize)
		// {
		// 	return $this->dal->removePackSize($packsize);
		// }
	}
