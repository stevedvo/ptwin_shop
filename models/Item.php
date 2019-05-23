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
		private $validation;
		private $departments;

		public function __construct($id = null, $description = null, $comments = null, $default_qty = null, $list_id = null, $link = null, $primary_dept = null, $departments = null)
		{
			$this->id = $id;
			$this->description = $description;
			$this->comments = $comments;
			$this->default_qty = $default_qty;
			$this->list_id = $list_id;
			$this->link = $link;
			$this->primary_dept = $primary_dept;
			$this->validation =
			[
				'Description' => ['required' => true],
				'DefaultQty' =>
				[
					'required' => true,
					'min-value' => 1
				],
				'ListId' => ['required' => true]
			];
			$this->departments = $departments;
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
	}
