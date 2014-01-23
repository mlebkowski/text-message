<?php

namespace Nassau\TextMessage;

use libphonenumber\PhoneNumber;

class WhitelistDecoratorTest extends TestCase
{
	public function testWhitelistDoesNotCallSender()
	{
		$phone = "600100200";

		/** @var SenderInterface|\PHPUnit_Framework_MockObject_MockObject $sender */
		$sender = $this->getMock('\\Nassau\\TextMessage\\SenderInterface', ['send', 'verifyNumber']);
		$sender->expects($this->never())->method('send');
		$sender = new WhitelistDecorator($sender, [$this->stringToPhoneNumber($phone)]);

		$sender->send(new Message(""), $this->stringToPhoneNumber("123456789"));

	}
	public function testWhitelistPassesRightNumber()
	{
		$phone = "600100200";

		/** @var SenderInterface|\PHPUnit_Framework_MockObject_MockObject $sender */
		$sender = $this->getMock('\\Nassau\\TextMessage\\SenderInterface', ['send', 'verifyNumber']);
		$sender->expects($this->once())->method('send');
		$sender = new WhitelistDecorator($sender, [$this->stringToPhoneNumber($phone)]);

		$sender->send(new Message(""), $this->stringToPhoneNumber($phone));

	}
}
 