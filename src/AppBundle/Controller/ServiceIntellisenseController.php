<?php
/**
 * ServiceIntellisenseController abstract class declaration.
 *
 * This class should be inherited by all other controllers in order to provide
 * us with intellisense / code completion capabilities in IDE's.
 *
 * Ideally, we would upgrade to PHP 5.4 and use this as a trait.
 *
 * @author Elijah Ethun <elijahe@gmail.com>
 * @project Rpsls
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class ServiceIntellisenseController extends Controller {

  /**
   * @return \AppBundle\Util\RpslsHelper
   */
  protected function getRpslsHelper() {
    return $this->get('rpsls_helper');
  }

  /**
   * @return \AppBundle\Util\StatsHelper
   */
  protected function getStatsHelper() {
    return $this->get('stats_helper');
  }

  /**
   * @return \Doctrine\ORM\EntityManager
   */
  protected function getEm() {
    return $this->getDoctrine()->getManager();
  }

  /**
   * @return \Symfony\Bundle\FrameworkBundle\Routing\Router
   */
  protected function getRouter() {
    return $this->get('router');
  }

  /**
   * @todo Figure out where this is...
   */
  protected function getMemcached() {
    return $this->get('memcached');
  }

  /**
   * @todo Figure out where this is...
   */
  protected function getTemplating() {
    return $this->get('templating');
  }
}