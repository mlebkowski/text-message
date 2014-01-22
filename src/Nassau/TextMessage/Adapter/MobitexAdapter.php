<?php

namespace Nassau\TextMessage\Adapter;

use Mobitex\Sender;
use Mobitex\Exception as MobitexException;
use Nassau\TextMessage\GSMTransportUtils;
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
	/** @var GSMTransportUtils */
	private $transport;

	public function __construct(Sender $sender)
	{
		$this->sender = $sender;
		$this->transport = new GSMTransportUtils;
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

	public function send(Message $message, PhoneNumber $recipient)
	{
		$number = $recipient->getNumber();
		$text = $message->getContent();
		try
		{
			return $this->sender->sendMessage($number, $text, $this->getTypeForMessage($message));
		}
		catch (MobitexException $e)
		{
			throw new SenderException($e->getMessage(), $e->getCode(), $e);
		}
	}

	private function getMaxLength($type)
	{
		switch ($type)
		{
			case Sender::TYPE_SMS:
				return 160;
			case Sender::TYPE_UNICODE:
				return 70;
			case Sender::TYPE_CONCAT:
				return 459;
			case Sender::TYPE_UNICODE_CONCAT:
				return 201;
		}
		throw new \InvalidArgumentException('Never happens :D');
 	}

	private function getTypeForMessage(Message $message)
	{
		$msg = $this->transport->prepareTextForMessage($message->getContent());
		switch ($message->getType())
		{
			case Message::TYPE_SMS:
				return (mb_strlen($msg) <= $this->getMaxLength(Sender::TYPE_SMS)) ? Sender::TYPE_SMS : Sender::TYPE_CONCAT;
			case Message::TYPE_UNICODE:
				return (mb_strlen($msg) <= $this->getMaxLength(Sender::TYPE_UNICODE)) ? Sender::TYPE_UNICODE : Sender::TYPE_UNICODE_CONCAT;
		}
		throw new \InvalidArgumentException('Never happens :D');
	}

}