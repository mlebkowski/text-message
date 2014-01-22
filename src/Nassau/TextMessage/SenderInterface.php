<?php

namespace Nassau\TextMessage;

interface SenderInterface
{
	/**
	 * Test if sender can use this number
	 *
	 * @param \Nassau\TextMessage\PhoneNumber $number
	 *
	 * @return bool
	 */
	public function verifyNumber(PhoneNumber $number);

	/**
	 * @param Message     $message
	 * @param PhoneNumber $recipient
	 * @throws SenderException
	 * @return bool
	 */
	public function send(Message $message, PhoneNumber $recipient);
}