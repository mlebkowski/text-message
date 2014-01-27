<?php


namespace Nassau\TextMessage\Adapter;

use libphonenumber\PhoneNumber;
use Nassau\TextMessage\Message;
use Nassau\TextMessage\SenderException;
use Nassau\TextMessage\SenderInterface;
use Nassau\TextMessage\SenderNameInterface;

class NexmoAdapter implements SenderInterface, SenderNameInterface
{
	const NEXTMO_ENDPOINT = 'https://rest.nexmo.com/sms/json';
	/**
	 * @var string
	 */
	private $senderName;
	/**
	 * @var
	 */
	private $apiKey;
	/**
	 * @var
	 */
	private $apiSecret;

	public function __construct($apiKey, $apiSecret)
	{
		$this->apiKey = $apiKey;
		$this->apiSecret = $apiSecret;
	}

	/**
	 * @param Message     $message
	 * @param PhoneNumber $recipient
	 * @throws SenderException
	 * @return bool
	 */
	public function send(Message $message, PhoneNumber $recipient)
	{
		$params = [
			'api_key' => $this->apiKey,
			'api_secret' => $this->apiSecret,
			'from' => $this->senderName,
			'to' => $recipient->getCountryCode() . $recipient->getNationalNumber(),
			'text' => $message->getContent(),
			'type' => $message->getType() === Message::TYPE_UNICODE ? "unicode" : "text",
		];

		$url = self::NEXTMO_ENDPOINT . '?' . http_build_query($params);

		$response = $this->getResponse($url);

		if (false === isset($response['messages']))
		{
			throw new SenderException('Invalid nexmo response: ' . var_export($response, true));
		}

		foreach ($response['messages'] as $message)
		{
			if ($message['status'] !== "0")
			{
				throw new SenderException($message['error-text'], $message['status']);
			}
		}
		return true;
	}

	public function setSenderName($senderName)
	{
		$this->senderName = $senderName;
	}

	private function getResponse($url)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		return json_decode($response, true);
	}
}