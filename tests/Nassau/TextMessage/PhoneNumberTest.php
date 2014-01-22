<?php


namespace Nassau\TextMessage;


class PhoneNumberTest extends \PHPUnit_Framework_TestCase
{
	public function testPhoneNumberStipsNonDigits()
	{
		$this->assertEquals("500500500", (new PhoneNumber("x500a500b500c")));
	}
}
