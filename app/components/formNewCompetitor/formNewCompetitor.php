<?php

namespace App\components\formNewCompetitor;

use App\components\BaseComponent;
use App\model\Constants;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


class formNewCompetitor extends BaseComponent {
  
  private $competitorId;
  public function render($competitorId = null) : void {
    $this->template->competitorId = $competitorId;
    $this->competitorId = $competitorId;
    
    $this->template->setFile(__DIR__ . '/formNewCompetitor.latte');
    $this->template->render();
  }
  
  public function createComponentNewCompetitorForm() : Form {
    $form = new Form();
    $form->addHidden('id_competitor');
    $form->addSelect('gender', 'Pohlaví', ['male' => 'Muž', 'female' => 'Žena'])
         ->setPrompt('Vyberte')
         ->setRequired();
    $form->addText('first_name', 'Jméno:')
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH,Constants::FORM_LONG, 30);
    $form->addText('last_name','Přijmení:')
      ->setRequired('přijmení: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH, Constants::FORM_LONG, 30);
    $form->addText('club','Oddíl:')
      ->setRequired('Oddíl: ' . Constants::FORM_MSG_REQUIRED);
    $form->addText('year','Rok narození:')
      ->setRequired('Rok narození: ' . Constants::FORM_MSG_REQUIRED);
    
    
    if ($this->competitorId) {
      $form->setDefaults($this->container->createService('Competitor')->initId($this->competitorId)->getRowAsArray());
    }
    
    $form->addSubmit('submit', 'registrovat');
    $form->onSuccess[] = [$this, 'newCompetitorFormSucceed'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function newCompetitorFormSucceed(Form $form, ArrayHash $values): void {
    try {
      $competitorId = $values->offsetGet('id_competitor');
      $values->offsetUnset('id_competitor');

      if ($competitorId) {
        $this->container->createService('Competitor')->initId($competitorId)->update($values);
        $this->presenter->flashMessage('Editace proběhla úspešně.');
      } else {
        $values->offsetSet('user_id', $this->userClass->getId());
        $this->container->createService('CompetitorModel')->newCompetitor($values);
        $this->presenter->flashMessage('Registrace proběhla úspešně.');
      }
    } catch (\Exception $e) {
      $this->presenter->flashMessage('Neúspešná registrace zavodnika');
    }
    $this->presenter->redirect('this');
  }
}
