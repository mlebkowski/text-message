<?php


namespace Nassau\TextMessage;


class GSMTransportUtils
{
	const DOUBLE_CHARS = '[]~^{}|\\';
	const ESCAPE_CHAR = "\0";

	public function prepareTextForMessage($text, $reverse = false)
	{
		$tr = [];
		for ($i = 0; $i < strlen(self::DOUBLE_CHARS); $i++)
		{
			$c = substr(self::DOUBLE_CHARS, $i, 1);
			$tr[$c] = self::ESCAPE_CHAR . $c;
		}

		if ($reverse)
		{
			$tr = array_flip($tr);
		}

		return strtr($text, $tr);
	}

	public function unprepareTextForMessage($text)
	{
		return $this->prepareTextForMessage($text, true);
	}

	public function __invoke($text)
	{
		return $this->unprepareTextForMessage($text);
	}

	public function splitMessage($text, $length)
	{
		// split into fixed length pieces
		// assert that no part ends in the escape char (\null)
		$re = sprintf('/.{1,%d}(?<!%s)/', $length, '\\' . ord(self::ESCAPE_CHAR));
		preg_match_all($re, $text, $text);
		return reset($text);
	}
}