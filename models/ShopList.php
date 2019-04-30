<?php
	class ShopList
	{
		private $id;
		private $name;
		private $validation;

		public function __construct($id = null, $name = null)
		{
			$this->id = $id;
			$this->name = $name;
			$this->validation =
			[
				'name' =>
				[
					'required' => true,
					'min-length' => 5,
					'max-length' => 10
				]
			];
		}

		public function getId()
		{
			return $this->id;
		}

		public function setId($id)
		{
			$this->id - $id;
		}

		public function getName()
		{
			return $this->name;
		}

		public function setName($name)
		{
			$this->name = $name;
		}

		public function getValidation($property = null)
		{
			if (is_null($property) || !isset($this->validation[$property]))
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
