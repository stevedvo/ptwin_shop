<?php
	class DalResult
	{
		private $result;
		private $exception;
		private $partial_view;

		public function __construct($result = null, $exception = null, $partial_view = null)
		{
			$this->result = $result;
			$this->exception = $exception;
			$this->partial_view = $partial_view;
		}

		public function jsonSerialize()
		{
			return get_object_vars($this);
		}

		public function getResult()
		{
			return $this->result;
		}

		public function setResult($result)
		{
			$this->result = $result;
		}

		public function getException()
		{
			return $this->exception;
		}

		public function setException($exception)
		{
			$this->exception = $exception;
		}

		public function getPartialView()
		{
			return $this->partial_view;
		}

		public function setPartialView(string $partial_view) : void
		{
			$this->partial_view = $partial_view;
		}
	}
