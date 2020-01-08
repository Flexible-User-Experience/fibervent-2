<?php

namespace App\Service;

use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use App\Enum\AuditLanguageEnum;
use App\Enum\WindfarmLanguageEnum;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

/**
 * Class WorkOrder Pdf Builder Service.
 *
 * @category Service
 */
class WorkOrderPdfBuilderService
{
    /**
     * @var \TCPDF $tcpdf
     */
    private $tcpdf;

    /**
     * @var Translator
     */
    private $ts;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var SmartAssetsHelperService
     */
    private $sahs;

    /**
     * Methods.
     */

    /**
     * WorkOrderPdfBuilderService constructor.
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
     * @param WorkOrder $workOrder
     *
     * @return \TCPDF
     */
    public function build(WorkOrder $workOrder) {
        $this->locale = AuditLanguageEnum::DEFAULT_LANGUAGE_STRING;
        if ($workOrder->getWindfarm() && $workOrder->getWindfarm()->getLanguage() >= AuditLanguageEnum::SPANISH && $workOrder->getWindfarm()->getLanguage() <= AuditLanguageEnum::ITALIAN) {
            $this->locale = WindfarmLanguageEnum::getReversedEnumArray()[$workOrder->getWindfarm()->getLanguage()];
        }
        $this->ts->setLocale($this->locale);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->AddPage('L', 'A4', true, true);
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetFilePath('/build/fibervent_logo_white_landscape.jpg'), 10, 15, 60, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(179, 204, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.3);
        $this->tcpdf->SetFont('', 'B', 7);

        $this->tcpdf->SetAbsXY(10, 45);
        $this->tcpdf->MultiCell(30, 5, $this->ts->trans('admin.workorder.project_number'), 1, 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $this->tcpdf->SetFont('');
        $this->tcpdf->MultiCell(15, 5, $workOrder->getId(), 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $this->tcpdf->SetAbsXY(10,50);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->MultiCell(30, 5, $this->ts->trans('admin.workorder.date'), 1, 'L', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $this->tcpdf->SetFont('');
        $this->tcpdf->MultiCell(15, 5, $workOrder->getCreatedAt()->format('d/m/Y'), 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');

        $this->tcpdf->SetAbsXY(100, 50);
        $this->tcpdf->SetFont('', 'B', 10);
        $this->tcpdf->MultiCell(100, 5, $this->ts->trans('pdf_workorder.header.sumary_external_damages_to_repair').' '.strtoupper($workOrder->getWindfarm()->getName()), 0, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');

        $this->tcpdf->SetAbsXY(112, 10);

        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(55, 7,  $this->ts->trans('pdf_workorder.header.customer_data'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(55, 7, $this->ts->trans('pdf_workorder.header.windmill_data'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(65, 7, $this->ts->trans('pdf_workorder.header.certifying_company_data'), 1, 0, 'C', 1);
        $this->tcpdf->Ln();
        // Color and font restore
        $this->tcpdf->SetFillColor(224, 235, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetFont('');

        $this->tcpdf->SetAbsXY(112, 17);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5, $this->ts->trans('admin.customer.title'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCustomer()->getName(), 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5, 'Fabricante', 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(30, 5, $this->ts->trans('admin.workorder.certifying_company_name'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyName(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(112, 22);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5,  $this->ts->trans('admin.customer.contact'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCustomer()->getContacts()->first(), 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5, $this->ts->trans('admin.windmill.bladetype'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(30, 5, $this->ts->trans('admin.customer.contact'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyContactPerson(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(112, 27);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5,  $this->ts->trans('admin.customer.phone'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCustomer()->getPhone(), 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5, 'Material', 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(30, 5, $this->ts->trans('admin.customer.phone'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyPhone(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(112, 32);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5,  $this->ts->trans('admin.windfarm.title'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getWindfarm()->getName(), 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5, 'Altura buje', 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(30, 5, $this->ts->trans('admin.customer.email'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyEmail(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(112, 37);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5,  $this->ts->trans('admin.customer.city'), 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, $workOrder->getWindfarm()->getCity(), 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(20, 5, 'Anticaidas', 1, 0, 'L', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, '-:', 1, 0, 'C', 0);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(30, 5, '', 1, 0, 'C', 0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->Cell(35, 5, '', 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(10, 60);
        $this->tcpdf->SetFillColor(179, 204, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.3);
        $this->tcpdf->SetFont('', 'B', 7);
        // Header
        $this->tcpdf->Cell(30, 7, $this->ts->trans('admin.workordertask.wtg'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(10, 7, strtoupper($this->ts->trans('admin.windmill.bladetype')), 1, 0, 'C', 1);
        $this->tcpdf->Cell(25, 7, strtoupper($this->ts->trans('pdf_workorder.table_header.serial_number')), 1, 0, 'C', 1);
        $this->tcpdf->Cell(10, 7, $this->ts->trans('pdf_workorder.table_header.damage'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(15, 7, $this->ts->trans('pdf_workorder.table_header.bladedamage_position'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(13, 7, $this->ts->trans('pdf_workorder.table_header.bladedamage_radius'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(18, 7, $this->ts->trans('pdf_workorder.table_header.bladedamage_distance'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(18, 7, $this->ts->trans('pdf_workorder.table_header.bladedamage_size'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(83, 7, $this->ts->trans('pdf_workorder.table_header.workordertask_description'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(35, 7, $this->ts->trans('pdf_workorder.table_header.team'), 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 7, strtoupper($this->ts->trans('admin.workordertask.is_completed')), 1, 0, 'C', 1);
        $this->tcpdf->Ln();
        // Color and font restoration
        $this->tcpdf->SetFillColor(224, 235, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetFont('');
        $this->tcpdf->SetAbsXY(15, 67);
        // Data
        $windmill = 0;
        $windmillBlade = 0;
        /** @var WorkOrderTask $workOrderTask */
        foreach ($workOrder->getWorkOrderTasks() as $workOrderTask) {
            $this->tcpdf->SetAbsX(10);
            if (1 == $workOrderTask->getWindmillBlade()->getOrder()) {
                $fillBlade = 1;
                $this->tcpdf->SetFillColor(255, 242, 230);
            } elseif (2 == $workOrderTask->getWindmillBlade()->getOrder()) {
                $fillBlade = 1;
                $this->tcpdf->SetFillColor(230, 255, 230);
            } elseif (3 == $workOrderTask->getWindmillBlade()->getOrder()) {
                $fillBlade = 1;
                $this->tcpdf->SetFillColor(255, 255, 230);
            } else {
                $fillBlade = 0;
            }
            $windmill = $workOrderTask->getWindmill()->getId();
            $windmillBlade =$workOrderTask->getWindmillBlade()->getId();

            $this->tcpdf->Cell(30, 5, $workOrderTask->getWindmill()->getCode(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(10, 5, $workOrderTask->getWindmillBlade()->getOrder(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(25, 5,  $workOrderTask->getWindmill()->getBladeType()->getModel(), 1, 0, 'C', $fillBlade);
            if ($workOrderTask->getBladeDamage()) {
                $this->tcpdf->Cell(10, 5, $workOrderTask->getBladeDamage()->getCalculatedNumberByRadius(), 1, 0, 'C', $fillBlade);
            } else {
                $this->tcpdf->Cell(10, 5, '-', 1, 0, 'C', $fillBlade);
            }
            $this->tcpdf->Cell(15, 5, $workOrderTask->getPositionString(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(13, 5, $workOrderTask->getRadius().' m', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(18, 5, $workOrderTask->getDistance().' cm', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(18, 5, $workOrderTask->getSize().' cm', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(83, 5, $workOrderTask->getDescription(), 1, 0, 'L', $fillBlade);
            $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 5, $workOrderTask->isCompleted()?'SI':'NO', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Ln();
        }
        $this->tcpdf->Ln();
        if ($workOrder->getRepairAccessTypesString()) {
            $repairAccessTypeString = '';
            foreach ($workOrder->getRepairAccessTypesString() as $key => $repairAccessType) {
                if ($key == array_key_first($workOrder->getRepairAccessTypesString())) {
                    $repairAccessTypeString = $this->ts->trans($repairAccessType);
                } else {
                    $repairAccessTypeString = $this->ts->trans($repairAccessType) . ', ' . $repairAccessTypeString;
                }
            }
            $this->tcpdf->SetAbsX(10);
            $this->tcpdf->SetFillColor(230, 230, 0);
            $this->tcpdf->SetDrawColor(230, 230, 0);
            $this->tcpdf->SetTextColor(0);
            $this->tcpdf->SetLineWidth(0.3);
            $this->tcpdf->SetFont('', 'B', 7);
            $this->tcpdf->Cell(277, 5, $this->ts->trans('pdf_workorder.header.repair') . ' ' . strtoupper($repairAccessTypeString), 1, 0, 'C', 1);
        }

        return $this->tcpdf;
    }
}
