<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Competitor
 **/
class Competitor extends BaseFactory
{

    /**
     * Competitor constructor.
     * @param Explorer $database
     * @param Container $container
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
        $this->table = 'competitor';
        parent::__construct($database, $container, $linkGenerator);
    }

    /**
     * @return string
     */
    public function getFullName(): string {
        return $this->get('first_name') . ' ' . $this->get('last_name');
    }

    /**
     * @param int $categoryId
     */
    public function changePrereg(int $categoryId): void {
        /** @var Prereg $prereg */
        $prereg = $this->container->createService('Prereg');
        /** @var PreregModel $preregModel */
        $preregModel = $this->container->createService('PreregModel');


        if ($prereg->initByCategoryIdAndCompetitorId($categoryId, $this->getId())) {
            $prereg->delete();
        } else {
            $preregModel->newPrereg($categoryId, $this->getId());
        }
    }

    public function isPrereg(int $categoryId): bool {

        /** @var Prereg $prereg */
        $prereg = $this->container->createService('prereg');

        return $prereg->initByCategoryIdAndCompetitorId($categoryId, $this->getId()) !== null;


    }

    public function getCategoryToPrereg(): array {
        $result = [];
        /** @var CompModel $compModel */
        $compModel = $this->container->createService('compModel');
        $openComps = $compModel->getOpenComps();

        foreach ($openComps as $comp) {
            foreach ($comp->getCategory() as $category) {
                if (
                    $category->get('year_young') >= $this->get('year') &&
                    $category->get('year_old') <= $this->get('year') &&
                    $category->get('gender') == $this->get('gender')
                )
                    $result[] = $category;
            }
        }
        return $result;
    }
}