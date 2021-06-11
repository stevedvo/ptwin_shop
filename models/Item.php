<?php
	class Item
	{
		private $id;
		private $description;
		private $comments;
		private $defaultQty;
		private $listId;
		private $link;
		private $primaryDept;
		private $muteTemp;
		private $mutePerm;
		private $packSizeId;
		private $luckyDipId;
		private $mealPlanCheck;
		private $validation;
		private $departments;
		private $orders;
		private $recentOrders;
		private $packSize;
		private $dailyConsumptionOverall;
		private $dailyConsumptionRecent;
		private $mealItems;

		public function __construct($id = null, $description = null, $comments = null, $defaultQty = null, $listId = null, $link = null, $primaryDept = null, $muteTemp = null, $mutePerm = null, $packSizeId = null, $luckyDipId = null, $mealPlanCheck = null, $departments = null, $orders = null, $recentOrders = null, $packSize = null, $dailyConsumptionOverall = null, $dailyConsumptionRecent = null, $mealItems = [])
		{
			$this->id = $id;
			$this->description = $description;
			$this->comments = $comments;
			$this->defaultQty = $defaultQty;
			$this->listId = $listId;
			$this->link = $link;
			$this->primaryDept = $primaryDept;
			$this->muteTemp = $muteTemp;
			$this->mutePerm = $mutePerm;
			$this->packSizeId = $packSizeId;
			$this->luckyDipId = $luckyDipId;
			$this->mealPlanCheck = $mealPlanCheck;
			$this->validation =
			[
				'Description' => ['required' => true],
				'DefaultQty'  =>
				[
					'required'  => true,
					'min-value' => 1
				],
				'ListId'      => ['required' => true],
				'PackSizeId'  => ['required' => true]
			];
			$this->departments = $departments;
			$this->orders = $orders;
			$this->recentOrders = $recentOrders;
			$this->packSize = $packSize;
			$this->dailyConsumptionOverall = $dailyConsumptionOverall;
			$this->dailyConsumptionRecent = $dailyConsumptionRecent;
			$this->mealItems = $mealItems;
		}

		public function jsonSerialize()
		{
			return get_object_vars($this);
		}

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id = $id;
		}

		public function getDescription()
		{
			return $this->description;
		}

		public function setDescription($description)
		{
			$this->description = $description;
		}

		public function getComments()
		{
			return $this->comments;
		}

		public function setComments($comments)
		{
			$this->comments = $comments;
		}

		public function getDefaultQty()
		{
			return $this->defaultQty;
		}

		public function setDefaultQty($defaultQty)
		{
			$this->defaultQty = $defaultQty;
		}

		public function getListId()
		{
			return $this->listId;
		}

		public function setListId($listId)
		{
			$this->listId = $listId;
		}

		public function getLink()
		{
			return $this->link;
		}

		public function setLink($link)
		{
			$this->link = $link;
		}

		public function getPrimaryDept()
		{
			return $this->primaryDept;
		}

		public function setPrimaryDept($primaryDept)
		{
			$this->primaryDept = $primaryDept;
		}

		public function getMuteTemp()
		{
			return $this->muteTemp;
		}

		public function setMuteTemp($muteTemp)
		{
			$this->muteTemp = $muteTemp;
		}

		public function getMutePerm()
		{
			return $this->mutePerm;
		}

		public function setMutePerm($mutePerm)
		{
			$this->mutePerm = $mutePerm;
		}

		public function getPackSizeId()
		{
			return $this->packSizeId;
		}

		public function setPackSizeId($packSizeId)
		{
			$this->packSizeId = $packSizeId;
		}

		public function getLuckyDipId()
		{
			return $this->luckyDipId;
		}

		public function setLuckyDipId($luckyDipId)
		{
			$this->luckyDipId = $luckyDipId;
		}

		public function getMealPlanCheck() : bool
		{
			return $this->mealPlanCheck;
		}

		public function setMealPlanCheck(bool $mealPlanCheck) : void
		{
			$this->mealPlanCheck = $mealPlanCheck;
		}

		public function getValidation($property = null)
		{
			if (is_null($property))
			{
				return $this->validation;
			}

			if (!isset($this->validation[$property]))
			{
				return false;
			}

			return getValidationString($this, $property);
		}

		public function getDepartments()
		{
			return $this->departments;
		}

		public function setDepartments($departments)
		{
			$this->departments = $departments;
		}

		public function addDepartment($department)
		{
			if (!is_array($this->departments))
			{
				$this->departments = [];
			}

			$this->departments[$department->getId()] = $department;
		}

		public function getOrders()
		{
			return $this->orders;
		}

		public function setOrders(array $orders) : void
		{
			$this->orders = $orders;
		}

		public function addOrder($order)
		{
			if (!is_array($this->orders))
			{
				$this->orders = [];
			}

			$this->orders[$order->getId()] = $order;
		}

		public function hasOrder($order_id)
		{
			if (!$this->hasOrders())
			{
				return false;
			}

			if (!array_key_exists($order_id, $this->orders))
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		public function hasOrders() : bool
		{
			if (!is_array($this->orders))
			{
				return false;
			}

			if (count($this->orders) == 0)
			{
				return false;
			}

			return true;
		}

		public function getTotalOrdered()
		{
			$total = 0;

			if (is_array($this->orders))
			{
				foreach ($this->orders as $order_id => $order)
				{
					$order_item = $order->getOrderItemByItemId($this->id);

					if ($order_item)
					{
						$total+= $order_item->getQuantity();
					}
				}
			}

			return $total;
		}

		public function getFirstOrder() : ?Order
		{
			$firstOrder = null;

			if ($this->hasOrders())
			{
				foreach ($this->orders as $order_id => $order)
				{
					if (!($firstOrder instanceof Order))
					{
						$firstOrder = $order;
					}
					else
					{
						if ($order->getDateOrdered() < $firstOrder->getDateOrdered())
						{
							$firstOrder = $order;
						}
					}
				}
			}

			return $firstOrder;
		}

		public function getLastOrder() : ?Order
		{
			$lastOrder = null;

			if ($this->hasOrders())
			{
				foreach ($this->orders as $order_id => $order)
				{
					if (!($lastOrder instanceof Order))
					{
						$lastOrder = $order;
					}
					else
					{
						if ($order->getDateOrdered() > $lastOrder->getDateOrdered())
						{
							$lastOrder = $order;
						}
					}
				}
			}

			return $lastOrder;
		}

		// public function getPenultimateOrder()
		// {
		// 	$penultimate_order = $last_order = false;

		// 	if (is_array($this->orders))
		// 	{
		// 		foreach ($this->orders as $order_id => $order)
		// 		{
		// 			if (!$last_order)
		// 			{
		// 				$last_order = $order;
		// 			}
		// 			else
		// 			{
		// 				if ($order->getDateOrdered() > $last_order->getDateOrdered())
		// 				{
		// 					$penultimate_order = $last_order;
		// 					$last_order = $order;
		// 				}
		// 				else
		// 				{
		// 					if (!$penultimate_order)
		// 					{
		// 						$penultimate_order = $order;
		// 					}
		// 					else
		// 					{
		// 						if ($order->getDateOrdered() > $penultimate_order->getDateOrdered())
		// 						{
		// 							$penultimate_order = $order;
		// 						}
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}

		// 	return $penultimate_order;
		// }

		public function getRecentOrders()
		{
			if (!$this->recentOrders)
			{
				$this->calculateRecentOrders();
			}

			return $this->recentOrders;
		}

		public function setRecentOrders($orders)
		{
			$this->recentOrders = $orders;
		}

		public function addRecentOrder($order)
		{
			if (!is_array($this->recentOrders))
			{
				$this->recentOrders = [];
			}

			$this->recentOrders[$order->getId()] = $order;
		}

		public function calculateRecentOrders(int $interval = DEFAULT_CONSUMPTION_INTERVAL, string $period = DEFAULT_CONSUMPTION_PERIOD) : void
		{
			$cutoffDate = new DateTime();
			$cutoffDate->modify("-".$interval." ".$period);
			$break = false;

			if ($this->hasOrders())
			{
				foreach ($this->orders as $orderId => $order)
				{
					if ($order->getDateOrdered() instanceof DateTime)
					{
						if (!$break)
						{
							$this->addRecentOrder($order);
							$interval = $order->getDateOrdered()->diff($cutoffDate);

							if (!$interval->invert)
							{
								$break = true;
							}
						}
					}
				}
			}
		}

		public function getPackSize()
		{
			return $this->packSize;
		}

		public function setPackSize($packSize)
		{
			$this->packSize = $packSize;
		}

		public function getPackSizeShortName()
		{
			$packSizeShortName = null;
			$packSize = $this->getPackSize();

			if ($packSize instanceof PackSize)
			{
				$packSizeShortName = $packSize->getShortName();
			}

			return $packSizeShortName;
		}

		public function calculateDailyConsumptionOverall() : void
		{
			$consumption = $firstOrder = null;

			$firstOrder = $this->getFirstOrder();

			if ($firstOrder instanceof Order)
			{
				$firstOrderDate = $firstOrder->getDateOrdered();

				/* First Order could be current Order */
				if (!is_null($firstOrderDate))
				{
					$days = $firstOrder->getDateOrdered()->diff(new DateTime)->format('%a');

					if ($days != '0')
					{
						$total = $this->getTotalOrdered();
						$consumption = $total / $days;
					}
				}
			}

			$this->dailyConsumptionOverall = $consumption;
		}

		public function getDailyConsumptionOverall() : ?float
		{
			if (is_null($this->dailyConsumptionOverall))
			{
				$this->calculateDailyConsumptionOverall();
			}

			return $this->dailyConsumptionOverall;
		}

		public function calculateDailyConsumptionRecent() : void
		{
			$from_date = $to_date = $total_recent_qty = $latest_recent_qty = $consumption = null;

			if ($this->getRecentOrders())
			{
				foreach ($this->recentOrders as $order_id => $order)
				{
					if (is_null($from_date))
					{
						$from_date = $order->getDateOrdered();
						$to_date = $order->getDateOrdered();
						$total_recent_qty = $order->getOrderItemByItemId($this->id)->getQuantity();
						$latest_recent_qty = $order->getOrderItemByItemId($this->id)->getQuantity();
					}
					else
					{
						$total_recent_qty+= $order->getOrderItemByItemId($this->id)->getQuantity();

						if ($from_date->diff($order->getDateOrdered())->invert)
						{
							$from_date = $order->getDateOrdered();
						}
						else
						{
							$to_date = $order->getDateOrdered();
							$latest_recent_qty = $order->getOrderItemByItemId($this->id)->getQuantity();
						}
					}
				}

				// $days = $from_date->diff($to_date)->format('%a');
				$days = $from_date->diff(new DateTime)->format('%a');

				if ($days != '0')
				{
					// $total = $total_recent_qty - $latest_recent_qty;
					$total = $total_recent_qty;
					$consumption = $total / $days;
				}
			}

			$this->dailyConsumptionRecent = $consumption;
		}

		public function getDailyConsumptionRecent() : ?float
		{
			if (is_null($this->dailyConsumptionRecent))
			{
				$this->calculateDailyConsumptionRecent();
			}

			return $this->dailyConsumptionRecent;
		}

		public function getStockLevelPrediction(int $daysAhead = 0, string $consumption = 'overall') : ?int
		{
			$stockLevel = $dailyConsumption = null;

			switch ($consumption)
			{
				case 'overall':
					$dailyConsumption = $this->getDailyConsumptionOverall();
					break;
				case 'recent':
					$dailyConsumption = $this->getDailyConsumptionRecent();
					break;
			}

			if (is_null($dailyConsumption))
			{
				return null;
			}

			$lastOrder = $this->getLastOrder();

			if (!($lastOrder instanceof Order))
			{
				return null;
			}

			$lastOrderQuantity = intval($lastOrder->getOrderItemByItemId($this->id)->getQuantity());
			$daysElapsed = $lastOrder->getDateOrdered()->diff(new DateTime())->format('%a');
			$days = $daysElapsed + $daysAhead;
			$estConsumption = intval(round($dailyConsumption * $days));
			$stockLevel = $lastOrderQuantity - $estConsumption;

			return $stockLevel;
		}

		public function getMealItems() : array
		{
			if (!is_array($this->mealItems))
			{
				$this->mealItems = [];
			}

			return $this->mealItems;
		}

		public function setMealItems(array $mealItems) : void
		{
			$this->mealItems = $mealItems;
		}

		public function addMealItem(string $dateString, MealItem $mealItem) : void
		{
			$this->getMealItems();

			$this->mealItems[$dateString] = $mealItem;
		}

		public function hasMealItem(string $dateString) : bool
		{
			if (!$this->hasMealItems())
			{
				return false;
			}

			return array_key_exists($dateString, $this->mealItems);
		}

		public function hasMealItems() : bool
		{
			return count($this->getMealItems()) > 0;
		}

		public function hasUpcomingMealItems() : bool
		{
			if (!$this->hasMealItems())
			{
				return false;
			}

			$mealPlanDates = array_keys($this->getMealItems());
			rsort($mealPlanDates);
			$now = new DateTimeImmutable();
			$upcomingLimit = $now->modify("+14 day");

			foreach ($mealPlanDates as $mealPlanDate)
			{
				if ($mealPlanDate > $now->format('Y-m-d') && $mealPlanDate < $upcomingLimit->format('Y-m-d'))
				{
					return true;
				}
			}

			return false;
		}

		public function getUpcomingMealItems() : array
		{
			$upcomingMealItems = [];
			$now = new DateTimeImmutable();
			$upcomingLimit = $now->modify("+14 day");

			foreach ($this->getMealItems() as $dateString => $mealItem)
			{
				if ($dateString > $now->format('Y-m-d') && $dateString < $upcomingLimit->format('Y-m-d'))
				{
					$upcomingMealItems[$dateString] = $mealItem;
				}
			}

			return $upcomingMealItems;
		}
	}
