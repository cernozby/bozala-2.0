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

    /**
     *
     *
     * @param $variable
     * @return string
     */
  public static function gender($variable): string {
    return CategoryModel::$genders[$variable];
  }

    /**
     * Nahradi "" nebo 0 Xkem
     *
     * @param $variable
     * @return string
     */
  public static function fillEmptyStr($variable): string {
      if (self::isEmpty($variable)) {
          return "&#10060;";
      }
      return $variable;
  }


    /**
     * Nahradi "" nebo 0 Xkem, jinak Y
     *
     * @param $variable
     * @return string
     */
    public static function boulderAmateur($variable): string {
        if (self::isEmpty($variable)) {
            return "&#10060;";
        }
        return "&#10004;";
    }

    /**
     * Formát čísla float v aplikace
     *
     *
     * @param $number
     * @return string
     */
    public static function decimalNumber($number) : string {
        return number_format($number, 2, '.', '');
    }


    private static function isEmpty($variable): bool {
        return $variable === " " or $variable === "" or $variable == 0;
    }
}
?>