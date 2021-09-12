<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Utils\ArrayHash;

/**
 * Class CompetitorModel
 * @package App\model
 */
class CompModel extends BaseModel
{
  public const COMP_RESULT = 'comp';
  public const AMATEUR_RESULT = 'amateur';
  
  public static array $result_type = array(
    self::COMP_RESULT => 'závodní',
    self::AMATEUR_RESULT => 'amatérské'
  );
  
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
    $this->table = 'comp';
    parent::__construct($database, $container, $linkGenerator);
  }

    /**
     * @return Comp[]
     */
  public function getOpenComps() : array {
    return $this->arrayToObject($this->getTable()->select('*')->where('online_registration = ?', 1)->fetchAll());
  }

  /**
  * @return Comp[]
  */
  public function getEditableResultComps() : array {
      return $this->arrayToObject($this->getTable()->select('*')->where('editable_result = ?', 1)->fetchAll());
  }

  public function getVisiblePreregComp() : array {
      return $this->arrayToObject($this->getTable()->select('*')->where('visible_prereg = ?', 1)->fetchAll());
  }
  
  public function newComp(ArrayHash $data) : void {
    if ($data->offsetGet('boulder') === '') {
      $data->offsetUnset('boulder_final');
      $data->offsetUnset('boulder_result');
    }
  
    if ($data->offsetGet('lead') === '') {
      $data->offsetUnset('lead_final');
      $data->offsetUnset('lead_result');
    }
  
    if ($data->offsetGet('speed') === '') {
      $data->offsetUnset('speed_final');
      $data->offsetUnset('speed_result');
    }
    
    foreach ($data as $key => $value) {
      if (!$value) {
        $data->offsetUnset($key);
      }
    }
    
    $this->db->table($this->getTableName())->insert($data);
  }
}