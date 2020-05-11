<?php

namespace App;

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
		$this->startTime = new DateTime();
		$this->startTime->setTime( 0, 0, 0, 0 );
		$this->endTime = new DateTime();
		$this->endTime->setTime( 24, 0, 0, 0 );
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
		while ( $incrementingTime < $this->endTime ) {
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
		return $this->endTime;
	}

	/**
	 * @param DateTime $endTime
	 */
	public function setEndTime( DateTime $endTime ): void {
		$this->endTime = $endTime;
	}

}
