<?php

/**
 * RpslsFunctionalWebTestCase class declaration.
 *
 * Unlike our unit tests, functional web tests require the extra overhead of
 * a connection to a test database, and a full Symfony application environment.
 *
 * @author Elijah Ethun <elijahe@gmail.com>
 * @project Rpsls
 */

namespace AppBundle\Tests\Functional;

use AppBundle\Exceptions\RpslsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class RpslsFunctionalWebTestCase extends WebTestCase {

  /**
   * @var \Doctrine\ORM\EntityManager
   */
  protected $em;

  protected $session;

  protected $service_container;

  public function setUp() {
    static::$kernel = static::createKernel();
    static::$kernel->boot();

    $this->em = static::$kernel->getContainer()->getDoctrine()->getManager();

    $this->service_container = static::$kernel->getContainer();
  }

  /**
   * Assert that two arrays hold identical values, irrespective of order.
   *
   * @param array $a1
   * @param array $a2
   */
  protected function assertArrayValsEqual($a1, $a2) {
    $this->assertTrue($this->_arrayValsEqual($a1, $a2));
  }

  /**
   * Check to see if two arrays hold identical values, irrespective of order.
   *
   * @param array $a1
   * @param array $a2
   * @return boolean
   */
  protected function _arrayValsEqual($a1, $a2) {
    return (!$this->_arrayDiff($a1, $a2) && !$this->_arrayDiff($a2, $a1)
        && (count($a1) == count($a2)));
  }

  protected function _arrayDiff($a1, $a2) {
    return array_udiff($a1, $a2,
        function ($a, $b) {
          if (print_r($a, TRUE) == print_r($b, TRUE)) {
            return 0;
          }
          return (print_r($a, TRUE) > print_r($b, TRUE)) ? 1 : -1;
        }
    );
  }
}