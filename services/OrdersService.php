<?php
	class OrdersService
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

		public function getCurrentOrder()
		{
			$order = false;
			$dalResult = $this->dal->getCurrentOrder();

			if ($dalResult->getResult() instanceof Order)
			{
				$order = $dalResult->getResult();
			}

			if (!$order)
			{
				$order = new Order();
				$dalResult = $this->dal->addOrder($order);

				if (!is_null($dalResult->getResult()))
				{
					$order->setId($dalResult->getResult());
				}
			}

			return $order;
		}

		public function addOrder($order)
		{
			return $this->dal->addOrder($order);
		}

		public function addOrderItem($order_item)
		{
			$dalResult = $this->dal->getOrderItemByOrderAndItem($order_item->getOrderId(), $order_item->getItemId());

			if ($dalResult->getResult())
			{
				return $dalResult->getResult();
			}

			return $this->dal->addOrderItem($order_item);
		}
	}
