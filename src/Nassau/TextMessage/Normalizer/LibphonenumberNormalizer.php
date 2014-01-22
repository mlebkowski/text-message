<?php


namespace Nassau\TextMessage\Normalizer;


use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Nassau\TextMessage\PhoneNumber;

class LibphonenumberNormalizer implements NumberNormalizerInterface
{
	/**
	 * @var PhoneNumberUtil
	 */
	private $lib;
	/**
	 * @var
	 */
	private $defaultRegion;

	function __construct(PhoneNumberUtil $lib, $defaultRegion)
	{
		$this->lib = $lib;
		$this->defaultRegion = $defaultRegion;
	}


	/**
	 * @param PhoneNumber $number
	 *
	 * @throws NumberException
	 * @return PhoneNumber
	 */
	public function normalizeNumber(PhoneNumber $number)
	{
		try
		{
			$phoneNumber = $this->lib->parse($number->getNumber(), $this->defaultRegion);
			$normalized = $this->lib->format($phoneNumber, PhoneNumberFormat::E164);
			return new PhoneNumber($normalized);
		}
		catch (NumberParseException $e)
		{
			throw new NumberException($e->getMessage(), $e->getCode(), $e);
		}

	}
}