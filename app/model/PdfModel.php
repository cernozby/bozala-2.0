<?php

namespace App\model;

use App\components\MyHelpers;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\Database\Explorer;
use Nette\DI\Container;

/**
 *
 */
class PdfModel extends BaseModel
{

    /**
     *
     */
    const fileName = array(
        'starters' => "Startovní listina",
        'boulder-amateur' => "Výsledková listina boulder",
        'boulder-amateur-final' => "Výsledková listina boulder",
        'lead' => "Výsledková listina obtížnost",
        'boulder-comp' => "Výsledková listina boulder",
        'boulder-comp-final' => "Výsledková listina boulder",
    );

    /**
     * Konstanta pro stránku na šířku
     */
    const PAGE_WIDTH = 'A4-L';

    /**
     *  Konstanta pro stránku na víšku
     */
    const PAGE_HEIGHT = '';



    /**
     * @var LatteFactory
     */
    private LatteFactory $lf;

    /**
     * @param Explorer $database
     * @param Container $container
     * @param LinkGenerator $linkGenerator
     * @param LatteFactory $lf
     */
    public function __construct(Explorer $database, Container $container, LinkGenerator $linkGenerator, LatteFactory $lf) {
        parent::__construct($database, $container, $linkGenerator);
        $this->lf = $lf;

    }


    /**
     * Vygeneruje pdf stránku
     *
     * @param array $params Seznam proměnné do šablony
     * @param string $latteFilename název šablony
     * @param string $format formát stránky (šířka / výška)
     * @throws MpdfException
     */
    public function generatePdfView(array $params, string $latteFilename, string $format = self::PAGE_HEIGHT): void {
        $mpdf = $this->preparePdf($params, $latteFilename, $format);
        $mpdf->output(self::fileName[$latteFilename] . ".pdf", "I");
    }

    /**
     * @param array $params
     * @param string $latteFilename
     * @param $format
     * @return Mpdf
     * @throws MpdfException
     */
    public function preparePdf(array $params, string $latteFilename, $format): Mpdf {
        $mpdf = new \Mpdf\Mpdf(['format' => $format]);
        $latte = $this->lf->create();
        $latte->addFilter("decimalnumber", MyHelpers::class . "::" .'decimalnumber');
        $latte->addFilter("fillEmptyStr", function ($variable) {return MyHelpers::isEmpty($variable) ? 'X' : $variable;});

        $style = file_get_contents(__DIR__ . '/../SysModule/templates/Homepage/pdf/styl.css');
        $html = $latte->renderToString(__DIR__ . '/../SysModule/templates/Homepage/pdf/' . $latteFilename . '.latte', $params);
        $mpdf->SetTitle(self::fileName[$latteFilename]);
        $mpdf->WriteHTML($style, 1);
        $mpdf->WriteHTML($html);
        return $mpdf;
    }
}
