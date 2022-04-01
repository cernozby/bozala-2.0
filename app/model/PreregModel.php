<?php

namespace App\model;
use Exception;
use Nette\Application\LinkGenerator;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 * Class PreregModel
 * @package App\model
 */
class PreregModel extends BaseModel
{
    /**
     * PreregModel constructor.
     * @param Explorer $database
     * @param Container $container
     * @param LinkGenerator $linkGenerator
     */
    public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator) {
        $this->table = 'prereg';
        parent::__construct($database, $container, $linkGenerator);
    }

    /**
     * @param int $categoryId
     * @param int $competitorId
     */
    public function newPrereg(int $categoryId, int $competitorId) {
        $this->getTable()->insert(['category_id' => $categoryId, 'competitor_id' => $competitorId]);
    }
}