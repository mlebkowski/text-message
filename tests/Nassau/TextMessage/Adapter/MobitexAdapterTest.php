<?php

namespace Nassau\TextMessage\Adapter;

use Mobitex\Sender;
use Nassau\TextMessage\Message;
use Mobitex\Exception as MobitexException;
use Nassau\TextMessage\TestCase;

class MobitexAdapterTest extends TestCase
{
	public function testAdapterForwardsVerificationToMobitexSender()
	{
		$number = '48500456789';
		$phoneNumber = $this->stringToPhoneNumber($number);

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Mobitex\Sender $mock */
		$mock = $this->getMock('\\Mobitex\\Sender', ['verifyNumber'], [], '', false);
		$mock->expects($this->once())->method('verifyNumber')->with($this->equalTo($number));

		$sender = new MobitexAdapter($mock);
		$sender->verifyNumber($phoneNumber);
	}

	public function testMessageIsSentViaMobitexSender()
	{
		$number = '48500456789';
		$content = "lorem ipsum";
		$message = new Message($content);

		/** @var \PHPUnit_Framework_MockObject_MockObject|\Mobitex\Sender $mock */
		$mock = $this->getMock('\\Mobitex\\Sender', ['sendMessage'], [], '', false);
		$mock->expects($this->once())->method('sendMessage')->with(
			$this->equalTo($number),
			$this->equalTo($content),
			$this->equalTo(Message::TYPE_ASCII)
		);

		$sender = new MobitexAdapter($mock);
		$sender->send($message, $this->stringToPhoneNumber($number));
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
		$sender->send($message, $this->stringToPhoneNumber($number));
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
		$sender->send(new Message($message, $messageType), $this->getAnyPhoneNumber());

	}

	public function dataSourceMessageTypesAndContents()
	{
		return [
			[Message::TYPE_ASCII, Sender::TYPE_SMS, str_repeat('x', 100)],
			[Message::TYPE_ASCII, Sender::TYPE_CONCAT, str_repeat('x', 200)],
			[Message::TYPE_UNICODE, Sender::TYPE_UNICODE, str_repeat('x', 50)],
			[Message::TYPE_UNICODE, Sender::TYPE_UNICODE_CONCAT, str_repeat('x', 100)],
		];
	}

	public function testLongMessagesAreSplitted()
	{
		$message = new Message("");
		$maxLen = MobitexAdapter::MAX_LENGTH_ASCII;

		$contents = [
			str_pad("lorem ipsum", $maxLen, "x", STR_PAD_RIGHT),
			"ipsum lorem",
		];
		$text = implode("", $contents);

		/** @var \PHPUnit_Framework_MockObject_MockObject|Sender $senderMock */
		$senderMock = $this->getMock('\\Mobitex\\Sender', ['sendMessage'], [], '', false);
		$callNum = 0;

		$senderMock->expects($this->exactly(2))->method('sendMessage')->will($this->returnValue(true))
			->with($this->anything(), $this->callback(function($value) use (&$contents, &$callNum)
			{
				// phpunit calls the callback twice for every call :-/
				if (++$callNum % 2)
				{
					return true;
				}

				$this->assertEquals(array_shift($contents), $value);
				return true;
			}));

		$sender = new MobitexAdapter($senderMock);
		$sender->send(new Message($text), $this->getAnyPhoneNumber());
	}
	public function testMessageWithSpecialCharactersGetsSplitted()
	{
		$maxLen = MobitexAdapter::MAX_LENGTH_ASCII;
		$text = str_pad("[lorem ipsum]", $maxLen, "x", STR_PAD_RIGHT);

		/** @var \PHPUnit_Framework_MockObject_MockObject|Sender $senderMock */
		$senderMock = $this->getMock('\\Mobitex\\Sender', ['sendMessage'], [], '', false);
		$senderMock->expects($this->exactly(2))->method('sendMessage')->will($this->returnValue(true));

		$sender = new MobitexAdapter($senderMock);
		$sender->send(new Message($text), $this->getAnyPhoneNumber());
	}

}
 