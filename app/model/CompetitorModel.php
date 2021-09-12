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

    /**
     * @param int $categoryId
     * @return Competitor[]
     */
    public function getByCategory(int $categoryId) {
        $result = [];
        foreach ($this->getTable('prereg')->select('*')->where('category_id = ?', $categoryId)->fetchAll() as $item) {
            $competitor = $this->container->createService('Competitor');
            $result[$item->offsetGet('competitor_id')] = $competitor->initId($item->offsetGet('competitor_id'));
        }

        return $result;
    }

    /**
     * @param int $smallerYear
     * @param int $biggerYear
     * @return Competitor[]
     */
    public function getCompetitorsBetweenYear(int $smallerYear, int $biggerYear, string $gender) : array {
        $result = $this->getTable()->select('*')
            ->where('year >= ?', $smallerYear)
            ->where('year <= ?', $biggerYear)
            ->where('gender = ?', $gender)
            ->fetchAll();

        return $this->arrayToObject($result);
    }
}