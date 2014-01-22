<?php

namespace Nassau\TextMessage\Adapter;

use Mobitex\Sender;
use Mobitex\Exception as MobitexException;
use Nassau\TextMessage\Message;
use Nassau\TextMessage\PhoneNumber;
use Nassau\TextMessage\SenderException;
use Nassau\TextMessage\SenderInterface;

class MobitexAdapter implements SenderInterface
{
	/**
	 * @var \Mobitex\Sender
	 */
	private $sender;

	public function __construct(Sender $sender)
	{

		$this->sender = $sender;
	}

	/**
	 * Test if sender can use this number
	 *
	 * @param \Nassau\TextMessage\PhoneNumber $number
	 *
	 * @return bool
	 */
	public function verifyNumber(PhoneNumber $number)
	{
		return $this->sender->verifyNumber($number->getNumber());
	}

	public function send(Message $message)
	{
		$number = $message->getRecipient()->getNumber();
		$text = $message->getContent();
		try
		{
			return $this->sender->sendMessage($number, $text, $message->getType());
		}
		catch (MobitexException $e)
		{
			throw new SenderException($e->getMessage(), $e->getCode(), $e);
		}
	}
}