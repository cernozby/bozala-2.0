<?php

namespace App\components\formRegistration;

use App\components\BaseComponent;
use App\model\Constants;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


class formRegistration extends BaseComponent {
  
  public function render() : void {
    $this->template->setFile(__DIR__ . '/formRegistration.latte');
    $this->template->render();
  }
  
  public function createComponentRegistrationForm() : Form {
    $form = new Form();
    $form->addText('first_name', 'Jméno:')
      ->setRequired('jméno: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH,Constants::FORM_LONG, 30);
    $form->addText('last_name','Přijmení:')
      ->setRequired('přijmení: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::MAX_LENGTH, Constants::FORM_LONG, 30);
    $form->addEmail('email', 'Email:')
      ->setRequired('email: '. Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::EMAIL, Constants::FORM_VALID_EMAIL)
      ->addRule(FORM::IS_NOT_IN,Constants::FORM_EMAIL_UNIQ, $this->userModel->getAllFromOneColumn('email'));
    $form->addPassword('passwd', 'Heslo:')
      ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::LENGTH, Constants::FORM_LENGHT_PASSWD, [5, 40]);
    $form->addPassword('passwd_verify', 'Heslo znova:')
      ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED)
      ->addRule(FORM::EQUAL, Constants::FORM_MATCH_PASSWD, $form['passwd'])
      ->setOmitted();
    $form->addCheckbox('agree_with_terms', 'Souhlasím s licenčníma podmínkama')
      ->setRequired('souhlas s licenčními podmínkami je povinný')
      ->setOmitted();
    $form->addSubmit('submit', 'registrovat');
    $form->onSuccess[] = [$this, 'registrationFormSucceed'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param ArrayHash $values
   */
  public function registrationFormSucceed(Form $form, ArrayHash $values): void {
    try {
      $this->userModel->newUser($values);
      $this->presenter->flashMessage('Registrace proběhla úspešně.');
    } catch (\Exception $e) {
      $this->presenter->flashMessage('Neúspešná registrace');
    }
  }
}
