<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\PresenceMonitoring;
use App\Enum\AuditLanguageEnum;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class PresenceMonitoring Pdf Builder Service.
 *
 * @category Service
 */
class PresenceMonitoringPdfBuilderService
{
    const PDF_MARGIN_LEFT = 15;
    const PDF_MARGIN_RIGHT = 15;
    const PDF_MARGIN_TOP = 10;
    const PDF_MARGIN_BOTTOM = 10;

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
     * @param User                       $user
     * @param PresenceMonitoring[]|array $items
     *
     * @return \TCPDF
     */
    public function build(User $user, $items) {
        $this->ts->setLocale(AuditLanguageEnum::DEFAULT_LANGUAGE_STRING);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->AddPage('P', 'A4', true, true);
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetFilePath('/build/fibervent_logo_white_landscape.jpg'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, 45, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(179, 204, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetFont('', 'B', 7);
        // head line
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 20);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.presencemonitoring.day'), 1, 0, 'C', true);
        $this->tcpdf->Cell(30, 6, $this->ts->trans('admin.presencemonitoring.morning'), 1, 0, 'C', true);
        $this->tcpdf->Cell(30, 6, $this->ts->trans('admin.presencemonitoring.afternoon'), 1, 0, 'C', true);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.presencemonitoring.total_hours_short'), 1, 0, 'C', true);
        $this->tcpdf->Cell(25, 12, $this->ts->trans('admin.presencemonitoring.normal_hours_short'), 1, 0, 'C', true);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.presencemonitoring.extra_hours_short'), 1, 0, 'C', true);
        $this->tcpdf->Cell(35, 12, $this->ts->trans('admin.presencemonitoring.sign'), 1, 0, 'C', true);
        // head line 2
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT + 20, self::PDF_MARGIN_TOP + 26);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.begin'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.end'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.begin'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.end'), 1, 1, 'C', true);

        $this->tcpdf->SetFont('', '', 7);

        $numItems = count($items);
        $i = 0;
        /** @var PresenceMonitoring $pm */
        foreach ($items as $pm) {
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT);
            $cellBackgroundFill = false;
            if (++$i === $numItems) {
                $this->tcpdf->SetFont('', 'B', 7);
                $cellBackgroundFill = true;
                $this->tcpdf->Cell(80, 6, $this->ts->trans('admin.presencemonitoring.total'), 1, 0, 'R', $cellBackgroundFill);
            } else {
                $this->tcpdf->Cell(20, 6, $pm->getDateString(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $pm->getMorningHourBeginString(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $pm->getMorningHourEndString(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $pm->getAfternoonHourBeginString(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $pm->getAfternoonHourEndString(), 1, 0, 'C', 0);
            }
            $this->tcpdf->Cell(20, 6, $pm->getTotalHours() ? $pm->getTotalHours() : 0, 1, 0, 'R', $cellBackgroundFill);
            $this->tcpdf->Cell(25, 6, $pm->getNormalHours() ? $pm->getNormalHours() : 0, 1, 0, 'R', $cellBackgroundFill);
            $this->tcpdf->Cell(20, 6, $pm->getExtraHours() ? $pm->getExtraHours() : 0, 1, 0, 'R', $cellBackgroundFill);
            $this->tcpdf->Cell(35, 6, '', 1, 1, 'C', 0);
            $this->tcpdf->SetFont('', '', 7);
        }

        // legal text
        $this->tcpdf->Ln(AbstractPdfBuilderService::SECTION_SPACER_V);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT);
        $this->tcpdf->SetFont('', 'B', 8);
        $this->tcpdf->MultiCell(180, 12, $this->ts->trans('admin.presencemonitoring.legal'), 0, 'L', false, 1, $this->tcpdf->GetX(), '', true);

        // final sign boxes
        $this->tcpdf->Ln(AbstractPdfBuilderService::SECTION_SPACER_V);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT);
        $this->tcpdf->Cell(60, 6, $this->ts->trans('admin.presencemonitoring.sign').' '.$this->ts->trans('admin.presencemonitoring.brand'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, '', 0, 0, 'C', false);
        $this->tcpdf->Cell(60, 6, $this->ts->trans('admin.presencemonitoring.sign').' '.$this->ts->trans('admin.presencemonitoring.operator'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT);
        $this->tcpdf->Cell(60, 18, '', 1, 0, 'C', false);
        $this->tcpdf->Cell(15, 18, '', 0, 0, 'C', false);
        $this->tcpdf->Cell(60, 18, '', 1, 1, 'C', false);

        return $this->tcpdf;
    }
}
