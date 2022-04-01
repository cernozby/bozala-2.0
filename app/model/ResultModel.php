<?php

namespace App\model;

use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class ResultModel
 * @package App\model
 */
class ResultModel extends BaseModel
{
    //typy vysledků
    const BOULDER_KVA = 'boulder_kva';
    const BOULDER_FI = 'boulder_fi';
    const LEAD_FI = 'lead_fi';
    const LEAD_KVA = 'lead_kva';
    const SPEED_KVA = 'speed_kva';
    const SPEED_FI = 'speed_fi';


    //typy vyhodnocovani
    const BOULDER_RESULT_COMP = 'comp';
    const BOULDER_RESULT_AMATEUR = 'amateur';

    const BOULDER_AMATEUR_RESULT_VALUE = 100;

    /**
     * CategoryModel constructor.
     * @param Explorer $database
     * @param Container $container
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
        $this->table = 'result';
        parent::__construct($database, $container, $linkGenerator);
    }

    public function deleteByType($type, $categoryId) : void {
        $this->getTable()->select('*')
            ->where('type = ?', $type)
            ->where('category_id = ?', $categoryId)
            ->delete();
    }

    public function saveResult(array $data) {
        $idCategory = $data['idCategory'];
        $type = $data['type'];
        unset($data['idCategory']);
        unset($data['type']);

        foreach ($data as $competitorId => $item) {
            //odstraneni starych
            $this->getTable()
                ->select('*')
                ->where('competitor_id = ?',$competitorId)
                ->where('type = ?',$type)
                ->where('category_id = ?', $idCategory)
                ->delete();

            //ulozeni novych
            $this->getTable()->insert([
                'type' => $type,
                'competitor_id' => $competitorId,
                'category_id' => $idCategory,
                'result' => json_encode($item)
                ]);
        }
    }

    /**
     *
     * Vrací pole instanci Result
     *
     *
     * @param string $type
     * @param int $categoryId
     * @return Result[]
     */
    public function getResult(string $type, int $categoryId) : array {
        return
            $this->arrayToObject(
            $this->getTable()->select('*')
            ->where('category_id = ?', $categoryId)
            ->where('type = ?', $type)
            ->fetchAll());
    }


    /**
     * @param int $categoryId
     * @return Result[]
     */
    public function getAllResultToCategory(int $categoryId) : array {
        return
            $this->arrayToObject(
                $this->getTable()->select('*')
                    ->where('category_id = ?', $categoryId)
                    ->fetchAll());
    }

    /**
     *
     * Vraci pole ve formě id_competitor => result
     *
     * @param Result[] $data
     * @return array
     */
    public function resultToCompetitor(array $data) : array {
        $result = [];
        foreach ($data as $item) {
            $result[$item->get('competitor_id')] = $item->getResultAsArray();
        }

        return $result;
    }

    /**
     * @param Result[] $data
     */
    public function getBoulderValueAmateurResult(array $data) {
        $result = array_map(fn ($value) : int => 0, current($data)->getResultAsArray());
        foreach ($data as $item) {
            foreach ($item->getResultAsArray() as $key => $value) {
                if ($value > 0) {
                    $result[$key] += 1;
                }
            }
        }
        //ohodnoceni bouldru
        return array_map(fn($value): float => $value > 0 ? self::BOULDER_AMATEUR_RESULT_VALUE / $value : 0, $result);
    }


    public function leadRoutePlace ($data) {
        $helpArray = array(); //pomocny vysledek pro jednu cestu
        $results = array(); // celkovy vysledek, format idCompetiro => [route-N => misto]

        //pokud je dalsi cesta, podminka je true
        while(!empty(end($data))) {

            //srovnani pole do formatu idCompetitor => idRoute
            foreach ($data as $key => $item) {
                $routeKey = array_key_first($item);
                $helpArray[$key] = array_shift($data[$key]);
            }


            //nahrazeni hodnot top a +
            $helpArray = array_map(fn($value) => strtolower($value) == "top" ? 1000 : $value, $helpArray);
            $helpArray = array_map(
                fn($value) => strpos($value, "+") ? (((int)str_replace("+", '', $value)) * 2) + 1 : (int)$value * 2,
                $helpArray);

            //serazeni pole
            uasort($helpArray, (fn($a, $b) => $a < $b ? 1 : -1));
            $helpArray = array_map(fn($value) => array('resultKey' => $value), $helpArray);

            $this->addPlace($helpArray);

            foreach ($helpArray as $key => $value) {
                $results[$key][$routeKey . "-q"] = $value['place'];
            }
        }
        return $results;

    }

