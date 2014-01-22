<?php


namespace Nassau\TextMessage\Normalizer;


use Nassau\TextMessage\PhoneNumber;

interface NumberNormalizerInterface
{
	/**
	 * @param PhoneNumber $number
	 *
	 * @throws NumberException
	 * @return PhoneNumber
	 */
	public function normalizeNumber(PhoneNumber $number);
}