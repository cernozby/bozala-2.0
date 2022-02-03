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
     * @return string|null
     */
    public function getName() : string {
      return $this->get('name');
  }

    /**
     * @return string | null
     */
  public function getGender() : string{
        return $this->get('gender');
  }

    /**
     * Pokud muzu zobrazit vysledky, vraci true;
     *
     * @return bool
     */
  public function visible_result() : bool {
      return $this->get('visible_result') == 1;
  }

  public function getPointsForBoulder() {
      /** @var ResultModel $resultModel */
      $resultModel = $this->container->createService('resultModel');
      return $resultModel->getBoulderValueAmateurResult($this->getBoulderResult('boulder_kva'));
  }

    /**
     * Vrátí výsledky jako pole
     *
     * @param string $type | typ výsledků (boulder_kva, lead_fi,....)
     * @return array
     */
  public function getBoulderResult(string $type): array {
      /** @var ResultModel $resultModel */
      $resultModel = $this->container->createService('resultModel');
      return $resultModel->getResult($type, $this->getId());
  }

    /**
     * Vrati kompletní výsledky ve tvaru
     * (bx => r)n
     * resultColomun => array
     * resultKey => x
     * place => int
     * competitor => class competitor
     *
     * @param string $roundType | typ kola - kvalifikace | final
     */
  public function getBoulderFullResult(string $roundType): array {
      $resultType = $this->getComp()->get('boulder_result');
      $results = $this->getBoulderResult('boulder_kva');
      if (!$results) {
          return array();
      }

      /** @var ResultModel $resultModel */
      $resultModel = $this->container->createService('resultModel');


      switch ($resultType) {
          case CompModel::AMATEUR_RESULT:
              return $this->mergeCompetitorWithResult($resultModel->getBoulderFullResultAmateurSystem($resultType, $results));
          case CompModel::COMP_RESULT:
              return $this->mergeCompetitorWithResult($resultModel->getBoulderFullResultCompSystem($resultType, $results));
          default:
              throw new \InvalidArgumentException("Wrong result system");
      }
  }

    /**
     * Přidá do pole výsledků pole competitor s instanci Competitor
     *
     * @param array $result
     * @return array
     */
  private function mergeCompetitorWithResult(array $result): array {
      foreach ($this->getPreregCompetitors() as $competitor) {
          if (array_key_exists($competitor->getId(), $result)) {
              $result[$competitor->getId()]['competitor'] = $competitor;
          }
      }

      return $result;
  }

    /**
     * @return string
     */
    public function getCategoryName() {
     return $this->getName() .' (' . $this->get('year_young') . ' - ' . $this->get('year_old') . ')';
  }
  
}