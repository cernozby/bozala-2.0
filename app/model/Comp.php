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
     * Vrátí pole categorii
     *
     * @return Category[]
     */
  public function getCategory() : array {
    return $this->container->createService('categoryModel')->getByCompId($this->getId());
  }

  public function getCategoryForUser($userId) {
      $obj = [];
      $array = $this->db->query("SELECT c.* FROM category c 
                            JOIN prereg p ON p.category_id = c.id_category 
                            JOIN competitor r ON r.id_competitor = p.competitor_id
                            WHERE r.user_id = ? AND c.comp_id = ?
                            GROUP BY c.id_category", $userId, $this->getId())->fetchAll();


      foreach ($array as $item) {
          $obj[$item->offsetGet('id_category')] = $this->container->createService('category')->initId($item->offsetGet('id_category'));
      }
      return $obj;
  }

  public function getCompName() : string {
      return $this->get('name');
  }

  public function getDate() {
      return $this->get('date');
  }

  public function isOpenPrereg() : bool {
      return boolval($this->get('online_registration'));
  }

  public function getBoulderResultType() : string {
      return $this->get('boulder_result');
  }

  public function isBoulderResultAmateur() : bool {
      return $this->getBoulderResultType() == CompModel::AMATEUR_RESULT;
  }

    public function isBoulderResultComp() : bool {
        return $this->getBoulderResultType() == CompModel::COMP_RESULT;
    }
}