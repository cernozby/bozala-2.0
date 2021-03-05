<?php

namespace App\components\formLogin;

use App\components\BaseComponent;
use App\model\Constants;
use Nette\Application\AbortException;
use \Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


class formLogin extends BaseComponent {
  
  public function render() : void {
    $this->template->setFile(__DIR__ . '/formLogin.latte');
    $this->template->render();
  }
  
  /**
   * @return Form
   */
  public function createComponentLoginForm() : Form {
    $form = new Form();
    $form->addEmail('email', 'Email:')
      ->setRequired('email: '. Constants::FORM_MSG_REQUIRED);
    $form->addPassword('passwd', 'Heslo:')
      ->setRequired('heslo: ' . Constants::FORM_MSG_REQUIRED);
    $form->addSubmit('submit', 'Přihlásít');
    $form->onSuccess[] = [$this, 'loginFormSucceeded'];
    return $form;
  }
  
  /**
   * @param Form $form
   * @param ArrayHash $values
   * @throws AbortException
   */
  public function loginFormSucceeded(Form $form, ArrayHash $values) : void {
    try {
      $this->user->login($values->email, $values->passwd);
      $this->presenter->flashMessage('Byl jste úspěšně přihlášen.');
    } catch (\Exception $e) {
      $this->presenter->flashMessage($e->getMessage());
      $this->redirect('this', ['do' => 'Login']);
    }
    $this->presenter->redirect(':Sys:Homepage:default');
    
  }
}
