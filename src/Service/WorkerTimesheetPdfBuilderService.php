<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\WorkerTimesheet;
use App\Enum\MinutesEnum;
use App\Enum\AuditLanguageEnum;
use App\Enum\MonthsEnum;
use DateTimeImmutable;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use TCPDF;

/**
 * Class WorkerTimesheet Pdf Builder Service.
 *
 * @category Service
 */
class WorkerTimesheetPdfBuilderService
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
     * @param User                    $operator
     * @param WorkerTimesheet[]|array $items
     *
     * @return TCPDF
     * @throws Exception
     */
    public function build(User $operator, $items) {
        $this->ts->setLocale(AuditLanguageEnum::DEFAULT_LANGUAGE_STRING);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetMargins(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 10, self::PDF_MARGIN_RIGHT, true);
        $this->tcpdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM + 10);
        $this->tcpdf->AddPage('L', 'A4', true, true);
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetPathContextIndependentWithVersionStrategy('build/fibervent_logo_white_landscape.jpg'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 10, 35, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // colors, line width and bold font
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetFont('', 'B', 10);

        // customer and worker table info
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 22);
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
            /** @var WorkerTimesheet $firstItemDate */
            $firstItemDate = $items[0];
            $firstItemDate = $firstItemDate->getDeliveryNote()->getDate();
            $periodString = $this->ts->trans(MonthsEnum::getOldMonthEnumArray()[intval($firstItemDate->format('n'))]).' '.$firstItemDate->format('Y');
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
        $i = 0;
        /** @var WorkerTimesheet $wt */
        foreach ($items as $wt) {
            $cellBackgroundFill = false;
            if (++$i === $itemsCount) {
                $this->tcpdf->SetFont('', 'B', 7);
                $cellBackgroundFill = true;
                $this->tcpdf->SetFillColor(183, 223, 234);
                $this->tcpdf->Cell(80, 6, $this->ts->trans('admin.presencemonitoring.total'), 1, 0, 'R', $cellBackgroundFill);
                $this->tcpdf->SetFillColor(108, 197, 205);
                $this->drawTotalHourCells($wt, $cellBackgroundFill);
            } else {
                $this->tcpdf->Cell(20, 6, $wt->getDeliveryNote()->getDateString(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $wt->getTotalNormalHours(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $wt->getTotalVerticalHours(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $wt->getTotalInclementWeatherHours(), 1, 0, 'C', 0);
                $this->tcpdf->Cell(15, 6, $wt->getTotalTripHours(), 1, 0, 'C', 0);
            }
            $this->tcpdf->Cell(35, 6, '', 1, 1, 'C', 0);
            $this->tcpdf->SetFont('', '', 7);
        }

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
     * @param WorkerTimesheet $wt
     * @param bool            $cellBackgroundFill
     */
    private function drawTotalHourCells(WorkerTimesheet $wt, bool $cellBackgroundFill)
    {
        $this->tcpdf->Cell(20, 6, $wt->getTotalNormalHours() ? MinutesEnum::transformToHoursAmountString($wt->getTotalNormalHours()) : '0h', 1, 0, 'R', $cellBackgroundFill);
        $this->tcpdf->Cell(25, 6, $wt->getTotalVerticalHours() ? MinutesEnum::transformToHoursAmountString($wt->getTotalVerticalHours()) : '0h', 1, 0, 'R', $cellBackgroundFill);
        $this->tcpdf->Cell(20, 6, $wt->getTotalInclementWeatherHours() ? MinutesEnum::transformToHoursAmountString($wt->getTotalInclementWeatherHours()) : '0h', 1, 0, 'R', $cellBackgroundFill);
    }
}
