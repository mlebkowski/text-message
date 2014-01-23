<?php


namespace Nassau\TextMessage\MessageDecorator;


use Nassau\TextMessage\Message;
use Nassau\TextMessage\UnicodeRemover\UnicodeRemoverInterface;

class PlainAsciiMessageDecorator extends Message
{
	/**
	 * @var \Nassau\TextMessage\Message
	 */
	private $message;
	/**
	 * @var \Nassau\TextMessage\UnicodeRemover\UnicodeRemoverInterface
	 */
	private $unicodeRemover;

	public function __construct(Message $message, UnicodeRemoverInterface $unicodeRemover)
	{
		$this->message = $message;
		$this->unicodeRemover = $unicodeRemover;
	}

	public function getContent()
	{
		return $this->unicodeRemover->removeUnicode($this->message->getContent());
	}

	public function getType()
	{
		return Message::TYPE_ASCII;
	}

}