<?php

namespace App\Controller;

use App\MeetingTimes;
use App\Timezone;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController {

	/**
	 * @Route("/", name="home")
	 * @param Request $request
	 * @param MeetingTimes $meetingTimes
	 * @return Response
	 */
	public function index( Request $request, MeetingTimes $meetingTimes ) {
		$startTime = $request->get( 'startdate', date( 'Y-m-d' ) )
			. ' ' . $request->get( 'starttime', '00:00:00' ) . 'Z';
		$meetingTimes->setStartTime( new DateTime( $startTime ) );

		$endTime = $request->get( 'enddate', date( 'Y-m-d' ) )
			. ' ' . $request->get( 'endtime', '24:00:00' ) . 'Z';
		$meetingTimes->setEndTime( new DateTime( $endTime ) );

		$tzIdentifiers = $request->get( 'tzs', [] );
		foreach ( $tzIdentifiers as $tzIdentifier ) {
			try {
				$meetingTimes->addTimezone( new Timezone( $tzIdentifier ) );
			} catch ( Exception $exception ) {
				// @TODO Tell the user about the bad timezone.
			}
		}

		return $this->render( 'home.html.twig', [ 'mt' => $meetingTimes ] );
	}

	/**
	 * @Route("/search", name="search")
	 * @param Request $request
	 * @param MeetingTimes $meetingTimes
	 * @return Response
	 */
	public function search( Request $request, MeetingTimes $meetingTimes ) {
		return $this->json( $meetingTimes->identifierSearch( $request->get( 'q' ) ) );
	}
}
