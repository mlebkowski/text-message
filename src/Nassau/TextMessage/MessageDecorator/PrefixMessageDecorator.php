<?php


namespace Nassau\TextMessage\MessageDecorator;


use Nassau\TextMessage\Message;

class PrefixMessageDecorator extends Message
{
	/**
	 * @var \Nassau\TextMessage\Message
	 */
	private $message;
	/**
	 * @var string
	 */
	private $prefix;

	public function __construct(Message $message, $prefix)
	{
		$this->message = $message;
		$this->prefix = $prefix;
	}

	public function getContent()
	{
		return $this->prefix . $this->message->getContent();
	}

	public function getType()
	{
		return $this->message->getType();
	}

}