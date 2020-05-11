<?php

namespace App;

use DateInterval;
use DateTime;

class Timeslot {

	/** @var DateTime */
	protected $startTime;

	/** @var Timeslot|null */
	protected $previous;

	/** @var DateInterval */
	protected $duration;

	/**
	 * @param DateTime $startTime
	 * @param Timeslot|null $previous
	 */
	public function __construct( DateTime $startTime, Timeslot $previous = null ) {
		$this->startTime = $startTime;
		$this->previous = $previous;
		$this->duration = new DateInterval( 'PT1H' );
	}

	/**
	 * @param DateInterval $duration
	 */
	public function setDuration( DateInterval $duration ): void {
		$this->duration = $duration;
	}

	/**
	 * @return DateInterval
	 */
	public function getDuration(): DateInterval {
		return $this->duration;
	}

	/**
	 * Get the name of the week to display.
	 * @param Timezone|null $timezone
	 * @return string
	 */
	public function getWeekday( Timezone $timezone = null ) {
		$prev = $this->previous ? $this->previous->getStartTime( $timezone )->format( 'l' ) : false;
		$curr = $this->getStartTime( $timezone )->format( 'l' );
		return $prev !== $curr ? $curr : '';
	}

	/**
	 * @param Timezone|null $timezone
	 * @return DateTime
	 */
	public function getStartTime( Timezone $timezone = null ): DateTime {
		if ( $timezone ) {
			$time = clone $this->startTime;
			$time->setTimezone( $timezone->getDateTimeZone() );
			return $time;
		}
		return $this->startTime;
	}

	/**
	 * @param Timezone $timezone
	 * @return bool If any of the timezones within this timeslot are Daylight Saving Time.
	 */
	public function hasDST( Timezone $timezone ): bool {
		$start = clone $this->startTime;
		$start->setTimezone( $timezone->getDateTimeZone() );
		$end = clone $start;
		$end->add( $this->getDuration() );
		$transitions = $timezone->getTransitions( $start, $end );
		foreach ( $transitions as $transition ) {
			if ( $transition[ 'isdst' ] ) {
				return true;
			}
		}
		return false;
	}

}
