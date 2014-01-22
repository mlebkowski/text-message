<?php

namespace Nassau\TextMessage;

use Nassau\TextMessage\UnicodeRemover\UnicodeRemoverInterface;

class TextMessageSender implements SenderInterface
{
	private $splitLongMessages = true;

	/**
	 * @var SenderInterface
	 */
	private $sender;

	/**
	 * @var string
	 */
	private $prefix;

	/**
	 * @var UnicodeRemover\UnicodeRemoverInterface
	 */
	private $unicodeRemover;

	/**
	 * @var GSMTransportUtils
	 */
	private $transport;

	/**
	 * @param SenderInterface $sender
	 */
	public function __construct(SenderInterface $sender)
	{
		$this->sender = $sender;
		$this->transport = new GSMTransportUtils;
	}

	public function setUnicodeRemover(UnicodeRemoverInterface $remover)
	{
		$this->unicodeRemover = $remover;
		return $this;
	}

	/**
	 * @param string $prefix
	 *
	 * @return $this
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}


	public function getSplitLongMessages()
	{
		return $this->splitLongMessages;
	}

	public function setSplitLongMessages($split)
	{
		$this->splitLongMessages = (bool) $split;
		if ($split && false === function_exists('mb_strlen'))
		{
			throw new \RuntimeException('mb_strlen function is required for splitting to work!');
		}
		return $this;
	}


	/**
	 * @param PhoneNumber $phone
	 *
	 * @return bool
	 */
	public function verifyNumber(PhoneNumber $phone)
	{
		return $this->sender->verifyNumber($phone);
	}

	public function send(Message $message)
	{
		$type = $message->getType();
		$text = $this->getPrefix() . trim($message->getContent());

		if ($this->unicodeRemover)
		{
			$text = $this->unicodeRemover->removeUnicode($text);
			$type = Message::TYPE_SMS;
		}

		if ($this->getSplitLongMessages())
		{
			$maxLen = $message->getMaxLength();
			$textPreparedForSms = $this->transport->prepareTextForMessage($text);

			if (mb_strlen($textPreparedForSms) > $maxLen)
			{
				$text = $this->transport->splitMessage($textPreparedForSms, $maxLen);

				$text = array_map($this->transport, $text);
			}
		}

		if (false === is_array($text))
		{
			$text = array($text);
		}

		foreach ($text as $contentPart)
		{
			$this->sender->send(new Message($contentPart, $message->getRecipient(), $type));
		}
	}

}
