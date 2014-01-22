<?php

namespace Nassau\TextMessage;

use Nassau\TextMessage\UnicodeRemover\UnicodeRemoverInterface;

class TextMessageSenderTest extends \PHPUnit_Framework_TestCase
{
	public function testUnicodeRemoverIsUsed()
	{
		$text = "lorem ipsum";
		$stripped = "ipsum lorem";
		/** @var \PHPUnit_Framework_MockObject_MockObject|UnicodeRemoverInterface $remover */
		$remover = $this->getMock('\\Nassau\\TextMessage\\UnicodeRemover\\UnicodeRemoverInterface', ['removeUnicode']);
		$remover->expects($this->once())->method('removeUnicode')->with($this->equalTo($text))
				->will($this->returnValue($stripped));

		$senderMock = $this->getSenderMock();
		$senderMock->expects($this->once())->method('send')->will($this->returnValue(true))
			->with($this->callback(function($value) use ($stripped)
			{
				if ($value instanceof Message)
				{
					$this->assertEquals($stripped, $value->getContent());
					return true;
				}
				return false;
			}));

		$sender = new TextMessageSender($senderMock);
		$sender->setUnicodeRemover($remover);
		$sender->send(new Message($text, new PhoneNumber(0)));
	}

	public function testLongMessagesAreSplitted()
	{
		$message = new Message("", new PhoneNumber(0));
		$maxLen = $message->getMaxLength();

		$contents = [
			str_pad("lorem ipsum", $maxLen, "x", STR_PAD_RIGHT),
			"ipsum lorem",
		];
		$text = implode("", $contents);

		$senderMock = $this->getSenderMock();
		$callNum = 0;
		$senderMock->expects($this->exactly(2))->method('send')->will($this->returnValue(true))
		   ->with($this->callback(function($value) use (&$contents, &$callNum)
		   {
			   // phpunit calls the callback twice for every call :-/
			   if (++$callNum % 2)
			   {
				   return true;
			   }

			   if ($value instanceof Message)
			   {
				   $this->assertEquals(array_shift($contents), $value->getContent());
				   return true;
			   }
			   return false;
		   }));

		$sender = new TextMessageSender($senderMock);
		$sender->setSplitLongMessages(true);
		$sender->send(new Message($text, new PhoneNumber(0)));
	}

	public function testMessageWithSpecialCharactersGetsSplitted()
	{
		$maxLen = (new Message("", new PhoneNumber(0)))->getMaxLength();
		$text = str_pad("[lorem ipsum]", $maxLen, "x", STR_PAD_RIGHT);

		$senderMock = $this->getSenderMock();
		$senderMock->expects($this->exactly(2))->method('send')->will($this->returnValue(true));

		$sender = new TextMessageSender($senderMock);
		$sender->setSplitLongMessages(true);
		$sender->send(new Message($text, new PhoneNumber(0)));
	}


	public function testPrefixIsAdded()
	{
		$prefix = "[lorem ipsum]: ";
		$text = "ipsum lorem";
		$senderMock = $this->getSenderMock();
		$senderMock->expects($this->once())->method('send')->will($this->returnValue(true))
			->with($this->callback(function($value) use ($prefix, $text)
			{
				if ($value instanceof Message)
				{
					$this->assertEquals($prefix . $text, $value->getContent());
					return true;
				}
				return false;
			}));

		$sender = new TextMessageSender($senderMock);
		$sender->setPrefix($prefix);
		$sender->send(new Message($text, new PhoneNumber(0)));

	}

	public function testNumberVerificationIsDelegatedToSender()
	{
		$number = new PhoneNumber(0);
		$senderMock = $this->getSenderMock();
		$senderMock->expects($this->once())->method('verifyNumber')->with($this->equalTo($number));
		$sender = new TextMessageSender($senderMock);

		$sender->verifyNumber($number);
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|SenderInterface
	 */
	private function getSenderMock()
	{
		return $this->getMock("\\Nassau\\TextMessage\\SenderInterface", ['send', 'verifyNumber']);
	}

}
 