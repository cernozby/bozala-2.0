<?php

namespace App\PublicModule\presenters;

use App\model\Constants;
use App\Presenters\BasePresenter;
use Nette;
use Nette\Application\UI\Form;

Class HomepagePresenter extends BasePresenter {
  
  

  /* ================ handles =======================*/
  /**
   * @return Form
   */
  public function handleLogin(): void {
    $this->template->loginForm = true;
  }
  
  public function handleRegistration() : void {
    $this->template->registrationForm = true;
  }


  
  
  /*=========================== forms ==================*/

  
  


}
