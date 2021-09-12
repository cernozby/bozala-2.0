<?php

namespace App\model;

use App\components\MyHelpers;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Category
 * @package App\model
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


    /**
     * @return Competitor[]
     */
    public function getUnpreregCompetitors() {
        /** @var  CompetitorModel $competitorModel */
        $competitorModel = $this->container->createService('competitorModel');
        $unprereg = $competitorModel->getCompetitorsBetweenYear($this->get('year_old'), $this->get('year_young'), $this->get('gender'));
        $prereg = $this->getPreregCompetitors();
        bdump($prereg);
        bdump($unprereg);

        return array_diff_key($unprereg, $prereg);
    }
    /**
     * @return Competitor[]
     */
    public function getPreregCompetitors() : array {
      /** @var CompetitorModel $competitorModel */
      $competitorModel = $this->container->createService('competitorModel');
      return $competitorModel->getByCategory($this->getId());
  }

    /**
     * @return Comp|null
     */
    public function getComp() : ?Comp {
      return $this->container->createService('Comp')->initId($this->get('comp_id'));
  }

    /**
     * @return null
     */
    public function getName() {
      return $this->get('name');
  }

    /**
     * @return string
     */
    public function getCategoryName() {
     return $this->getName() .' (' . $this->get('year_young') . ' - ' . $this->get('year_old') . ')';
  }
  
}