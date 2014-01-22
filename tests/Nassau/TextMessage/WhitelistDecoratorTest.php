<?php

namespace Nassau\TextMessage;

class WhitelistDecoratorTest extends \PHPUnit_Framework_TestCase
{
	public function testWhitelistDoesNotCallSender()
	{
		$phone = 600100200;

		/** @var SenderInterface|\PHPUnit_Framework_MockObject_MockObject $sender */
		$sender = $this->getMock('\\Nassau\\TextMessage\\SenderInterface', ['send', 'verifyNumber']);
		$sender->expects($this->never())->method('send');
		$sender = new WhitelistDecorator($sender, [$phone]);

		$sender->send(new Message(""), new PhoneNumber(123456789));

	}
	public function testWhitelistPassesRightNumber()
	{
		$phone = 600100200;

		/** @var SenderInterface|\PHPUnit_Framework_MockObject_MockObject $sender */
		$sender = $this->getMock('\\Nassau\\TextMessage\\SenderInterface', ['send', 'verifyNumber']);
		$sender->expects($this->once())->method('send');
		$sender = new WhitelistDecorator($sender, [$phone]);

		$sender->send(new Message(""), new PhoneNumber($phone));

	}
}
 