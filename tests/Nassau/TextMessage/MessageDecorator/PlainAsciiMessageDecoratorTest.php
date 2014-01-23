<?php


namespace Nassau\TextMessage\MessageDecorator;


use Nassau\TextMessage\Message;
use Nassau\TextMessage\UnicodeRemover\UnicodeRemoverInterface;

class PlainAsciiMessageDecoratorTest extends \PHPUnit_Framework_TestCase
{
	public function testUnicodeRemoverIsCalled()
	{
		$content = "lorem ipsum";
		$returnValue = "ipsum lorem";

		$message = new Message($content, Message::TYPE_UNICODE);
		/** @var \PHPUnit_Framework_MockObject_MockObject|UnicodeRemoverInterface $remover */
		$remover = $this->getMock('\\Nassau\\TextMessage\\UnicodeRemover\\UnicodeRemoverInterface', ['removeUnicode']);
		$remover->expects($this->once())->method('removeUnicode')->with($this->equalTo($content))
			->will($this->returnValue($returnValue));

		$decorator = new PlainAsciiMessageDecorator($message, $remover);

		$this->assertEquals($returnValue, $decorator->getContent());
		$this->assertEquals(Message::TYPE_ASCII, $decorator->getType());

	}
}
