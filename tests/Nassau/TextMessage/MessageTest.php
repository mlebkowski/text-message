<?php


namespace Nassau\TextMessage;


class MessageTest extends \PHPUnit_Framework_TestCase
{
	public function testUnicodeHasDifferentLengthThanAscii()
	{
		$ascii = new Message("", Message::TYPE_SMS);
		$unicode = new Message("", Message::TYPE_UNICODE);
		$this->assertNotEquals($unicode->getMaxLength(), $ascii->getMaxLength());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Unknown Text Message Type: unknown-type
	 */
	public function testCannotCreateMessageOfInvalidType()
	{
		new Message("", "unknown-type");
	}
}
