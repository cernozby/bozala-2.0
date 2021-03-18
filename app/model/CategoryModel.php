<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class CategoryModel
 * @package App\model
 */
class CategoryModel extends BaseModel
{
  
  public const MALE = 'male';
  public const FEMALE = 'female';
  public const BOTH = 'both';
  
  public static array $genders = array(
    self::MALE => 'Mužská',
    self::FEMALE => 'Ženská',
    self::BOTH => 'Smíšená'
  );
  
  
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'category';
    parent::__construct($database, $container, $linkGenerator);
  }
  
  public function newCategory($data) : void {
    $this->db->table($this->getTableName())->insert($data);
  }
  
  public function getByCompId($compId) : array {
    return $this->arrayToObject($this->getTable()
                                  ->select('*')
                                  ->where('comp_id = ?', $compId)
                                  ->order('year_young')
                                  ->fetchAll());
  }
}