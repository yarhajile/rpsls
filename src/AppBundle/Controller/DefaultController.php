<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Controller\ServiceIntellisenseController;
use AppBundle\Entity\Stats;

class DefaultController extends ServiceIntellisenseController {
  /**
   * Show the landing page.
   *
   * @param Request $request
   * @return Response
   */
  public function indexAction(Request $request) {
    return $this->render(
        'AppBundle:Default:index.html.twig', array());
  }

  /**
   * Main play action for calculating player choices and producing stats.
   *
   * This ajax action is responsible for handing off the comparison of submitted
   * POST actions (if present) to the game playing routines that determine
   * the outcome.  The outcome is produced as a tuple of the textual
   * representation of the results and the 'result' itself which will either
   * be one of 'win', 'loss', 'tied'.
   *
   * Additionally, we store the stats of the choices & result to our Stats
   * entity for future retrieval / collation.
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \AppBundle\Exceptions\RpslsException
   */
  public function playAction(Request $request) {
    $rh = $this->getRpslsHelper();

    // We absolutely need a choice from the user.
    if (!$choice_p1 = $request->get('choice')) {
      return new Response('Ya gotta make a choice!');
    }

    // Fetch a random choice of options for this game.
    $choice_p2 = $rh->getRandomChoice();

    // Compare the player's choices and create the outcome tuple.
    $outcome = $rh->play($choice_p1, $choice_p2);

    // Store the game stats.
    $stat = new Stats(array(
        'choice_p1' => $choice_p1,
        'choice_p2' => $choice_p2,
        'result' => $outcome[1]));
    $em = $this->getEm();
    $em->persist($stat);
    $em->flush();

    return $this->render(
        'AppBundle:Default:results.html.twig', array(
            'choice_p1' => $choice_p1, 'choice_p2' => $choice_p2,
            'outcome' => $outcome[0]));
  }

  /**
   * Provide StatsHelper to the front-end call for access to aggregate stats.
   *
   * This ajax action provides the methods present in the StatsHelper service
   * so that we can poll wins, losses, ties, favorite choices, plays, etc. of
   * the user playing.
   *
   * @param Request $request
   * @return Response
   * @throws \AppBundle\Exceptions\RpslsException
   */
  public function statsAction(Request $request) {
    $sh = $this->getStatsHelper();

    return $this->render(
        'AppBundle:Default:stats.html.twig', array('stats' => $sh));
  }

}
