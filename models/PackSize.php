<?php
	class PackSize
	{
		private $id;
		private $name;
		private $short_name;
		private $validation;

		public function __construct($id = null, $name = null, $short_name = null)
		{
			$this->id = $id;
			$this->name = $name;
			$this->short_name = $short_name;
			$this->validation =
			[
				'Name'      => ['required' => true],
				'ShortName' => ['required' => true]
			];
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

		public function getName()
		{
			return $this->name;
		}

		public function setName($name)
		{
			$this->name = $name;
		}

		public function getShortName()
		{
			return $this->short_name;
		}

		public function setShortName($short_name)
		{
			$this->short_name = $short_name;
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
	}
