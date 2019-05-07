<?php
	class DalResult
	{
		private $result;
		private $exception;

		public function __construct($result = null, $exception = null)
		{
			$this->result = $result;
			$this->exception = $exception;
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
	}
