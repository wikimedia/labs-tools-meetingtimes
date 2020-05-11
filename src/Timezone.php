<?php

namespace App;

use DateTime;
use DateTimeZone;

class Timezone {

	/** @var DateTimeZone */
	protected $tz;

	/**
	 * @param string $identifier
	 */
	public function __construct( string $identifier ) {
		// Normalize the identifier.
		$identifiers = DateTimeZone::listIdentifiers();
		foreach ( $identifiers as $id ) {
			if ( strtolower( $id ) === strtolower( trim( $identifier ) ) ) {
				$identifier = $id;
				break;
			}
		}
		$this->tz = new DateTimeZone( $identifier );
	}

	/**
	 * @return DateTimeZone
	 */
	public function getDateTimeZone(): DateTimeZone {
		return $this->tz;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->tz->getName();
	}

	/**
	 * @param DateTime $start
	 * @param DateTime|null $end
	 * @return string[][]
	 */
	public function getTransitions( DateTime $start, DateTime $end = null ): array {
		if ( $end === null ) {
			$end = $start;
		}
		$out = [];
		$transitions = $this->tz->getTransitions( $start->getTimestamp(), $end->getTimestamp() );
		// Not all timezones report a transition.
		if ( !is_array( $transitions ) ) {
			return $out;
		}
		foreach ( $transitions as $tran ) {
			$totalMinutes = $tran['offset'] / 60;
			$hours = floor( $totalMinutes / 60 );
			$minutes = $totalMinutes - ( $hours * 60 );
			$formattedOffset = ( $tran['offset'] > 0 ? '+' : '' )
				. str_pad( $hours, 2, '0', STR_PAD_LEFT )
				. ':' . str_pad( round( $minutes ), 2, '0', STR_PAD_LEFT );
			$out[] = [
				'abbr' => $tran['abbr'],
				'isdst' => $tran['isdst'],
				'offset' => $tran['offset'],
				'offset_formatted' => $formattedOffset,
			];
		}
		return $out;
	}
}
