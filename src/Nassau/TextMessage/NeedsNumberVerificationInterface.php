<?php

namespace Nassau\TextMessage;

use libphonenumber\PhoneNumber;

interface NeedsNumberVerificationInterface
{
	/**
	 * Test if sender can use this number
	 *
	 * @param \libphonenumber\PhoneNumber $number
	 *
	 * @return bool
	 */
	public function verifyNumber(PhoneNumber $number);

}