<?php


namespace Nassau\TextMessage;


class MessageTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param $type
	 * @param $content
	 *
	 * @dataProvider dataMessageTypesWithContent
	 */
	public function testPropperMessageTypeIsDetected($type, $content)
	{
		$this->assertEquals($type, (new Message($content))->getType());
	}
	public function dataMessageTypesWithContent()
	{
		return [
			Message::TYPE_ASCII => [Message::TYPE_ASCII, 'Just plain ASCII. ()/-,!?'],
			Message::TYPE_UNICODE => [Message::TYPE_UNICODE, 'Zażółć gęślą jaźń'],
		];

	}


}
