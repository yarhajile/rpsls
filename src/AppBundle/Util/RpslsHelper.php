<?php

/**
 * RpslsHelper class declaration.
 *
 * This class is responsible for determining the outcome of p1 vs p2 inputs.
 *
 * @author Elijah Ethun <elijahe@gmail.com>
 * @project Rpsls
 */

namespace AppBundle\Util;
use AppBundle\Exceptions\RpslsException;

class RpslsHelper {

  /**
   * Associative array of descriptive names and short-tags we use to associate
   * choices with outcomes.
   *
   * @var array
   */
  private $choices = array('rock' => 'R', 'paper' => 'P', 'scissors' => 'S',
      'lizard' => 'L', 'spock' => 'K');


  /**
   * This associative array defines the potential outcomes based on a
   * combination of player1 & player2 choice keys combined. i.e. player1 chooses
   * 'rock', the key is 'R' and when player2 chooses 'scissors' the key is 'S'.
   * So the combined key value would be 'RS' which translates to 'RS' which is
   * mapped to 'crushes' -- Rock crushes scissors;
   *
   * @var array
   */
  private $outcomes = array(
      'LK' => 'poisons', // Lizard poisons Spock
      'LP' => 'eats', // Lizard eats Paper
      'RL' => 'crushes', // Rock crushes Lizard
      'SL' => 'decapitates', // Scissors decapitates Lizard
      'KR' => 'vaporizes', // Spock vaporizes Rock
      'KS' => 'smashes', // Spock smashes Scissors
      'PK' => 'disproves', // Paper disproves Spock
      'PR' => 'covers', // Paper covers Rock
      'RS' => 'crushes', // Rock crushes Scissors
      'SP' => 'cut' // Scissors cut Paper
  );

  /**
   * Retrieve a tuple of the outcome text & 'win' / 'loss' ruling.
   *
   * This is our bread & butter right here.  We first get the short-tag
   * associated with the choices of our P1 and P2 users.
   *
   * Then, we concatenate those keys into an $outcome_key which is used to
   * find an element from our private $elements array.
   *
   * If the two keys are identical, a 'tie' is identified.
   * If the concatenated keys match the $outcomes keys, then a 'player 1'
   * match is determined
   * If the concatenated keys do NOT match the $outcome keys, the concatenated
   * key is reversed to match up inversely.
   * If that key matches, then a 'player 2' match is determined.
   *
   * If neither outcomes match, something has gone horribly wrong and an
   * exception is thrown... figure it out engineer!
   *
   * @param $p1
   * @param $p2
   * @throws RpslsException
   */
  public function play($p1, $p2) {
    $c1 = $this->choices[$p1];
    $c2 = $this->choices[$p2];

    $outcome_key = $c1 . $c2;

    if ($c1 == $c2) {
      return array('You tied... try again!', 'tied');
    }

    if (array_key_exists($outcome_key, $this->outcomes)) {
      // Player 1 wins;
      return array(sprintf('You win! - %s %s %s', $p1,
          $this->outcomes[$outcome_key], $p2), 'win');
    } elseif (array_key_exists(strrev($outcome_key), $this->outcomes)) {
      // Player 2 wins;
      return array(sprintf('Computer wins - %s %s %s', $p2,
          $this->outcomes[strrev($outcome_key)], $p1), 'loss');
    }
    else {
      throw new RpslsException(sprintf('Invalid outcome key: %s', $outcome_key));
    }
  }

  /**
   * Get a random choice from our choices array.
   *
   * @return mixed
   */
  public function getRandomChoice() {
    return array_rand($this->choices);
  }
}
