<?php

namespace Nassau\TextMessage;

use libphonenumber\PhoneNumber;

interface SenderInterface
{

	/**
	 * @param Message     $message
	 * @param PhoneNumber $recipient
	 * @throws SenderException
	 * @return bool
	 */
	public function send(Message $message, PhoneNumber $recipient);
}