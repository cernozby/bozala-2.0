<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Result
 **/
class Result extends BaseFactory
{


    /**
     * Comp constructor.
     * @param Explorer $database
     * @param Container $container
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
        $this->table = 'result';
        parent::__construct($database, $container, $linkGenerator);
    }

    public function getResultAsArray() {
        return json_decode($this->get('result'), true);
    }

    public function getPointPerBoulder(array $boulderPoint) {
        return array_sum(array_map(
            function ($key, $value) use ($boulderPoint) {if ($value > 0) {return $boulderPoint[$key];}},
            array_keys($this->getResultAsArray()),
            $this->getResultAsArray()));
    }

    public function getBestTime() {
        if ($this->get('type') !== ResultModel::SPEED_KVA) {
            return null;
        }

        $data = $this->getResultAsArray();
        $max = max($data);
        return min(array_map(fn ($item) => $item == 0 ? $max + 1 : $item , $data));
    }

    public function getSumBoulderTopZone() : array {
        $result['T'] = 0;
        $result['Z'] = 0;
        $result['PT'] = 0;
        $result['PZ'] = 0;
        foreach ($this->getResultAsArray() as $key => $item) {
            //je to top
            if (strpos($key, 't')) {
                if ($item > 0) {
                 $result['T'] += 1;
                 $result['PT'] += $item;
                }
            } else {
                if ($item > 0) {
                    $result['Z'] += 1;
                    $result['PZ'] += $item;
                }
            }
        }
        return $result;
    }

    /**
     * @param $type
     * @param $competitorId
     * @return Result|null
     */
    public function initByTypeAndCompetitorId($type, $competitorId) : ?object {
        $result = $this->getTable()
            ->select('*')
            ->where('type = ?', $type)
            ->where('competitor_id = ?', $competitorId)
            ->fetch();

        if (!$result) {
            return null;
        }
        return $this->initId($result->id_result);
    }

    /**
     * @return Competitor
     */
    public function getCompetitor() : Competitor {
        return $this->container->createService('Competitor')->initId($this->get('competitor_id'));

    }

    /**
     * @return Category
     */
    public function getCategory() : Category {
        return $this->container->createService('Category')->initId($this->get('category_id'));

    }

    public function getCompetitorId() {
        return $this->get('competitor_id');
    }

}