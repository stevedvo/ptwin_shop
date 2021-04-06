<?php
	declare(strict_types=1);

	class SelectListItem
	{
		private int $value;
		private string $text;
		private array $dataAttributes;

		public function __construct(int $value, string $text, array $dataAttributes = [])
		{
			$this->value = $value;
			$this->text = $text;
			$this->dataAttributes = $dataAttributes;
		}

		public function jsonSerialize() : array
		{
			return get_object_vars($this);
		}

		public function getValue() : int
		{
			return $this->value;
		}

		public function setValue(int $value) : void
		{
			$this->value = $value;
		}

		public function getText() : string
		{
			return $this->text;
		}

		public function setText(string $text) : void
		{
			$this->text = $text;
		}

		public function getDataAttributes() : array
		{
			return $this->dataAttributes;
		}

		public function setDataAttributes(array $dataAttributes) : void
		{
			$this->dataAttributes = $dataAttributes;
		}

		public function addDataAttribute(array $dataAttribute) : void
		{
			$this->dataAttributes[$dataAttribute['key']] = $dataAttribute['value'];
		}

		public function getDataAttributesString() : string
		{
			$dataAttributesString = "";

			foreach ($this->getDataAttributes() as $dataAttribute)
			{
				$dataAttributesString.= "data-".$dataAttribute['key']."='".$dataAttribute['value']."' ";
			}

			return $dataAttributesString;
		}
	}
