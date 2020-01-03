<?php

namespace App\Service;

use App\Entity\WorkOrder;
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

    public function build() {
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->AddPage('L', 'A4', true, true);

        return $this->tcpdf;
    }
}
