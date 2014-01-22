<?php


namespace Nassau\TextMessage;

class PhoneNumber
{
	/**
	 * @var string
	 */
	private $number;

	/**
	 * @param string $number
	 */
	public function __construct($number)
	{
		$this->number = preg_replace('/[^\d]/', '', $number);
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	public function equals(PhoneNumber $number)
	{
		return $this->number === $number->getNumber();
	}

	public function __toString()
	{
		return $this->getNumber();
	}
}