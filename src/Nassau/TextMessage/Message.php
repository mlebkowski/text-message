<?php

namespace Nassau\TextMessage;

class Message
{
	const TYPE_ASCII = 'ascii';
	const TYPE_UNICODE = 'unicode';

	/**
	 * @var string
	 */
	private $content;
	/**
	 * @var string
	 */
	private $type;

	public function __construct($content, $type = null)
	{
		$this->content = $content;
		$this->type = $type ?: $this->detectType($content);
		if (false === in_array($this->type, [self::TYPE_ASCII, self::TYPE_UNICODE]))
		{
			throw new \InvalidArgumentException('Unknown Text Message Type: ' . $type);
		}
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	function __toString()
	{
		return $this->getContent();
	}

	private function detectType($content)
	{
		return mb_detect_encoding($content, 'ASCII', true) ? self::TYPE_ASCII : self::TYPE_UNICODE;
	}

}