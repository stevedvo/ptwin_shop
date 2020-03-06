<?php
	class Item
	{
		private $id;
		private $description;
		private $comments;
		private $default_qty;
		private $list_id;
		private $link;
		private $primary_dept;
		private $mute_temp;
		private $mute_perm;
		private $packsize_id;
		private $validation;
		private $departments;
		private $orders;
		private $recent_orders;
		private $packsize;
		private $daily_consumption_overall;
		private $daily_consumption_recent;

		public function __construct($id = null, $description = null, $comments = null, $default_qty = null, $list_id = null, $link = null, $primary_dept = null, $mute_temp = null, $mute_perm = null, $packsize_id = null, $departments = null, $orders = null, $recent_orders = null, $packsize = null, $daily_consumption_overall = null, $daily_consumption_recent = null)
		{
			$this->id = $id;
			$this->description = $description;
			$this->comments = $comments;
			$this->default_qty = $default_qty;
			$this->list_id = $list_id;
			$this->link = $link;
			$this->primary_dept = $primary_dept;
			$this->mute_temp = $mute_temp;
			$this->mute_perm = $mute_perm;
			$this->packsize_id = $packsize_id;
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
			$this->recent_orders = $recent_orders;
			$this->packsize = $packsize;
			$this->daily_consumption_overall = $daily_consumption_overall;
			$this->daily_consumption_recent = $daily_consumption_recent;
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
			return $this->default_qty;
		}

		public function setDefaultQty($default_qty)
		{
			$this->default_qty = $default_qty;
		}

		public function getListId()
		{
			return $this->list_id;
		}

		public function setListId($list_id)
		{
			$this->list_id = $list_id;
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
			return $this->primary_dept;
		}

		public function setPrimaryDept($primary_dept)
		{
			$this->primary_dept = $primary_dept;
		}

		public function getMuteTemp()
		{
			return $this->mute_temp;
		}

		public function setMuteTemp($mute_temp)
		{
			$this->mute_temp = $mute_temp;
		}

		public function getMutePerm()
		{
			return $this->mute_perm;
		}

		public function setMutePerm($mute_perm)
		{
			$this->mute_perm = $mute_perm;
		}

		public function getPackSizeId()
		{
			return $this->packsize_id;
		}

		public function setPackSizeId($packsize_id)
		{
			$this->packsize_id = $packsize_id;
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

		public function setOrders($orders)
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

		public function hasOrders()
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
					$order_item = $order->getOrderItembyItemId($this->id);

					if ($order_item)
					{
						$total+= $order_item->getQuantity();
					}
				}
			}

			return $total;
		}

		public function getFirstOrder()
		{
			$first_order = false;

			if (is_array($this->orders))
			{
				foreach ($this->orders as $order_id => $order)
				{
					if (!$first_order)
					{
						$first_order = $order;
					}
					else
					{
						if ($order->getDateOrdered() < $first_order->getDateOrdered())
						{
							$first_order = $order;
						}
					}
				}
			}

			return $first_order;
		}

		public function getLastOrder()
		{
			$last_order = false;

			if (is_array($this->orders))
			{
				foreach ($this->orders as $order_id => $order)
				{
					if (!$last_order)
					{
						$last_order = $order;
					}
					else
					{
						if ($order->getDateOrdered() > $last_order->getDateOrdered())
						{
							$last_order = $order;
						}
					}
				}
			}

			return $last_order;
		}

		public function getPenultimateOrder()
		{
			$penultimate_order = $last_order = false;

			if (is_array($this->orders))
			{
				foreach ($this->orders as $order_id => $order)
				{
					if (!$last_order)
					{
						$last_order = $order;
					}
					else
					{
						if ($order->getDateOrdered() > $last_order->getDateOrdered())
						{
							$penultimate_order = $last_order;
							$last_order = $order;
						}
						else
						{
							if (!$penultimate_order)
							{
								$penultimate_order = $order;
							}
							else
							{
								if ($order->getDateOrdered() > $penultimate_order->getDateOrdered())
								{
									$penultimate_order = $order;
								}
							}
						}
					}
				}
			}

			return $penultimate_order;
		}

		public function getRecentOrders()
		{
			if (!$this->recent_orders)
			{
				$this->calculateRecentOrders();
			}

			return $this->recent_orders;
		}

		public function setRecentOrders($orders)
		{
			$this->recent_orders = $orders;
		}

		public function addRecentOrder($order)
		{
			if (!is_array($this->recent_orders))
			{
				$this->recent_orders = [];
			}

			$this->recent_orders[$order->getId()] = $order;
		}

		public function calculateRecentOrders($interval = 3, $period = "month")
		{
			$cutoff_date = new DateTime();
			$cutoff_date->modify("-".$interval." ".$period);
			$break = false;

			if ($this->hasOrders())
			{

			var_debug($interval);
			var_debug($period);
			var_debug($cutoff_date);exit;
				foreach ($this->orders as $order_id => $order)
				{
					if (!$break)
					{
						$this->addRecentOrder($order);
						$interval = $order->getDateOrdered()->diff($cutoff_date);

						if (!$interval->invert)
						{
							$break = true;
						}
					}
				}
			}
		}

		public function getPackSize()
		{
			return $this->packsize;
		}

		public function setPackSize($packsize)
		{
			$this->packsize = $packsize;
		}

		public function getPackSizeShortName()
		{
			$packSizeShortName = null;
			$packsize = $this->getPackSize();

			if ($packsize instanceof PackSize)
			{
				$packSizeShortName = $packsize->getShortName();
			}

			return $packSizeShortName;
		}

		public function calculateDailyConsumptionOverall()
		{
			$consumption = $first_order = $last_order = false;

			$first_order = $this->getFirstOrder();
			$last_order = $this->getLastOrder();

			if ($first_order && $last_order)
			{
				$days = $first_order->getDateOrdered()->diff($last_order->getDateOrdered())->format('%a');

				if ($days != '0')
				{
					$total = $this->getTotalOrdered() - $last_order->getOrderItembyItemId($this->id)->getQuantity();
					$consumption = $total / $days;
				}
			}

			$this->daily_consumption_overall = $consumption;
		}

		public function getDailyConsumptionOverall()
		{
			if (!$this->daily_consumption_overall)
			{
				$this->calculateDailyConsumptionOverall();
			}

			return $this->daily_consumption_overall;
		}

		public function calculateDailyConsumptionRecent()
		{
			$from_date = $to_date = $total_recent_qty = $latest_recent_qty = $consumption = null;

			if ($this->getRecentOrders())
			{
				foreach ($this->recent_orders as $order_id => $order)
				{
					if (is_null($from_date))
					{
						$from_date = $order->getDateOrdered();
						$to_date = $order->getDateOrdered();
						$total_recent_qty = $order->getOrderItembyItemId($this->id)->getQuantity();
						$latest_recent_qty = $order->getOrderItembyItemId($this->id)->getQuantity();
					}
					else
					{
						$total_recent_qty+= $order->getOrderItembyItemId($this->id)->getQuantity();

						if ($from_date->diff($order->getDateOrdered())->invert)
						{
							$from_date = $order->getDateOrdered();
						}
						else
						{
							$to_date = $order->getDateOrdered();
							$latest_recent_qty = $order->getOrderItembyItemId($this->id)->getQuantity();
						}
					}
				}

				$days = $from_date->diff($to_date)->format('%a');

				if ($days != '0')
				{
					$total = $total_recent_qty - $latest_recent_qty;
					$consumption = $total / $days;
				}
			}

			$this->daily_consumption_recent = $consumption;
		}

		public function getDailyConsumptionRecent()
		{
			if (!$this->daily_consumption_recent)
			{
				$this->calculateDailyConsumptionRecent();
			}

			return $this->daily_consumption_recent;
		}

		public function getStockLevelPrediction($days_ahead = 0, $consumption = 'overall')
		{
			$stock_level = $daily_consumption = false;

			switch ($consumption)
			{
				case 'overall':
					$daily_consumption = $this->getDailyConsumptionOverall();
					break;
				case 'recent':
					$daily_consumption = $this->getDailyConsumptionRecent();
					break;
			}

			if (!$daily_consumption)
			{
				return false;
			}

			$last_order = $this->getLastOrder();
			$last_order_qty = $last_order->getOrderItembyItemId($this->id)->getQuantity();
			$days_elapsed = $last_order->getDateOrdered()->diff(new DateTime())->format('%a');
			$days = $days_elapsed + $days_ahead;
			$est_consumption = round($daily_consumption * $days);
			$stock_level = $last_order_qty - $est_consumption;

			return $stock_level;
		}
	}
