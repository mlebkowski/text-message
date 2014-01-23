<?php

namespace Nassau\TextMessage\Adapter;

use libphonenumber\PhoneNumber;
use Mobitex\Sender;
use Mobitex\Exception as MobitexException;
use Nassau\TextMessage\GSMTransportUtils;
use Nassau\TextMessage\Message;
use Nassau\TextMessage\NeedsNumberVerificationInterface;
use Nassau\TextMessage\SenderException;
use Nassau\TextMessage\SenderInterface;

class MobitexAdapter implements SenderInterface, NeedsNumberVerificationInterface
{
	const MAX_LENGTH_ASCII = 459;
	const MAX_LENGTH_UNICODE = 201;
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
	 * @param PhoneNumber $number
	 *
	 * @return bool
	 */
	public function verifyNumber(PhoneNumber $number)
	{
		$sPhone = $number->getCountryCode() . $number->getNationalNumber();
		return $this->sender->verifyNumber($sPhone);
	}

	public function send(Message $message, PhoneNumber $recipient)
	{
		$number = $recipient->getCountryCode() . $recipient->getNationalNumber();
		$text = $message->getContent();

		try
		{
			$maxLen = $message->getType() === Message::TYPE_ASCII ? self::MAX_LENGTH_ASCII : self::MAX_LENGTH_UNICODE;
			$textPreparedForSms = $this->transport->prepareTextForMessage($text);

			if (mb_strlen($textPreparedForSms) > $maxLen)
			{
				$text = $this->transport->splitMessage($textPreparedForSms, $maxLen);

				$text = array_map($this->transport, $text);
			}

			if (false === is_array($text))
			{
				$text = array($text);
			}

			foreach ($text as $contentPart)
			{
				$this->sender->sendMessage($number, $contentPart, $this->getTypeForMessage($message));
			}
		}
		catch (MobitexException $e)
		{
			throw new SenderException($e->getMessage(), $e->getCode(), $e);
		}
	}

	private function getSinglePartMaxLength($type)
	{
		switch ($type)
		{
			case Sender::TYPE_SMS:
				return 160;
			case Sender::TYPE_UNICODE:
				return 70;
		}
		throw new \InvalidArgumentException('Never happens :D');
 	}

	private function getTypeForMessage(Message $message)
	{
		$msg = $this->transport->prepareTextForMessage($message->getContent());
		switch ($message->getType())
		{
			case Message::TYPE_ASCII:
				return (mb_strlen($msg) <= $this->getSinglePartMaxLength(Sender::TYPE_SMS)) ? Sender::TYPE_SMS : Sender::TYPE_CONCAT;
			case Message::TYPE_UNICODE:
				return (mb_strlen($msg) <= $this->getSinglePartMaxLength(Sender::TYPE_UNICODE)) ? Sender::TYPE_UNICODE : Sender::TYPE_UNICODE_CONCAT;
		}
		throw new \InvalidArgumentException('Never happens :D');
	}

}