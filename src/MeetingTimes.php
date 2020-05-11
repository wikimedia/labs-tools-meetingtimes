<?php

namespace App;

use DateInterval;
use DateTime;
use DateTimeZone;

class MeetingTimes {

	/** @var DateTime */
	protected $startTime;

	/** @var DateTime */
	protected $endTime;

	/** @var Timezone[] */
	protected $timezones;

	/** @var Timeslot[] */
	protected $timeslots;

	public function __construct() {
		$this->addTimezone( new Timezone( 'Etc/UTC' ) );
		$startTime = new DateTime();
		$startTime->setTime( 0, 0, 0, 0 );
		$this->setStartTime( $startTime );
		$endTime = new DateTime();
		$endTime->setTime( 24, 0, 0, 0 );
		$this->setEndTime( $endTime );
	}

	/**
	 * @return string[]
	 */
	public function getIdentifiers(): array {
		return DateTimeZone::listIdentifiers();
	}

	/**
	 * @return Timezone[]
	 */
	public function getTimezones(): array {
		if ( count( $this->timezones ) === 1 ) {
			// Get three random ones.
			$randTzs = array_rand( array_flip( $this->getIdentifiers() ), 3 );
			$defaultTzs = array_unique( array_merge( [ 'Etc/UTC' ], $randTzs ) );
			foreach ( $defaultTzs as $tz ) {
				$this->addTimezone( new Timezone( $tz ) );
			}
		}
		return $this->timezones;
	}

	/**
	 * @param Timezone $timezone
	 */
	public function addTimezone( Timezone $timezone ): void {
		$this->timezones[$timezone->getName()] = $timezone;
	}

	/**
	 * @return Timeslot[]
	 */
	public function getTimeslots(): array {
		if ( $this->timeslots ) {
			return $this->timeslots;
		}
		$timeslots = [];
		$incrementingTime = clone $this->getStartTime();
		$timeslot = null;
		while ( $incrementingTime < $this->getEndTime() ) {
			$timeslot = new Timeslot( clone $incrementingTime, $timeslot );
			$timeslots[] = $timeslot;
			$incrementingTime->add( $timeslot->getDuration() );
		}
		$this->timeslots = $timeslots;
		return $timeslots;
	}

	/**
	 * @return DateTime
	 */
	public function getStartTime(): DateTime {
		return $this->startTime;
	}

	/**
	 * @param DateTime $startTime
	 */
	public function setStartTime( DateTime $startTime ): void {
		$this->startTime = $startTime;
	}

	/**
	 * @return DateTime
	 */
	public function getEndTime(): DateTime {
		// If the end is before the start, move to 1 day after.
		if ( !$this->endTime || $this->endTime < $this->startTime ) {
			$this->endTime = clone $this->startTime;
			$this->endTime->add( new DateInterval( 'P1D' ) );
		}
		return $this->endTime;
	}

	/**
	 * @param DateTime $endTime
	 */
	public function setEndTime( DateTime $endTime ): void {
		$this->endTime = $endTime;
	}

}
