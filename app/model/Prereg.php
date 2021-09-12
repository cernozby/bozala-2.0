<?php

namespace App\model;

use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;
use Nette\Security\User;

/**
 * Class Prereg
 **/
class Prereg extends BaseFactory
{

    /**
     * Compcat constructor.
     * @param Explorer $database
     * @param Container $container
     * @param LinkGenerator $linkGenerator
     */

    public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
        $this->table = 'prereg';
        parent::__construct($database, $container, $linkGenerator);
    }

    public function initByCategoryIdAndCompetitorId(int $categoryId, int $competitorId) :? object {
        $result = $this->getTable()
            ->select('id_prereg')
            ->where('category_id = ?', $categoryId)
            ->where('competitor_id = ?', $competitorId)
            ->fetch();

        if (!$result) {
            return null;
        }
        return $this->initId($result->id_prereg);
    }
}