<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class CompetitorModel
 * @package App\model
 */
class CompetitorModel extends BaseModel
{
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'competitor';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  public function getByUser($userId) : array {
    return $this->arrayToObject($this->getTable()->select('*')->where('user_id =?', $userId)->fetchAll());
  }
  
  public function newCompetitor($data) : void {
    $this->db->table($this->getTableName())->insert($data);
  }
}