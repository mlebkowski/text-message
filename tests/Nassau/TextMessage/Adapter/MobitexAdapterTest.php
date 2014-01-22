<?php

namespace Nassau\TextMessage\Adapter;

use Mobitex\Sender;
use Nassau\TextMessage\Message;
use Nassau\TextMessage\PhoneNumber;
use Mobitex\Exception as MobitexException;

class MobitexAdapterTest extends \PHPUnit_Framework_TestCase
{
	public function testAdapterForwardsVerificationToMobitexSender()
	{
		$number = '123456789';
		$phoneNumber = new PhoneNumber($number);

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Mobitex\Sender $mock */
		$mock = $this->getMock('\\Mobitex\\Sender', ['verifyNumber'], [], '', false);
		$mock->expects($this->once())->method('verifyNumber')->with($this->equalTo($number));

		$sender = new MobitexAdapter($mock);
		$sender->verifyNumber($phoneNumber);
	}

	public function testMessageIsSentViaMobitexSender()
	{
		$number = '123456789';
		$content = "lorem ipsum";
		$message = new Message($content);

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Mobitex\Sender $mock */
		$mock = $this->getMock('\\Mobitex\\Sender', ['sendMessage'], [], '', false);
		$mock->expects($this->once())->method('sendMessage')->with(
			$this->equalTo($number),
			$this->equalTo($content),
			$this->equalTo(Message::TYPE_SMS)
		);

		$sender = new MobitexAdapter($mock);
		$sender->send($message, new PhoneNumber($number));
	}

	/**
	 * @expectedException \Nassau\TextMessage\SenderException
	 */
	public function testSenderThrowsSenderExceptionOnError()
	{
		$number = '123456789';
		$content = "lorem ipsum";
		$message = new Message($content);

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Mobitex\Sender $mock */
		$mock = $this->getMock('\\Mobitex\\Sender', ['sendMessage'], [], '', false);
		$mock->expects($this->once())->method('sendMessage')->will(
			$this->throwException(new MobitexException)
		);

		$sender = new MobitexAdapter($mock);
		$sender->send($message, new PhoneNumber($number));
	}

	/**
	 * @dataProvider dataSourceMessageTypesAndContents
	 */
	public function testLongMessagesAreSentAsConcat($messageType, $expectedType, $message)
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|\Mobitex\Sender $mock */
		$mock = $this->getMock('\\Mobitex\\Sender', ['sendMessage'], [], '', false);
		$mock->expects($this->once())->method('sendMessage')->with(
			$this->anything(),
			$this->anything(),
			$this->equalTo($expectedType)
		);

		$sender = new MobitexAdapter($mock);
		$sender->send(new Message($message, $messageType), new PhoneNumber(0));

	}

	public function dataSourceMessageTypesAndContents()
	{
		return [
			[Message::TYPE_SMS, Sender::TYPE_SMS, str_repeat('x', 100)],
			[Message::TYPE_SMS, Sender::TYPE_CONCAT, str_repeat('x', 200)],
			[Message::TYPE_UNICODE, Sender::TYPE_UNICODE, str_repeat('x', 50)],
			[Message::TYPE_UNICODE, Sender::TYPE_UNICODE_CONCAT, str_repeat('x', 100)],
		];
	}
}
 