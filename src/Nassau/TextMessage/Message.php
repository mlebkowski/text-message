<?php

namespace Nassau\TextMessage;

class Message
{
	const TYPE_SMS = 'sms';
	const TYPE_UNICODE = 'unicode';

	/**
	 * @var string
	 */
	private $content;
	/**
	 * @var \Nassau\TextMessage\PhoneNumber
	 */
	private $recipient;
	/**
	 * @var string
	 */
	private $type;

	public function __construct($content, PhoneNumber $recipient, $type = self::TYPE_SMS)
	{
		$this->content = $content;
		$this->recipient = $recipient;
		$this->type = $type;
		if (false === in_array($type, [self::TYPE_SMS, self::TYPE_UNICODE]))
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
	 * @return \Nassau\TextMessage\PhoneNumber
	 */
	public function getRecipient()
	{
		return $this->recipient;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	public function getMaxLength()
	{
		switch ($this->type)
		{
			case self::TYPE_SMS:
				return 459;
			case self::TYPE_UNICODE:
				return 201;
		}
		return null;
	}

	function __toString()
	{
		return $this->getContent();
	}

}