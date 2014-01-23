<?php


namespace Nassau\TextMessage;


use libphonenumber\PhoneNumberUtil;

class TestCase extends \PHPUnit_Framework_TestCase
{
	protected function stringToPhoneNumber($string)
	{
		return PhoneNumberUtil::getInstance()->parse($string, 'PL');
	}
	protected function getAnyPhoneNumber()
	{
		return $this->stringToPhoneNumber('500456789');
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|SenderInterface
	 */
	protected function getSenderMock()
	{
		return $this->getMock("\\Nassau\\TextMessage\\SenderInterface", ['send', 'verifyNumber']);
	}
}