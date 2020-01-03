<?php

namespace App\Service;

use App\Entity\WorkOrder;
use App\Entity\WorkOrderTask;
use App\Pdf\CustomTcpdf;
use WhiteOctober\TCPDFBundle\Controller\TCPDFController;

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
     * WorkOrderPdfBuilderService constructor.
     */
    public function __construct()
    {
        $this->tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    /**
     * @param WorkOrder $workOrder
     *
     * @return \TCPDF
     */
    public function build(WorkOrder $workOrder) {
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->AddPage('L', 'A4', true, true);

        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(179, 204, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.3);
        $this->tcpdf->SetFont('', 'B', 7);
        // Header
        $this->tcpdf->Cell(30, 10, 'WTG', 1, 0, 'C', 1);
        $this->tcpdf->Cell(10, 10, 'PALA', 1, 0, 'C', 1);
        $this->tcpdf->Cell(25, 10, 'Nº SERIE', 1, 0, 'C', 1);
        $this->tcpdf->Cell(10, 10, 'DAÑO', 1, 0, 'C', 1);
        $this->tcpdf->Cell(15, 10, 'POSICIÓN', 1, 0, 'C', 1);
        $this->tcpdf->Cell(15, 10, 'RADIO (m)', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 10, 'DISTANCIA (cm)', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 10, 'DIMENSION (cm)', 1, 0, 'C', 1);
        $this->tcpdf->Cell(50, 10, 'DESCRIPCIÓN', 1, 0, 'C', 1);
        $this->tcpdf->Cell(40, 10, 'EQUIPO', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 10, 'FINALIZADA', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 10, 'FOTOS', 1, 0, 'C', 1);
        $this->tcpdf->Ln();
        // Color and font restoration
        $this->tcpdf->SetFillColor(224, 235, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetFont('');
        // Data
        $fillWindmill = 1;
        $fillBlade = 1;
        $windmill = 0;
        $windmillBlade = 0;
        /** @var WorkOrderTask $workOrderTask */
        foreach($workOrder->getWorkOrderTasks() as $workOrderTask) {
            if ($windmill != $workOrderTask->getWindmill()->getId()) {
                $fillWindmill = !$fillWindmill;
                $fillBlade = !$fillBlade;
            } elseif ($windmillBlade != $workOrderTask->getWindmillBlade()->getId()) {
                $fillBlade = !$fillBlade;
            }
            $windmill = $workOrderTask->getWindmill()->getId();
            $windmillBlade =$workOrderTask->getWindmillBlade()->getId();

            $this->tcpdf->Cell(30, 7, $workOrderTask->getWindmill()->getCode(), 1, 0, 'C', $fillWindmill);
            $this->tcpdf->Cell(10, 7, $workOrderTask->getWindmillBlade()->getOrder(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(25, 7,  $workOrderTask->getWindmill()->getBladeType()->getModel(), 1, 0, 'C', $fillBlade);
            if ($workOrderTask->getBladeDamage()) {
                $this->tcpdf->Cell(10, 7, $workOrderTask->getBladeDamage()->getDamageCategory()->getCategory(), 1, 0, 'C', $fillBlade);
            } else {
                $this->tcpdf->Cell(10, 7, '-', 1, 0, 'C', $fillBlade);
            }
            $this->tcpdf->Cell(15, 7, $workOrderTask->getPosition(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(15, 7, $workOrderTask->getRadius(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 7, $workOrderTask->getDistance(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 7, $workOrderTask->getSize(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(50, 7, $workOrderTask->getDescription(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(40, 7, '-', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 7, $workOrderTask->isCompleted()?'SI':'NO', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 7, 'FOTOS', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Ln();
        }

        return $this->tcpdf;
    }
}
