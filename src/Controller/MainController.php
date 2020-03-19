<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {
	/**
	 * @Route("/", name="home")
	 * @param Request $request
	 * @return Response
	 */
	public function index( Request $request ) {
		$identifiers = DateTimeZone::listIdentifiers();

		$tzs = [];
		// Show UTC and three other random timezones.
		$randTzs = array_rand( array_flip( $identifiers ), 3 );
		$defaultTzs = array_unique( array_merge( [ 'Etc/UTC' ], $randTzs ) );
		foreach ( array_filter( $request->get( 'tzs', $defaultTzs ) ) as $tz ) {
			foreach ( $identifiers as $identifier ) {
				if ( strtolower( $tz ) === strtolower( $identifier ) ) {
					$tz = $identifier;
					break;
				}
			}
			try {
				$tzs[] = new DateTimeZone( $tz );
			} catch ( Exception $exception ) {
				// Do something?
			}
		}

		$date = $request->get( 'date' );

		$tzsData = [];
		foreach ( $tzs as $tz ) {
			$startDay = new DateTime( $date . " 00:00:00 Z" );
			$endDay = new DateTime( $date . " 23:59:59.99 Z" );
			$tzsData[$tz->getName()] = [
				'tz' => $tz,
				'transitions' => [],
			];
			$transitions = $tz->getTransitions( $startDay->getTimestamp(), $endDay->getTimestamp() );
			// Not all timezones report a transition.
			if ( is_array( $transitions ) ) {
				foreach ( $transitions as $tran ) {
					$totalMinutes = $tran['offset'] / 60;
					$hours = floor( $totalMinutes / 60 );
					$minutes = $totalMinutes - ( $hours * 60 );
					$formattedOffset = ( $tran['offset'] > 0 ? '+' : '' )
						. str_pad( $hours, 2, '0', STR_PAD_LEFT )
						. ':' . str_pad( round( $minutes ), 2, '0', STR_PAD_LEFT );
					$tzsData[$tz->getName()]['transitions'][] = [
						'abbr' => $tran['abbr'],
						'isdst' => $tran['isdst'],
						'offset' => $tran['offset'],
						'offset_formatted' => $formattedOffset,
					];
				}
			}
		}

		$times = [];
		for ( $h = 0; $h < 24; $h++ ) {
			$times[$h] = [];
			foreach ( $tzs as $tz ) {
				$time = new DateTime( $date . " $h:00:00 Z" );
				$time->setTimezone( $tz );
				$transition = $tz->getTransitions( $time->getTimestamp(), $time->getTimestamp() );
				$newWeekday = $time->format( 'l' );
				$prevWeekday = $times[$h - 1][$tz->getName()]['weekday_name'] ?? '';
				$times[$h][$tz->getName()] = [
					'time' => $time->format( 'H:i' ),
					'show_dst' => $transition[0]['isdst'] && count( $tzsData[$tz->getName()]['transitions'] ) > 1,
					// Store weekday for next loop's comparison.
					'weekday_name' => $newWeekday,
					// Weekday to display in the UI table.
					'weekday' => $newWeekday != $prevWeekday ? $newWeekday : '',
				];
			}
		}

		return $this->render( 'home.html.twig', [
			'date' => new DateTime( $date . " 00:00:00 Z" ),
			'tzs' => $tzs,
			'tzs_data' => $tzsData,
			'times' => $times,
		] );
	}
}
