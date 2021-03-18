<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class CompcatModel
 * @package App\model
 */
class CompcatModel extends BaseModel
{
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'compcat';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  public function getByComp(int $categoryId) : array {
    $this->arrayToObject($this->getTable()->select('*')->where('category_id = ?', $categoryId)->fetchAll());
  }
}