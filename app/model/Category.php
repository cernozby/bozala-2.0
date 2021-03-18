<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Category
 **/
class Category extends BaseFactory
{
  
  
  /**
   * Category constructor.
   * @param Explorer $database
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'category';
    parent::__construct($database, $container, $linkGenerator);
  }
  
}