    public function leadRoutePlaceForAll($data) {
        foreach ($data as $key => $item) {
            $data[$key] = pow(array_product($item), (1 / count($item)) );
        }

        return $data;
    }

    /**
     * @param Result[] $data
     * @return array
     */
    public function getLeadResult(array $data) {
        $result = array();
        $helpArray = array();

        foreach ($data as $item) {
            $helpArray[$item->getCompetitorId()] = $item->getResultAsArray();
        }
        $helpArray = $this->leadRoutePlace($helpArray);
        $resultForAllRoute = $this->leadRoutePlaceForAll($helpArray);


        foreach ($data as $item) {
            $result[$item->get('competitor_id')] =
                $item->getResultAsArray() +
                ['resultColumn' => $resultForAllRoute[$item->getCompetitorId()]] +
                ['resultKey' => number_format($resultForAllRoute[$item->getCompetitorId()],2,'.','')] +
                $helpArray[$item->getCompetitorId()];
        }
        uasort($result, (fn($a, $b): int => $a['resultColumn'] > $b['resultColumn'] ? 1 : -1));

        $this->addPlace($result);
        return $result;
    }

    /**
     * @param Result[] $data
     * @return array
     */
    public function getSpeedFullResult(array $data): array {

        foreach ($data as $item) {
            $result[$item->get('competitor_id')] =
                $item->getResultAsArray() +
                ['resultColumn' => $item->getBestTime()] +
                ['resultKey' => $item->getBestTime()];

        }

        uasort($result, (fn($a, $b): int => $a['resultColumn'] > $b['resultColumn'] ? 1 : -1));
        $this->addPlace($result);
        return $result;
    }

    private function cmpBoulderFinAmateurResult(): \Closure {
        return function ($a, $b) {
            if ($a['resultColumn'] == $b['resultColumn']) {
                return $a['resultColumnKva'] > $b['resultColumnKva'] ? -1 : 1;
            }
            return $a['resultColumn'] > $b['resultColumn'] ? -1 : 1;
        };
    }

    /**
     * @param Result[] $data
     * @return array
     */
    public function getBoulderFinResult(array $dataKva, array $dataFi, string $resultType): array {
        $resultKva = $resultType == self::BOULDER_RESULT_AMATEUR ?
            $this->getBoulderKvalResultAmateurSystem($dataKva):
            $this->getBoulderKvalResultCompSystem($dataKva);


        $resultFi = $resultType == self::BOULDER_RESULT_AMATEUR ?
            $this->getBoulderKvalResultAmateurSystem($dataFi):
            $this->getBoulderKvalResultCompSystem($dataFi);


        foreach ($resultFi as $key => $item) {
            $resultFi[$key]['resultColumnKva'] = $resultKva[$key]['resultColumn'];
            $resultFi[$key]['resultKeyKva'] = $resultKva[$key]['resultKey'];
        }

        uasort($resultFi, $this->cmpBoulderFinAmateurResult());
        $this->addPlaceFullLead($resultFi, $this->groupByTwoKeys($resultFi, 'resultKey', 'resultKeyKva'));

        return $resultFi;
    }


    /**
     * @param Result[] $data
     * @return array
     */
    public function getBoulderKvalResultAmateurSystem(array $data): array {
        $result = array();

        $boulderValue = $this->getBoulderValueAmateurResult($data);
        foreach ($data as $item) {
            $points = $item->getPointPerBoulder($boulderValue);

            $result[$item->get('competitor_id')] =
                $item->getResultAsArray() +
                ['resultColumn' => $points] +
                ['resultKey' =>    number_format($points,2,'.','')]+
                ['pointsForBoulder' => $boulderValue];
        }

        //serazeni podle vysledku pro profi boulder
        uasort($result, (fn($a, $b): int => $a['resultColumn'] < $b['resultColumn'] ? 1 : -1));
        $this->addPlace($result);
        return $result;

    }


    /**
     * @param string $resultType
     * @param Result[] $data
     * @return array
     */
    public function getBoulderKvalResultCompSystem(array $data): array {
        $result = array();
        foreach ($data as $item) {
            $result[] = ['result' => $item, 'resultColumn' => $item->getSumBoulderTopZone()];
        }

        //serazeni podle vysledku pro profi boulder
        uasort($result, $this->cmpOrderBoulderCompResult());


        $newResult = array();
        foreach ($result as $item) {
            $newResult[$item['result']->get('competitor_id')] =
                $item['result']->getResultAsArray() +
                ['resultColumn' => $item['resultColumn']] +
                ['resultKey' =>    $item['resultColumn']['T'] .
                                    $item['resultColumn']['Z'] .
                                    $item['resultColumn']['PT'] .
                                    $item['resultColumn']['PZ']];
        }

        $this->addPlace($newResult);
        return $newResult;
    }

