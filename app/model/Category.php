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
    /** @var ResultModel $resultModel */
    private $resultModel;

    /** @var Result $result */
    private $result;
  
  /**
   * Category constructor.
   * @param Explorer $database
   * @param Container $container
   * @param LinkGenerator $linkGenerator
   */
  public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
      $this->table = 'category';
      parent::__construct($database, $container, $linkGenerator);

      $this->resultModel = $this->container->createService('resultModel');
      $this->result = $this->container->createService('result');
  }


  public function delete(): void {
      foreach ($this->resultModel->getAllResultToCategory($this->getId()) as $result) {
          $result->delete();
      }
      parent::delete();
  }


    /**
     * @return Competitor[]
     */
    public function getUnpreregCompetitors() {
        /** @var  CompetitorModel $competitorModel */
        $competitorModel = $this->container->createService('competitorModel');
        $unprereg = $competitorModel->getCompetitorsBetweenYear($this->get('year_old'), $this->get('year_young'), $this->get('gender'));
        $prereg = $this->getPreregCompetitors();
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
     * @return Competitor[]
     */
    public function getPreregCompetitorsByUser($userId) : array {
        /** @var CompetitorModel $competitorModel */
        $competitorModel = $this->container->createService('competitorModel');
        return array_filter($competitorModel->getByCategory($this->getId()), (fn ($item) => $item->get('user_id') == $userId));
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

    /**
     * @return string
     */
    public function getCategoryName() {
        return $this->getName() .' (' . $this->get('year_young') . ' - ' . $this->get('year_old') . ')';
    }

  public function getPointsForBoulder(string $type) {
      return $this->resultModel->getBoulderValueAmateurResult($this->getResult($type));
  }

    /**
     * Vrátí výsledky jako pole
     *
     * @param string $type | typ výsledků (boulder_kva, lead_fi,....)
     * @return array
     */
  public function getResult(string $type): array {
      return $this->resultModel->getResult($type, $this->getId());
  }

  public function getSpeedFullResult() : array {
      $results = $this->getResult(ResultModel::SPEED_KVA);
      if (!$results) {
          return array();
      }

      return $this->mergeCompetitorWithResult($this->resultModel->getSpeedFullResult($results));
  }


  public function makeFinalListLead() : void {
      $this->makeFinalList(ResultModel::LEAD_FI, $this->getComp()->getCountCompetitorsLeadFinal(), $this->getLeadResult());
  }

  public function makeFinalListBoulder(): void {
      $this->makeFinalList(ResultModel::BOULDER_FI, $this->getComp()->getCountCompetitorsBoulderFinal(), $this->getBoulderFullResult(ResultModel::BOULDER_KVA));

  }

  private function writeCompetitorToFinalList($idCompetitor, $type) {
          $this->getTable('result')
              ->insert(['category_id' => $this->getId(),
                        'competitor_id' => $idCompetitor,
                        'type' => $type]);

  }

    /**
     * @param string $finalType
     * @param int $competitors
     * @param array $results
     */
  private function makeFinalList(string $finalType, int $competitors, array $results) : void {
      $lastPlace = -1;

      $this->resultModel->deleteByType($finalType, $this->getId());

      foreach ($results as $idCompetitor => $result) {
          if ($competitors < 1 && $lastPlace != $result['place']) {
              break;
          }

          $this->writeCompetitorToFinalList($idCompetitor, $finalType);
          $competitors -= 1;
          $lastPlace = $result['place'];
      }
  }


  public function getLeadResult(): array {
      return $this->mergeCompetitorWithResult($this->resultModel->getFullLeadResult($this->getId()));
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
      $resultsKva = $this->getResult(ResultModel::BOULDER_KVA);
      $resultsFi = $this->getResult(ResultModel::BOULDER_FI);

      if (!$resultsKva) {
          return array();
      }


      if ($roundType == ResultModel::BOULDER_FI) {
          return $this->mergeCompetitorWithResult($this->resultModel->getBoulderFinResult($resultsKva, $resultsFi, $resultType));
      }

      switch ($resultType) {
          case CompModel::AMATEUR_RESULT:
              return $this->mergeCompetitorWithResult($this->resultModel->getBoulderKvalResultAmateurSystem($resultsKva));
          case CompModel::COMP_RESULT:
              return $this->mergeCompetitorWithResult($this->resultModel->getBoulderKvalResultCompSystem($resultsKva));
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


  
}