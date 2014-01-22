<?php

namespace Nassau\TextMessage\Normalizer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use Nassau\TextMessage\PhoneNumber;

class LibphonenumberNormalizerTest extends \PHPUnit_Framework_TestCase
{
	public function testNormalizerCallsLibrary()
	{
		$number = new PhoneNumber('123 456 789');
		$normalized = new PhoneNumber('48123456789');

		$phoneNumber = $this->getMock('\\libphonenumber\\PhoneNumber');

		/** @var \PHPUnit_Framework_MockObject_MockObject|\libphonenumber\PhoneNumberUtil $mock */
		$mock = $this->getMock('\libphonenumber\PhoneNumberUtil', ['parse', 'format'], [], '', false);
		$mock->expects($this->once())->method('parse')
			->with($this->equalTo($number->getNumber()))
			->will($this->returnValue($phoneNumber));

		$mock->expects($this->once())->method('format')->with(
			$this->equalTo($phoneNumber),
			$this->equalTo(PhoneNumberFormat::E164)
		)->will($this->returnValue($normalized->getNumber()));

		$normalizer = new LibphonenumberNormalizer($mock, "PL");

		$result = $normalizer->normalizeNumber($number);
		$this->assertEquals($normalized, $result);
		$this->assertInstanceOf("\\Nassau\\TextMessage\\PhoneNumber", $result);
	}

	/**
	 * @expectedException \Nassau\TextMessage\Normalizer\NumberException
	 */
	public function testInvalidNumberThrowsNumberException()
	{
		$number = new PhoneNumber('123456789');

		$exception = new NumberParseException(0, "dummy");

		/** @var \PHPUnit_Framework_MockObject_MockObject|\libphonenumber\PhoneNumberUtil $mock */
		$mock = $this->getMock('\libphonenumber\PhoneNumberUtil', ['parse'], [], '', false);
		$mock->expects($this->once())->method('parse')->will($this->throwException($exception));

		$normalizer = new LibphonenumberNormalizer($mock, "PL");

		$normalizer->normalizeNumber($number);
	}
}
