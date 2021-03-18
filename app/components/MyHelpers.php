<?php
namespace App\components;
use App\model\CategoryModel;
use App\model\CompModel;

class MyHelpers {
  
  public static function boolToWorld($variable): string {
    if ($variable === 1 || $variable === true) {
      return 'Ano';
    }
    return 'Ne';
  }
  
  public static function resultType($variable): string {
    return CompModel::$result_type[$variable];
  }
  
  public static function gender($variable): string {
    return CategoryModel::$genders[$variable];
  }
}
?>