<?php


namespace Nassau\TextMessage\UnicodeRemover;


class IconvRemover implements UnicodeRemoverInterface
{
	private $outCharset;

	public function __construct($useIgnore = true)
	{
		$this->outCharset = 'ASCII//TRANSLIT' . ($useIgnore ? '//IGNORE' : '');
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function removeUnicode($text)
	{
		return iconv('UTF-8', $this->outCharset, $text);
	}
}