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
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetPathContextIndependentWithVersionStrategy('build/fibervent_logo_white_landscape.jpg'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 10, 45, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // colors, line width and bold font
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetFont('', 'B', 10);

        $today = new DateTimeImmutable();
        $periodString = '';
        $itemsCount = count($items);
        if ($itemsCount > 0) {
            /** @var WorkerTimesheet $firstItemDate */
            $firstItemDate = $items[0];
            $firstItemDate = $firstItemDate->getDeliveryNote()->getDate();
            $periodString = $this->ts->trans(MonthsEnum::getOldMonthEnumArray()[intval($firstItemDate->format('n'))]).' '.$firstItemDate->format('Y');
        }

        // customer and worker table info
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT + 50, self::PDF_MARGIN_TOP + 10);
        $this->tcpdf->Cell(217, 9, $this->ts->trans('admin.workertimesheet.head_line_1'), 1, 1, 'C', true);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + 50);
        $this->tcpdf->Cell(30, 6, strtoupper($this->ts->trans('admin.workertimesheet.head_line_2')), 1, 0, 'L', true);
        $this->tcpdf->SetFillColor(255, 255, 255);
        $this->tcpdf->Cell(137, 6, $operator->getFullname(), 1, 0, 'L', true);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->Cell(20, 6, strtoupper($this->ts->trans('admin.workertimesheet.head_line_3')), 1, 0, 'C', true);
        $this->tcpdf->SetFillColor(255, 255, 255);
        $this->tcpdf->Cell(30, 6, $periodString, 1, 1, 'C', true);

        // main table head line
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->Cell(145, 6, $this->ts->trans('admin.workertimesheet.head_line_4'), 1, 0, 'C', true);
        $this->tcpdf->Cell(80, 6, $this->ts->trans('admin.workertimesheet.head_line_5'), 1, 0, 'C', true);
        $this->tcpdf->Cell(42, 6, $this->ts->trans('admin.workertimesheet.head_line_6'), 1, 1, 'C', true);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.workertimesheet.head_line_7'), 1, 0, 'C', true);
        $this->tcpdf->Cell(20, 12, $this->ts->trans('admin.workertimesheet.head_line_8'), 1, 0, 'C', true);
        $this->tcpdf->Cell(105, 12, $this->ts->trans('admin.workertimesheet.head_line_9'), 1, 0, 'C', true);
        $this->tcpdf->Cell(16, 12, $this->ts->trans('admin.workertimesheet.head_line_10'), 1, 0, 'C', true);
        $this->tcpdf->Cell(16, 12, $this->ts->trans('admin.workertimesheet.head_line_11'), 1, 0, 'C', true);
        $this->tcpdf->Cell(16, 12, $this->ts->trans('admin.workertimesheet.head_line_12'), 1, 0, 'C', true);
        $this->tcpdf->Cell(16, 12, $this->ts->trans('admin.workertimesheet.head_line_13'), 1, 0, 'C', true);
        $this->tcpdf->Cell(16, 12, $this->ts->trans('admin.workertimesheet.head_line_14'), 1, 0, 'C', true);
        $this->tcpdf->Cell(21, 12, $this->ts->trans('admin.workertimesheet.head_line_15'), 1, 0, 'C', true);
        $this->tcpdf->Cell(21, 12, $this->ts->trans('admin.workertimesheet.head_line_16'), 1, 1, 'C', true);

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
