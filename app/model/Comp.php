<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Comp
 **/
class Comp extends BaseFactory
{
  
  /**
   * Comp constructor.
   * @param Explorer $database
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'comp';
    parent::__construct($database, $container, $linkGenerator);
  }

  public function isLeadComp() : bool {
    return $this->get('lead') > 0;
  }
  
  public function isBoulderComp() : bool {
    return $this->get('boulder') > 0;
  }
  
  public function isSpeedComp() : bool {
    return $this->get('speed') > 0;
  }

    /**
     *
     * @return Category[]
     */
  public function getCategory() : array {
    return $this->container->createService('categoryModel')->getByCompId($this->getId());
  }

  public function getCompName() : string {
      return $this->get('name');
  }

  public function getDate() {
      return $this->get('date');
  }
}