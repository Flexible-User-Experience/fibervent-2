<?php

namespace App\Service;

use App\Enum\MinutesEnum;
use App\Entity\User;
use App\Entity\PresenceMonitoring;
use App\Enum\AuditLanguageEnum;
use App\Enum\MonthsEnum;
use App\Enum\PresenceMonitoringCategoryEnum;
use DateTimeImmutable;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use TCPDF;

/**
 * Class PresenceMonitoring Pdf Builder Service.
 *
 * @category Service
 */
class PresenceMonitoringPdfBuilderService
{
    const PDF_MARGIN_LEFT = 15;
    const PDF_MARGIN_RIGHT = 15;
    const PDF_MARGIN_TOP = 4;
    const PDF_MARGIN_BOTTOM = 4;

    /**
     * @var TCPDF $tcpdf
     */
    private TCPDF $tcpdf;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $ts;

    /**
     * @var string
     */
    protected string $locale;

    /**
     * @var SmartAssetsHelperService
     */
    protected SmartAssetsHelperService $sahs;

    /**
     * Methods.
     */

    /**
     * PresenceMonitoringPdfBuilderService constructor.
     *
     * @param TranslatorInterface      $ts
     * @param SmartAssetsHelperService $sahs
     */
    public function __construct(TranslatorInterface $ts, SmartAssetsHelperService $sahs)
    {
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->ts = $ts;
        $this->sahs = $sahs;
    }

    /**
     * @param User $operator
     * @param PresenceMonitoring[]|array $items
     *
     * @return TCPDF
     * @throws Exception
     */
    public function build(User $operator, $items) {
        $this->ts->setLocale(AuditLanguageEnum::DEFAULT_LANGUAGE_STRING);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetMargins(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, self::PDF_MARGIN_RIGHT, true);
        $this->tcpdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM);
        $this->tcpdf->AddPage('P', 'A4', true, true);
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetFilePath('/build/fibervent_logo_white_landscape.jpg'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, 35, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetFont('', 'B', 10);

        // customer and worker table info
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 12);
        $this->tcpdf->Cell(180, 9, $this->ts->trans('admin.presencemonitoring.head_line_1'), 1, 1, 'C', true);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(90, 6, strtoupper($this->ts->trans('admin.presencemonitoring.brand')), 1, 0, 'C', true);
        $this->tcpdf->Cell(90, 6, strtoupper($this->ts->trans('admin.presencemonitoring.operator')), 1, 1, 'C', true);
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->Cell(90, 6, $this->ts->trans('admin.presencemonitoring.brand_title').': '.$this->ts->trans('fibervent.name'), 1, 0, 'L', true);
        $this->tcpdf->Cell(90, 6, $this->ts->trans('admin.presencemonitoring.operator_name').': '.$operator->getFullname(), 1, 1, 'L', true);
        $this->tcpdf->Cell(90, 6, $this->ts->trans('admin.presencemonitoring.brand_cif').': '.$this->ts->trans('fibervent.cif'), 1, 0, 'L', true);
        $this->tcpdf->Cell(90, 6, $this->ts->trans('admin.presencemonitoring.operator_nif').': '.$operator->getNif(), 1, 1, 'L', true);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $today = new DateTimeImmutable();
        $periodString = '';
        $itemsCount = count($items);
        if ($itemsCount > 0) {
            /** @var PresenceMonitoring $lastItemDate */
            $lastItemDate = $items[$itemsCount - 1];
            $lastItemDate = $lastItemDate->getDate();
            $periodString = $this->ts->trans(MonthsEnum::getOldMonthEnumArray()[intval($lastItemDate->format('n'))]).' '.$lastItemDate->format('Y');
        }
        $this->tcpdf->Cell(90, 6, $this->ts->trans('admin.presencemonitoring.head_line_2').': '.$periodString, 1, 0, 'L', true);
        $this->tcpdf->Cell(90, 6, $this->ts->trans('admin.presencemonitoring.head_line_3').': '.$today->format('d/m/Y'), 1, 1, 'L', true);
        $this->tcpdf->SetFillColor(108, 197, 205);

        // main table head line
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.presencemonitoring.day'), 1, 0, 'C', true);
        $this->tcpdf->Cell(30, 6, $this->ts->trans('admin.presencemonitoring.morning'), 1, 0, 'C', true);
        $this->tcpdf->Cell(30, 6, $this->ts->trans('admin.presencemonitoring.afternoon'), 1, 0, 'C', true);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.presencemonitoring.total_hours_short'), 1, 0, 'C', true);
        $this->tcpdf->Cell(25, 12, $this->ts->trans('admin.presencemonitoring.normal_hours_short'), 1, 0, 'C', true);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.presencemonitoring.extra_hours_short'), 1, 0, 'C', true);
        $this->tcpdf->Cell(35, 12, $this->ts->trans('admin.presencemonitoring.sign'), 1, 0, 'C', true);

