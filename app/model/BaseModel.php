<?php
namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\DI\Container;

class BaseModel {
  
  
  /**
   *
   * @var Explorer @inject
   */
  public Explorer $db;
  
  /**
   *
   * @var Container
   */
  public Container $container;
  
  /**
   *
   * @var LinkGenerator
   */
  public LinkGenerator $linkGenerator;
  
  /**
   * @var string
   */
  public string $table;
  
  
  /**
   *
   * @param Explorer $database Database connection
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    
    $this->db = $database;
    $this->container = $container;
    $this->linkGenerator = $linkGenerator;
  }

  
  /**
   * Get name of object table
   * @return string
   */
  public function getTableName() : string {
    return $this->table;
  }
  
  /**
   * @param array $array
   * @return array
   */
  public function arrayToObject(array $array) : array {
    $objects = array();

    foreach ($array as $a) {
      $instance = $this->container->createInstance(str_replace('Model', '', get_class($this)));
      $objects[$a->getPrimary()] = $instance->initData($a);
    }
    return $objects;
  }
  
  /**
   * Get primary name of table
   */
  protected function getPrimaryName() : string {
    return 'id_' . $this->table;
  }
  
  /**
   * @param string|null $name
   */
  public function getTable(string $name = null) : Selection  {
    if ($name) {
      return $this->db->table($name);
    }
    return $this->db->table($this->getTableName());
  }
  
  /**
   * @return array
   */
  public function getAll() : array {
    return $this->getTable()->select('*')->fetchAll();
  }
  
  /**
   * @param string $column
   * @return array|null
   */
  public function getAllFromOneColumn(string $column) : array {
    return $this->getTable()->select($column)->fetchPairs(null, $column) ? : [];
  }
  
  /**
   * @param array $columns
   * @return array|null
   */
  public function getAllColumns(array $columns) :? array {
    return $this->getTable()->select($columns)->fetchAll();
  }
  
  /**
   * @param string $column
   * @param string $value
   * @return bool
   */
  public function existInColumn(string $column, string $value) : bool {
    return (bool)$this->db->table($this->table)->where($column . ' = ?', $value)->fetch();
  }
  
  public function getAllOrder($column) : array {
    return $this->getTable()->select('*')->order($column)->fetchAll();
  }
  
  public function getAllAsObj(string $orderColumn = '') : array {
    if ($orderColumn) {
      return $this->arrayToObject($this->getAllOrder($orderColumn));
    }
    return $this->arrayToObject($this->getAll());
  }
}