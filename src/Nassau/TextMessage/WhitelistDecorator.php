<?php


namespace Nassau\TextMessage;

class WhitelistDecorator implements SenderInterface
{
	/**
	 * @var \Nassau\TextMessage\PhoneNumber[]
	 */
	private $whitelist;
	/**
	 * @var SenderInterface
	 */
	private $sender;

	public function __construct(SenderInterface $sender, $whitelist)
	{
		$this->sender = $sender;
		$this->whitelist = array_map(function ($number)
		{
			return $number instanceof PhoneNumber ? $number : new PhoneNumber($number);
		},
		$whitelist);
	}

	/**
	 * Test if sender can use this number
	 *
	 * @param \Nassau\TextMessage\PhoneNumber $number
	 * @codeCoverageIgnore
	 *
	 * @return bool
	 */
	public function verifyNumber(PhoneNumber $number)
	{
		return $this->sender->verifyNumber($number);
	}

	public function send(Message $message)
	{
		$number = $message->getRecipient();
		foreach ($this->whitelist as $whitelisted)
		{
			if ($number->equals($whitelisted))
			{
				return $this->sender->send($message);
			}
		}
		return true;
	}
}