        // main table head line 2
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT + 20, self::PDF_MARGIN_TOP + 51);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.begin'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.end'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.begin'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, $this->ts->trans('admin.presencemonitoring.end'), 1, 1, 'C', true);
        $this->tcpdf->SetFont('', '', 7);

        // main table values
        $numItems = count($items);
        $i = 0;
        /** @var PresenceMonitoring $pm */
        foreach ($items as $pm) {
            $cellBackgroundFill = false;
            if (++$i === $numItems) {
                $this->tcpdf->SetFont('', 'B', 7);
                $cellBackgroundFill = true;
                $this->tcpdf->SetFillColor(183, 223, 234);
                $this->tcpdf->Cell(80, 6, $this->ts->trans('admin.presencemonitoring.total'), 1, 0, 'R', $cellBackgroundFill);
                $this->tcpdf->SetFillColor(108, 197, 205);
                $this->drawTotalHourCells($pm, $cellBackgroundFill);
            } else {
                $this->tcpdf->Cell(20, 6, $pm->getDateString(), 1, 0, 'C', 0);
                if ($pm->getCategory() == PresenceMonitoringCategoryEnum::WORKDAY) {
                    $this->tcpdf->Cell(15, 6, $pm->getMorningHourBeginString(), 1, 0, 'C', 0);
                    $this->tcpdf->Cell(15, 6, $pm->getMorningHourEndString(), 1, 0, 'C', 0);
                    $this->tcpdf->Cell(15, 6, $pm->getAfternoonHourBeginString(), 1, 0, 'C', 0);
                    $this->tcpdf->Cell(15, 6, $pm->getAfternoonHourEndString(), 1, 0, 'C', 0);
                    $this->drawTotalHourCells($pm, $cellBackgroundFill);
                } else {
                    $this->tcpdf->Cell(125, 6, $this->ts->trans($pm->getCategoryString()), 1, 0, 'C', 0);
                }
            }
            $this->tcpdf->Cell(35, 6, '', 1, 1, 'C', 0);
            $this->tcpdf->SetFont('', '', 7);
        }

        // legal text
        $this->tcpdf->Ln(AbstractPdfBuilderService::SECTION_SPACER_V);
        $this->tcpdf->SetFont('', 'B', 8);
        $this->tcpdf->MultiCell(180, 12, $this->ts->trans('admin.presencemonitoring.legal'), 0, 'L', false, 1, $this->tcpdf->GetX(), '', true);

        // final sign boxes
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->Ln(AbstractPdfBuilderService::SECTION_SPACER_V);
        $this->tcpdf->Cell(60, 6, $this->ts->trans('admin.presencemonitoring.sign').' '.$this->ts->trans('admin.presencemonitoring.brand'), 1, 0, 'C', true);
        $this->tcpdf->Cell(15, 6, '', 0, 0, 'C', false);
        $this->tcpdf->Cell(60, 6, $this->ts->trans('admin.presencemonitoring.sign').' '.$this->ts->trans('admin.presencemonitoring.operator'), 1, 1, 'C', true);
        $this->tcpdf->Cell(60, 16, '', 1, 0, 'C', false);
        $this->tcpdf->Cell(15, 16, '', 0, 0, 'C', false);
        $this->tcpdf->Cell(60, 16, '', 1, 1, 'C', false);

        return $this->tcpdf;
    }

    /**
     * @param PresenceMonitoring $pm
     * @param bool               $cellBackgroundFill
     */
    private function drawTotalHourCells(PresenceMonitoring $pm, bool $cellBackgroundFill)
    {
        $this->tcpdf->Cell(20, 6, $pm->getTotalHours() ? MinutesEnum::transformToHoursAmountString($pm->getTotalHours()) : '0h', 1, 0, 'R', $cellBackgroundFill);
        $this->tcpdf->Cell(25, 6, $pm->getNormalHours() ? MinutesEnum::transformToHoursAmountString($pm->getNormalHours()) : '0h', 1, 0, 'R', $cellBackgroundFill);
        $this->tcpdf->Cell(20, 6, $pm->getExtraHours() ? MinutesEnum::transformToHoursAmountString($pm->getExtraHours()) : '0h', 1, 0, 'R', $cellBackgroundFill);
    }
}
