<?php
namespace App\model;
/**
 * Class Constants
 * @package App\model
 */
class Constants
{
  public static $user = array(
    self::USER_USER => 'user',
    self::USER_ADMIN => 'admin'
  );
  
  const USER_USER = 0;
  const USER_ADMIN = 1;
  
  const FORM_MSG_REQUIRED = "toto pole je povinné";
  const FORM_SHORT_PASSWD = "Vaše heslo musí mít alespon %d znaků!";
  const FORM_LONG_PASSWD = "Vaše heslo může mít maximálně %d znaků!";
  const FORM_LENGHT_PASSWD = "Vaše heslo musí mít mezi %d a %d znaky!";
  const FORM_VALID_EMAIL = 'Zadejte prosím platný email!';
  const FORM_SHORT = 'Toto pole nesmí mít méně než %d znaků!';
  const FORM_LONG = 'Toto pole nesmí mít více než %d znaků!';
  const FORM_EMAIL_UNIQ = 'Tento email již používá někdo jiný!.';
  const FORM_MATCH_PASSWD = 'Zadaná hesla se neschodují!';
  
}
?>