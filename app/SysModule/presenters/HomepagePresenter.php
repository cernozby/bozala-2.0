<?php

namespace App\SysModule\presenters;

use App\model\PdfModel;
use App\model\ResultModel;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;

/**
 *
 */
class HomepagePresenter extends BasePresenter
{
    /**
     * @throws AbortException
     */
    public function startup(): void {
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('Nedostatečná oprávnění');
            $this->redirect(':Public:Homepage:default');
        }
        parent::startup();
    }

    /**
     * @param $data
     */
    public function handleSaveResult($data): void {
        $data = $this->request->getPost();
        $this->resultModel->saveResult($data);
    }

    /**
     * Change bool value in column
     *
     * @param int $compId id comp
     * @param string $column name of the column
     */
    public function handleChangeBoolColumn(int $compId, string $column) {
        $this->comp->initId($compId);
        $this->comp->changeBoolColumn($column);
        $this->redrawControl();

    }

    /**
     * Make a final list
     *
     * @param $idCategory
     * @param string $type
     */
    public function handleMakeFinalList($idCategory, string $type) {
        $this->category->initId($idCategory);

        switch ($type) {
            case ResultModel::LEAD_KVA:
                $this->category->makeFinalListLead();
                break;
            case ResultModel::BOULDER_KVA:
                $this->category->makeFinalListBoulder();
                break;
        }
    }

    /**
     * @throws \Mpdf\MpdfException
     */
    public function handleGeneratePdfCategory($idCategory, string $latteFilename) {
        if (!$idCategory) {
            return;
        }
        try {
            switch ($latteFilename) {
                case 'starters':
                    $this->pdfModel->generatePdfView($this->getStartersParam($idCategory), $latteFilename);
                    break;
                case 'boulder-amateur':
                case 'boulder-comp':
                    $this->pdfModel->generatePdfView($this->getBoulderKvaParams($idCategory), $latteFilename, PdfModel::PAGE_WIDTH);
                    break;
                case 'boulder-amateur-final':
                case 'boulder-comp-final':
                    $this->pdfModel->generatePdfView($this->getBoulderFinalParams($idCategory), $latteFilename, PdfModel::PAGE_WIDTH);
                    break;
                case 'lead':
                    $this->pdfModel->generatePdfView($this->getLeadParams($idCategory), $latteFilename);

            }

        } catch (\Exception $e) {
            $this->flashMessage("Generace pdf se nezdařila!" . $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }

    /**
     * @param $idCategory
     * @return array
     */
    private function getStartersParam($idCategory): array {
        $this->category->initId($idCategory);
        return ['comp' => $this->category->getComp(), 'category' => $this->category];
    }

    /**
     * @param $idCategory
     * @return array
     */
    private function getBoulderKvaParams($idCategory): array {
        $this->category->initId($idCategory);
        return [
            'comp' => $this->category->getComp(),
            'category' => $this->category,
            'results' => $this->category->getBoulderFullResult(ResultModel::BOULDER_KVA),
            'pointForBoulder' => $this->category->getPointsForBoulder(ResultModel::BOULDER_KVA),
            'boulderCount' => $this->category->getComp()->get('boulder')];
    }

    /**
     * @param $idCategory
     * @return array
     */
    private function getBoulderFinalParams($idCategory): array {
        $this->category->initId($idCategory);
        return [
            'comp' => $this->category->getComp(),
            'category' => $this->category,
            'results' => $this->category->getBoulderFullResult(ResultModel::BOULDER_FI),
            'pointForBoulder' => $this->category->getPointsForBoulder(ResultModel::BOULDER_FI),
            'boulderCount' => $this->category->getComp()->get('boulder_final_boulders')];
    }

    /**
     * @param $idCategory
     * @return array
     */
    private function getLeadParams($idCategory) {
        $this->category->initId($idCategory);
        return [
            'comp' => $this->category->getComp(),
            'category' => $this->category,
            'results' => $this->category->getLeadResult(),
            'routeCountKva' => $this->category->getComp()->get('lead'),
            'routeCountFi' => $this->category->getComp()->get('lead_final')];
    }


    /**
     * @param int $categoryId
     * @param string $column
     */
    public function handleChangeBoolCategory(int $categoryId, string $column) {
        $this->category->initId($categoryId);
        $this->category->changeBoolColumn($column);
        $this->redrawControl();
    }


    /**
     * @param int $categoryId
     * @param int $competitorId
     */
    public function handlePrereg(int $categoryId, int $competitorId) {
        $this->competitor->initId($competitorId);
        $this->competitor->changePrereg($categoryId);
        $this->flashMessage('Registrace byla upravena.');
        $this->redrawControl();
    }

    /**
     *
     */
    public function renderDefault() {
        $this->template->comp = $this->compModel;
        $this->template->competitors = $this->competitorModel->getByUser($this->userClass->getId());

    }

    /**
     * @param $compId
     */
    public function renderListOfPrereg($compId): void {
        $this->template->compId = $compId;
        if (!$compId) {
            $this->template->comps = $this->compModel->getVisiblePreregComp();
        } else {
            $this->comp->initId($compId);
            $this->template->categories = $this->comp->getCategory();
        }

    }

    /**
     *
     */
    public function renderListOfComps(): void {
        $this->template->comps = $this->compModel->getAllAsObj();
    }

    /**
     * @param $competitorId
     */
    public function renderPrereg($competitorId): void {
        $this->competitor->initId($competitorId);
        $this->template->categories = $this->competitor->getCategoryToPrereg();
        $this->template->competitor = $this->competitor;
    }

    /**
     *
     */
    public function renderListOfCompetitors(): void {
        $this->template->competitors = $this->competitorModel->getByUser($this->userClass->getId());
    }

    /**
     * @param $compId
     * @param $categoryId
     */
    public function renderAddResult($compId, $categoryId): void {
        $this->template->compId = $compId;
        $this->template->categoryId = $categoryId;
        $this->template->resultModel = $this->resultModel;

        if ($categoryId && $compId) {
            $this->category->initId($categoryId);
            $this->template->category = $this->category;
            $this->template->competitors = $this->userClass->isAdmin() ?
                $this->category->getPreregCompetitors() :
                $this->category->getPreregCompetitorsByUser($this->userClass->getId());
            $this->template->finalLeadCompetitors = $this->resultModel->getCompetitors($categoryId, ResultModel::LEAD_FI);
            $this->template->finalBoulderCompetitors = $this->resultModel->getCompetitors($categoryId, ResultModel::BOULDER_FI);

        } elseif ($compId) {
            $this->comp->initId($compId);
            if ($this->userClass->isAdmin()) {
                $this->template->categories = $this->comp->getCategory();
            } else {
                $this->template->categories = $this->comp->getCategoryForUser($this->userClass->getId());
            }
        } else {
            if ($this->userClass->isAdmin()) {
                $this->template->comps = $this->compModel->getEditableResultComps();
            } else {
                $this->template->comps = $this->compModel->getEditableResultForUser($this->userClass->getId());
            }
        }
    }

    /**
     * @param $compId
     * @param $categoryId
     */
    public function renderResult($compId, $categoryId): void {
        $this->template->compId = $compId;
        $this->template->categoryId = $categoryId;
        $this->template->resultModel = $this->resultModel;


        if ($categoryId && $compId) {
            $this->category->initId($categoryId);
            $this->template->category = $this->category;
            $this->template->comp = $this->category->getComp();
        } elseif ($compId) {
            $this->comp->initId($compId);
            $this->template->categories = $this->comp->getCategory();
        } else {
            $this->template->comps = $this->compModel->getEditableResultComps();
        }
    }

    /**
     * @throws AbortException
     */
    public function handleLogout(): void {
        $this->user->logout();
        $this->flashMessage('Byl jste úspešně odhlášen');
        $this->redirect(':Public:Homepage:default');
    }

    /**
     *
     */
    public function handleChangePasswd() {
        $this->template->changePasswd = true;

    }

    /**
     *
     */
    public function handleResetPasswd(): void {
        $this->template->forgetPasswdForm = true;
    }

}