    public function addPlace(&$data) {
        foreach ($this->addPlaceToCompetitorId($this->groupByResult($data)) as $key => $place) {
            $data[$key]['place'] = $place;
        }
    }

    /**
     * vrací pole ve formatu result => idCompetitor[], setridene podle nejlepsiho vysledku
     *
     * @param array $data
     * @return array
     */
    private function groupByResult(array $data): array {
        $reduceData = (array_combine(array_keys($data), array_column($data, 'resultKey')));
        $result = array();
        foreach ($reduceData as $key => $item) {
            if (array_key_exists($item, $result)) {
                array_push($result[$item], $key);
            } else {
                $result[$item] = array($key);
            }
        }
        return $result;
    }

    private function addPlaceToCompetitorId(array $data): array {
        $result = [];
        $index = 1;

        foreach ($data as $item) {
            $samePlace = sizeof($item);
            $place = ($index + ($index + $samePlace - 1)) / 2;
            foreach ($item as $idCompetitor) {
                $result[$idCompetitor] = $place;
                $index++;
            }
        }
        return $result;
    }

    public function getCompetitors(int $categoryId, string $type): array {
        $return = [];
        $result = $this->getTable()->select('*')
            ->where('category_id = ?', $categoryId )
            ->where('type = ?', $type)
            ->fetchAll();
        foreach ($result as $item) {
            $return[$item['competitor_id']] = $this->container->createService('Competitor')->initId($item['competitor_id']);
        }

        return $return;
    }

    private function cmpFunctionLead(): \Closure {
        return function ($a, $b) {
                if ($a['resultKeyFinal'] == $b['resultKeyFinal']) {
                    return $a['resultKey'] < $b['resultKey'] ? -1 : 1;
                }
                return $a['resultKeyFinal'] < $b['resultKeyFinal'] ? -1 : 1;
        };
    }

    private function groupByTwoKeys(array $result, string $key1, string $key2): array {
        $helpArray = [];
        foreach ($result as $key => $item) {
            if (isset($helpArray[$item[$key1] . $item[$key2]])) {
                array_push($helpArray[$item[$key1] . $item[$key2]], $key);
            } else {
                $helpArray[$item[$key1] . $item[$key2]] = array($key);
            }
        }

        return $helpArray;
    }

    private function addPlaceFullLead(array &$result, array $helpArray) {
        $index = 1;
        foreach ($helpArray as $item) {
            $samePlace = count($item);
            $place = ($index + ($index + $samePlace - 1)) / 2;
            foreach ($item as $key) {
                $result[$key]['place'] = $place;
                $index++;
            }
        }
    }

    public function getFullLeadResult(int $categoryId)  :array {
        $kval = $this->getLeadResult($this->getResult(self::LEAD_KVA, $categoryId));
        $final = $this->getLeadResult($this->getResult(self::LEAD_FI, $categoryId));

        foreach ($kval as $key => $item) {
            $kval[$key]['resultKeyFinal'] = 9999;
        }

        foreach ($final as $key => $item) {
            $kval[$key]['final'] = $item['route-1'];
            $kval[$key]['resultKeyFinal'] = $item['resultKey'];
        }

        $result = $kval;
        uasort($result, $this->cmpFunctionLead());

        $this->addPlaceFullLead($result, $this->groupByTwoKeys($result, 'resultKey', 'resultKeyFinal'));
        return $result;
    }

    private function cmpOrderBoulderCompResult(): \Closure {
        $key = 'resultColumn';
        return function ($a, $b) use ($key) {
            if ($a[$key]['T'] == $b[$key]['T']) {
                if ($a[$key]['Z'] == $b[$key]['Z']) {
                    if ($a[$key]['PT'] == $b[$key]['PT']) {
                        if ($a[$key]['PZ'] == $b[$key]['PZ']) {
                            return 0;
                        }
                        return $a[$key]['PZ'] < $b[$key]['PZ'] ? 1 : -1;
                    }
                    return $a[$key]['PT'] > $b[$key]['PT'] ? 1 : -1;
                }
                return $a[$key]['Z'] < $b[$key]['Z'] ? 1 : -1;
            }
            return $a[$key]['T'] < $b[$key]['T'] ? 1 : -1;
        };
    }
}





