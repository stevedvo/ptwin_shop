<?php
	class Item
	{
		private $id;
		private $description;
		private $comments;
		private $default_qty;
		private $total_qty;
		private $last_ordered;
		private $selected;
		private $list_id;
		private $link;
		private $validation;

		public function __construct($id = null, $description = null, $comments = null, $default_qty = null, $total_qty = null, $last_ordered = null, $selected = null, $list_id = null, $link = null)
		{
			$this->id = $id;
			$this->description = $description;
			$this->comments = $comments;
			$this->default_qty = $default_qty;
			$this->total_qty = $total_qty;
			$this->last_ordered = $last_ordered;
			$this->selected = $selected;
			$this->list_id = $list_id;
			$this->link = $link;
			$this->validation =
			[
				'Description' =>
				[
					'required' => true
				]
			];
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

		public function getTotalQty()
		{
			return $this->total_qty;
		}

		public function setTotalQty($total_qty)
		{
			$this->total_qty = $total_qty;
		}

		public function getLastOrdered()
		{
			return $this->last_ordered;
		}

		public function setLastOrdered($last_ordered)
		{
			$this->last_ordered = $last_ordered;
		}

		public function getSelected()
		{
			return $this->selected;
		}

		public function setSelected($selected)
		{
			$this->selected = $selected;
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

			$validation = "";

			foreach ($this->validation[$property] as $key => $value)
			{
				$validation.= $key.":".$value."_";
			}

			$validation = rtrim($validation, "_");

			return $validation;
		}
	}
