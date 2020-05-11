<?php

namespace App\Tests;

use App\MeetingTimes;
use App\Timezone;
use DateTime;
use PHPUnit\Framework\TestCase;

class MeetingTimesTest extends TestCase {

	/**
	 * @covers \App\MeetingTimes::getStartTime()
	 * @covers \App\MeetingTimes::getTimezones()
	 */
	public function testDefaults() {
		$meetingTimes = new MeetingTimes();
		static::assertSame( $meetingTimes->getStartTime()->format( 'H:i:s' ), '00:00:00' );
		static::assertCount( 4, $meetingTimes->getTimezones() );
		static::assertSame( 'Etc/UTC', $meetingTimes->getTimezones()['Etc/UTC']->getName() );
	}

	/**
	 * @covers \App\MeetingTimes::getEndTime()
	 */
	public function testGetEndTime() {
		$meetingTimes = new MeetingTimes();
		$meetingTimes->setStartTime( new DateTime( '2020-04-05' ) );
		$meetingTimes->setEndTime( new DateTime( '1990-04-05' ) );
		static::assertSame( '2020-04-06', $meetingTimes->getEndTime()->format( 'Y-m-d' ) );
	}

	/**
	 * @covers \App\MeetingTimes::getTimeslots()
	 */
	public function testGetTimeslots() {
		$meetingTimes = new MeetingTimes();
		$meetingTimes->setStartTime( new DateTime( '2020-05-05 22:00 Z' ) );
		$meetingTimes->setEndTime( new DateTime( '2020-05-06 02:00 Z' ) );
		$timeslots = $meetingTimes->getTimeslots();
		static::assertCount( 4, $timeslots );
		static::assertSame( '2020-05-05 22:00', $timeslots[0]->getStartTime()->format( 'Y-m-d H:i' ) );

		static::assertSame( 'Tuesday', $timeslots[0]->getWeekday() );
		static::assertSame( '', $timeslots[1]->getWeekday() );
		static::assertSame( 'Wednesday', $timeslots[2]->getWeekday() );
		static::assertSame( '', $timeslots[3]->getWeekday() );

		$perth = new Timezone( 'Australia/Perth' );
		$meetingTimes->addTimezone( $perth );
		// $meetingTimes->addTimezone( new Timezone( 'America/New_York' ) );
		//static::assertCount( 3, $meetingTimes->getTimezones() );
		static::assertSame( 'Wednesday', $timeslots[0]->getWeekday( $perth ) );
		static::assertSame( '', $timeslots[1]->getWeekday( $perth ) );
		static::assertSame( '', $timeslots[2]->getWeekday( $perth ) );
		static::assertSame( '', $timeslots[3]->getWeekday( $perth ) );

		$rows = [];
		foreach ( $meetingTimes->getTimeslots() as $timeslot ) {
			$row = [];
			foreach ( $meetingTimes->getTimezones() as $timezone ) {
				$row[] = $timeslot->getStartTime( $timezone )->format( 'Y-m-d H:i' )
					. ' ' . $timeslot->getWeekday( $timezone );
			}
			$rows[] = $row;
		}
		static::assertSame( [
			[ '2020-05-05 22:00 Tuesday', '2020-05-06 06:00 Wednesday' ],
			[ '2020-05-05 23:00 ', '2020-05-06 07:00 ' ],
			[ '2020-05-06 00:00 Wednesday', '2020-05-06 08:00 ' ],
			[ '2020-05-06 01:00 ', '2020-05-06 09:00 ' ],
		], $rows );
	}
}
