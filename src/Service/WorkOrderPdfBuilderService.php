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

        $this->tcpdf->SetAbsXY(10,45);
        $this->tcpdf->MultiCell(25, 5, 'Nª Proyecto', 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');
        $this->tcpdf->MultiCell(25, 5, $workOrder->getId(), 1, 'C', 0, 0, '', '', true, 0, false, true, 5, 'M');

        $this->tcpdf->SetAbsXY(120,10);
        $this->tcpdf->Cell(55, 7, 'DATOS CLIENTE', 1, 0, 'C', 1);
        $this->tcpdf->Cell(55, 7, 'DATOS AEROS', 1, 0, 'C', 1);
        $this->tcpdf->Cell(55, 7, 'DATOS CERTIFICADORA', 1, 0, 'C', 1);
        $this->tcpdf->Ln();
        // Color and font restoration
        $this->tcpdf->SetFillColor(224, 235, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetFont('');

        $this->tcpdf->SetAbsXY(120,17);
        $this->tcpdf->Cell(20, 5, 'Cliente:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCustomer()->getName(), 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Fabricante:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Empresa:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyName(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(120,22);
        $this->tcpdf->Cell(20, 5, 'Contacto:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCustomer()->getContacts()->first(), 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Pala:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Contacto:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyContactPerson(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(120,27);
        $this->tcpdf->Cell(20, 5, 'Telefono:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCustomer()->getPhone(), 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Material:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Telefono:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyPhone(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(120,32);
        $this->tcpdf->Cell(20, 5, 'Parque:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getWindfarm()->getName(), 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Altura buje:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, '-', 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Email:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getCertifyingCompanyEmail(), 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(120,37);
        $this->tcpdf->Cell(20, 5, 'Localidad:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, $workOrder->getWindfarm()->getCity(), 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, 'Anticaidas:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, '-:', 1, 0, 'C', 0);
        $this->tcpdf->Cell(20, 5, '', 1, 0, 'C', 0);
        $this->tcpdf->Cell(35, 5, '', 1, 0, 'C', 0);

        $this->tcpdf->SetAbsXY(10,60);
        $this->tcpdf->SetFillColor(179, 204, 255);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.3);
        $this->tcpdf->SetFont('', 'B', 7);
        // Header
        $this->tcpdf->Cell(30, 7, 'WTG', 1, 0, 'C', 1);
        $this->tcpdf->Cell(10, 7, 'PALA', 1, 0, 'C', 1);
        $this->tcpdf->Cell(25, 7, 'Nº SERIE', 1, 0, 'C', 1);
        $this->tcpdf->Cell(10, 7, 'DAÑO', 1, 0, 'C', 1);
        $this->tcpdf->Cell(15, 7, 'POSICIÓN', 1, 0, 'C', 1);
        $this->tcpdf->Cell(15, 7, 'RADIO (m)', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 7, 'DISTANCIA (cm)', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 7, 'DIMENSION (cm)', 1, 0, 'C', 1);
        $this->tcpdf->Cell(50, 7, 'DESCRIPCIÓN', 1, 0, 'C', 1);
        $this->tcpdf->Cell(40, 7, 'EQUIPO', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 7, 'FINALIZADA', 1, 0, 'C', 1);
        $this->tcpdf->Cell(20, 7, 'FOTOS', 1, 0, 'C', 1);
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

            $this->tcpdf->Cell(30, 5, $workOrderTask->getWindmill()->getCode(), 1, 0, 'C', $fillWindmill);
            $this->tcpdf->Cell(10, 5, $workOrderTask->getWindmillBlade()->getOrder(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(25, 5,  $workOrderTask->getWindmill()->getBladeType()->getModel(), 1, 0, 'C', $fillBlade);
            if ($workOrderTask->getBladeDamage()) {
                $this->tcpdf->Cell(10, 5, $workOrderTask->getBladeDamage()->getDamageCategory()->getCategory(), 1, 0, 'C', $fillBlade);
            } else {
                $this->tcpdf->Cell(10, 5, '-', 1, 0, 'C', $fillBlade);
            }
            $this->tcpdf->Cell(15, 5, $workOrderTask->getPosition(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(15, 5, $workOrderTask->getRadius(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 5, $workOrderTask->getDistance(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 5, $workOrderTask->getSize(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(50, 5, $workOrderTask->getDescription(), 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(40, 5, '-', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 5, $workOrderTask->isCompleted()?'SI':'NO', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Cell(20, 5, 'FOTOS', 1, 0, 'C', $fillBlade);
            $this->tcpdf->Ln();
        }

        return $this->tcpdf;
    }
}
