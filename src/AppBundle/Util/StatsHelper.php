<?php

/**
 * StatsHelper class declaration.
 *
 * @author Elijah Ethun <elijahe@gmail.com>
 * @project Rpsls
 */

namespace AppBundle\Util;
use AppBundle\Exceptions\RpslsException;

class StatsHelper {

  private $em;

  /**
   * The constructor expects a document manager for connecting to database.
   *
   * @param \Doctrine\ORM\EntityManager $em
   */
  public function __construct($em) {
    $this->em = $em;
  }

  /**
   * Fetch the named result.
   *
   * One of 'win', 'loss', 'tied' is expected.  If none is found, an
   * exception is thrown.
   *
   * @param $status
   * @return mixed
   * @throws RpslsException
   * @throws \Doctrine\ORM\NoResultException
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  private function _getResult($status) {
    if (!in_array($status, array('win', 'loss', 'tied'))) {
      throw new RpslsException('Invalid result provided!');
    }

    $repository = $this->em->getRepository('AppBundle:Stats');

    // createQueryBuilder() automatically selects FROM AppBundle:Stats
    // and aliases it to "s"
    $qb = $repository->createQueryBuilder('s')
        ->select('COUNT(s.result) as result')
        ->where('s.result = :sstatus')
          ->setParameter('sstatus', $status);

    $result = $qb->getQuery()->getSingleResult();
    return $result['result'];
  }

  /**
   * Call the parent _getResult() method with 'win' as the variable.
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getWins() {
    return $this->_getResult('win');
  }

  /**
   * Call the parent _getResult() method with 'loss' as the variable.
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getLosses() {
    return $this->_getResult('loss');
  }

  /**
   * Call the parent _getResult() method with 'tied' as the variable.
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getTies() {
    return $this->_getResult('tied');
  }

  /**
   * Fetch the favorite choices for each of p1 and p2.
   *
   * @param $player
   * @return mixed
   * @throws RpslsException
   */
  private function _favoriteChoices($player) {
    if (!in_array($player, array('p1', 'p2'))) {
      throw new RpslsException('Invalid player provided!');
    }

    $repository = $this->em->getRepository('AppBundle:Stats');

    // createQueryBuilder() automatically selects FROM AppBundle:Stats
    // and aliases it to "s"
    $qb = $repository->createQueryBuilder('s')
        ->select(sprintf('s.choice_%s, COUNT(s.choice_%s) as result', $player,
            $player))
        ->addOrderBy('result', 'desc');

    $query = $qb->getQuery();
    $result = $query->getSingleResult();
    return $result[sprintf('choice_%s', $player)];
  }

  /**
   * Get the player 1 favorite choice
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getP1FavoriteChoice() {
    return $this->_favoriteChoices('p1');
  }

  /**
   * Get the player 2 favorite choice
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getP2FavoriteChoice() {
    return $this->_favoriteChoices('p2');
  }


  /**
   * Get all choices & associated accounts as aggregated results.
   *
   * @param $player
   * @return mixed
   * @throws RpslsException
   */
  private function _choices($player) {
    if (!in_array($player, array('p1', 'p2'))) {
      throw new RpslsException('Invalid player provided!');
    }

    $repository = $this->em->getRepository('AppBundle:Stats');

    // createQueryBuilder() automatically selects FROM AppBundle:Stats
    // and aliases it to "s"
    $qb = $repository->createQueryBuilder('s')
        ->select(sprintf('s.choice_%s as choice, COUNT(s.choice_%s) as result', $player,
            $player))
        ->addGroupBy('result')
        ->addOrderBy('result', 'desc');

    $query = $qb->getQuery();
    $result = $query->getResult();
    return $result;
  }

  /**
   * Get Player 1 choices & associated accounts as aggregated results.
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getP1Plays() {
    return $this->_choices('p1');
  }

  /**
   * Get Player 2 choices & associated accounts as aggregated results.
   *
   * @return mixed
   * @throws RpslsException
   */
  public function getP2Plays() {
    return $this->_choices('p2');
  }
}
