<?php


namespace Nassau\TextMessage\MessageDecorator;


use Nassau\TextMessage\Message;

class PrefixMessageDecoratorTest extends \PHPUnit_Framework_TestCase
{
	public function testPrefixIsAdded()
	{
		$content = "without prefix";
		$prefix = "prefix: ";
		$message = new Message($content);

		$decorator = new PrefixMessageDecorator($message, $prefix);

		$this->assertEquals($prefix.$content, $decorator->getContent());
	}

	public function testGetTypeIsForwarded()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|Message $mock */
		$mock = $this->getMock('\\Nassau\\TextMessage\\Message', ['getType'], [""]);
		$mock->expects($this->once())->method('getType');

		$decorator = new PrefixMessageDecorator($mock, "");
		$decorator->getType();
	}
}
