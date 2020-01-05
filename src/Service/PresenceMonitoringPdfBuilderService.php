<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class PresenceMonitoring Pdf Builder Service.
 *
 * @category Service
 */
class PresenceMonitoringPdfBuilderService
{
    /**
     * @var \TCPDF $tcpdf
     */
    private $tcpdf;

    /**
     * @var Translator
     */
    protected $ts;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var SmartAssetsHelperService
     */
    protected $sahs;

    /**
     * Methods.
     */

    /**
     * PresenceMonitoringPdfBuilderService constructor.
     *
     * @param Translator $ts
     * @param SmartAssetsHelperService $sahs
     */
    public function __construct(Translator $ts, SmartAssetsHelperService $sahs)
    {
        $this->tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->ts = $ts;
        $this->sahs = $sahs;
    }

    /**
     * @param User $user
     *
     * @return \TCPDF
     */
    public function build(User $user) {
        $this->ts->setLocale('es');
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->AddPage('P', 'A4', true, true);
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetFilePath('/build/fibervent_logo_white_landscape.jpg'), 15, 15, 60, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(179, 204, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetFont('', 'B', 7);

        $this->tcpdf->SetAbsXY(15,45);

        return $this->tcpdf;
    }
}
