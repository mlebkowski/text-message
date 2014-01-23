<?php


namespace Nassau\TextMessage;

use libphonenumber\PhoneNumber;

class WhitelistDecorator implements SenderInterface
{
	/**
	 * @var PhoneNumber[]
	 */
	private $whitelist;
	/**
	 * @var SenderInterface
	 */
	private $sender;

	/**
	 * @param SenderInterface $sender
	 * @param PhoneNumber[]   $whitelist
	 */
	public function __construct(SenderInterface $sender, $whitelist)
	{
		$this->sender = $sender;
		$this->whitelist = $whitelist;
	}

	public function send(Message $message, PhoneNumber $recipient)
	{
		foreach ($this->whitelist as $whitelisted)
		{
			if ($recipient->equals($whitelisted))
			{
				return $this->sender->send($message, $recipient);
			}
		}
		return true;
	}
}