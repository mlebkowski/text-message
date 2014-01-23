<?php

namespace Nassau\TextMessage;

class Message
{
	const TYPE_ASCII = 'sms';
	const TYPE_UNICODE = 'unicode';

	/**
	 * @var string
	 */
	private $content;
	/**
	 * @var string
	 */
	private $type;

	public function __construct($content, $type = self::TYPE_ASCII)
	{
		$this->content = $content;
		$this->type = $type;
		if (false === in_array($type, [self::TYPE_ASCII, self::TYPE_UNICODE]))
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

}