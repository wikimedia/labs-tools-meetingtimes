<?php

namespace App\Tests;

use App\MeetingTimes;
use App\Timeslot;
use App\Timezone;
use DateTime;
use PHPUnit\Framework\TestCase;

class TimeslotTest extends TestCase {

	/**
	 * @covers \App\Timeslot::getWeekday()
	 */
	public function testGeneral() {
		$perth = new Timezone( 'Australia/Perth' );
		$canberra = new Timezone( 'Australia/Canberra' );
		$timeslot = new Timeslot( new DateTime( '2020-05-01 15:00:00 Z' ) );
		static::assertSame(
			'2020-05-01 15:00',
			$timeslot->getStartTime()->format( 'Y-m-d H:i' )
		);
		static::assertSame(
			'2020-05-01 23:00',
			$timeslot->getStartTime( $perth )->format( 'Y-m-d H:i' )
		);
		static::assertSame(
			'2020-05-02 01:00',
			$timeslot->getStartTime( $canberra )->format( 'Y-m-d H:i' )
		);
		static::assertSame( 'Friday', $timeslot->getWeekday() );
		static::assertSame( 'Friday', $timeslot->getWeekday( $perth ) );
		static::assertSame( 'Saturday', $timeslot->getWeekday( $canberra ) );
	}

	/**
	 * @covers \App\Timeslot::hasDST()
	 */
	public function testShowDst() {
		$perth = new Timezone( 'Australia/Perth' );
		static::assertFalse(
			( new Timeslot( new DateTime( '2008-10-25 17:00:00 Z' ) ) )->hasDST( $perth )
		);
		static::assertTrue(
			( new Timeslot( new DateTime( '2008-10-25 18:00:00 Z' ) ) )->hasDST( $perth )
		);
	}
}
