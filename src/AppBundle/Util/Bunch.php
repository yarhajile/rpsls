<?php

/**
 * Bunch class declaration.
 *
 * Bunch objects allow associative arrays to be safely passed to an object
 * constructor. An example:
 *
 * class Foo extends Bunch {
 *   function setAnApple($apple) {
 *     //
 *   }
 *   function setAnOrange($orange) {
 *     //
 *   }
 * }
 *
 * The setter methods get automatically called here.
 * $my_foo = new Foo(array(
 *     'an_apple' => $my_apple, 'an_orange' => $my_orange));
 *
 * Class setter methods are expected in CamelCase; in the constructor, keys
 * should utilize underscores and lower-case. If a key in the constructor
 * array can't be mapped to a setter, an exception will be raised.
 *
 * @author Elijah Ethun <elijahe@gmail.com>
 * @project Rpsls
 */

namespace AppBundle\Util;

use AppBundle\Exceptions\BunchException;

abstract class Bunch {
  public function __construct($properties = array()) {
    foreach($properties as $key => $val) {
      // Convert from lower-case with underscores to camel-case.
      $setter = sprintf(
          'set%s',preg_replace('/(?:^|_)(.?)/e', "strtoupper('$1')", $key));

      $this->{$setter}($val);
    }
  }

  /**
   * Generic purpose getter & setter.
   *
   * This works because we have a Symfony PropertyAccessor component that has
   * magicCall enabled (see config.yml).
   *
   * Classes extending bunch require the use of getVal() and setVal() style
   * methods throughout the app.  Majority of the time, these are repetitive
   * and require an additional amount of administrative overhead given the fact
   * we create multiple classes of this inheritance for each client.
   *
   * Note that we require that a property exists for the named getter / setter.
   * If we attempt to access a method here that doesn't have an associated
   * property, a BunchException is thrown.
   *
   * Additionally, we accept getters in the form of $obj->val() and $obj->Val()
   * so that we can access properties from the likes of Twig via
   * {{ user.Val() }}.
   *
   * @param $method
   * @param $args
   * @return $this
   * @throws BunchException
   */
  public function __call($method, $args) {
    $action = strtolower(substr($method, 0, 3));

    // Symfony resolves property access in multiple ways including
    // 'getPropertyName', 'PropertyName' and 'property_name'.  Make sure we can
    // accommodate all, defaulting to 'get' if trying to access the property
    // directly.
    if (in_array($action, array('get', 'set'))) {
      $action = substr($method, 0, 3);
      $property = substr($method, 3, strlen($method));
    }
    else {
      $action = 'get';
      $property = $method;
    }

    // Convert $name from getVariableName to variable_name
    $property = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2',$property));

    if (!property_exists($this, $property)) {
      throw new BunchException(sprintf(
          'Class "%s" has no property "%s"', get_class($this), $property));
    }

    if ($action === 'get') {
      return $this->$property;
    }

    // Our setters in Bunch children only have one field.
    $this->$property = $args[0];

    return $this;
  }
}