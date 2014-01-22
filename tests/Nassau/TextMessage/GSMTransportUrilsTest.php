<?php

namespace Nassau\TextMessage;

class GSMTransportUrilsTest extends \PHPUnit_Framework_TestCase
{
	public function testCharactersAreEscaped()
	{
		$escaper = new GSMTransportUtils();
		$null = GSMTransportUtils::ESCAPE_CHAR;
		foreach (explode(" ", wordwrap('[]~^{}|\\', 1, " ", true)) as $char)
		{
			$this->assertEquals($null . $char, $escaper->prepareTextForMessage($char));
		}
	}

	public function testCharactersAreUnEscaped()
	{
		$escaper = new GSMTransportUtils();
		$null = GSMTransportUtils::ESCAPE_CHAR;
		foreach (explode(" ", wordwrap('[]~^{}|\\', 1, " ", true)) as $char)
		{
			$this->assertEquals($char, $escaper->unprepareTextForMessage($null.$char));
		}
	}

	public function testInvokeCallsUnprepare()
	{
		$escaper = new GSMTransportUtils();
		$this->assertEquals("[", $escaper(GSMTransportUtils::ESCAPE_CHAR . "["));
	}

	public function testSplitWithLength()
	{
		$text = str_repeat("x", 100);
		$len = 10;
		$expected = explode(" ", wordwrap($text, $len, " ", true));
		$splitter = new GSMTransportUtils();

		$this->assertEquals($expected, $splitter->splitMessage($text, $len));
	}

	public function testSplitterDoesNotLeaveEscapeCharacterAtTheEnd()
	{
		$text = "aa" . GSMTransportUtils::ESCAPE_CHAR . "bbccc";
		$expected = ["aa", GSMTransportUtils::ESCAPE_CHAR . "bb", "ccc"];
		$splitter = new GSMTransportUtils();
		$this->assertEquals($expected, $splitter->splitMessage($text, 3));

	}
}
 