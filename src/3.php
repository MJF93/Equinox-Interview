<?php

use PHPUnit\Framework\TestCase;

class ApplyDiscountTest extends TestCase
{

 /**
 * @var array
 */
 private $discountCodes = [
	 'valid' => [
		 'SUMMER10' => 10,
		 'WINTER20' => 20,
	 ],
	 'invalid' => [
		'INVALID_CODE' => 0,
	 ],
	 'edge' => [
		 'NO_DISCOUNT' => 0,
		 'FREE100' => 0,
	 ]
 ];

 public function testValidDiscountCode($discountCode, $expected)
 {
 $this->assertEquals($expected, applyDiscount(100, $discountCode));
 }

 public function testInvalidDiscountCode()
 {
 $this->assertEquals(100, applyDiscount(100, $this->discountCodes['invalid']['INVALID_CODE']));
 }

 public function testEdgeCases()
 {
 $this->assertEquals(100, applyDiscount(100, $this->discountCodes['edge']['NO_DISCOUNT']));

 $this->assertEquals(0, applyDiscount(100, $this->discountCodes['edge']['FREE100']));
 }
}

/*
To run these test use the following command:

phpunit --bootstrap <path_to_your_file> ApplyDiscountTest

Replace <path_to_your_file> with the appropriate path to your bootstrap file if necessary.

*/