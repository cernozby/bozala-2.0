<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Competitor
 **/
class Competitor extends BaseFactory
{
  
  /**
   * Competitor constructor.
   * @param Explorer $database
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'competitor';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  /**
   * @return string
   */
  public function getFullName() : string {
    return $this->get('first_name') .' '. $this->get('last_name');
  }
  
  public function getCategoryToPrereg() : array{
    $result = [];
    $openComps = $this->container->createService('compModel')->getOpenComps();
    foreach ($openComps as $comp) {
      foreach ($comp->getCategory() as $category) {
        if ($category->get('year_young') >= $this->get('year') || $category->get('year_old') <= $this->get('year') )
          $result[] = $comp;
      }
    }
    return $result;
  }
}