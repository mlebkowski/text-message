<?php


namespace Nassau\TextMessage\UnicodeRemover;


interface UnicodeRemoverInterface
{
	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public function removeUnicode($text);
}