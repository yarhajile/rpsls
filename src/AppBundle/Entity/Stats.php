<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Util\Bunch;

/**
 * @ORM\Entity
 * @ORM\Table(name="stats")
 */
class Stats extends Bunch
{
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $choice_p1;

  /**
   * @ORM\Column(type="string")
   */
  protected $choice_p2;

  /**
   * Whether we 'won' or 'lost' or 'tied'
   * @ORM\Column(type="string")
   */
  protected $result;

}
