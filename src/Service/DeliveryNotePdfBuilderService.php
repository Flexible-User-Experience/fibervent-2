<?php

namespace App\Service;

use App\Entity\DeliveryNote;
use App\Enum\AuditLanguageEnum;
use Symfony\Contracts\Translation\TranslatorInterface;
use TCPDF;

/**
 * Class Delivery Note Pdf Builder Service.
 *
 * @category Service
 */
class DeliveryNotePdfBuilderService
{
    const PDF_MARGIN_LEFT = 8;
    const PDF_MARGIN_RIGHT = 8;
    const PDF_MARGIN_TOP = 8;
    const PDF_MARGIN_BOTTOM = 4;

    const H_DIVISOR = 92;
    
    /**
     * @var TCPDF
     */
    private TCPDF $tcpdf;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $ts;

    /**
     * @var SmartAssetsHelperService
     */
    private SmartAssetsHelperService $sahs;

    /**
     * Methods.
     */

    /**
     * DeliveryNotePdfBuilderService constructor.
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
     * @param DeliveryNote $dn
     *
     * @return TCPDF
     */
    public function build(DeliveryNote $dn) {
        $this->ts->setLocale(AuditLanguageEnum::DEFAULT_LANGUAGE_STRING);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetMargins(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, self::PDF_MARGIN_RIGHT, true);
        $this->tcpdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM);
        $this->tcpdf->AddPage('P', 'A4', true, true);
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetFilePath('/build/fibervent_logo_white_landscape.jpg'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, 60, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetFont('', 'B', 7);

        // delivery note header table info
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT + self::H_DIVISOR, self::PDF_MARGIN_TOP);
        $this->tcpdf->Cell(14, 5, $this->ts->trans('admin.deliverynote.title'), 1, 0, 'C', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(20, 5, $dn->getId(), 1, 0, 'C', false);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(14, 5, $this->ts->trans('admin.deliverynote.id'), 1, 0, 'C', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(20, 5, $dn->getWorkOrder()->getId(), 1, 0, 'C', false);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(14, 5, $this->ts->trans('admin.deliverynote.date'), 1, 0, 'C', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(20, 5, $dn->getDateString(), 1, 1, 'C', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // customer data header table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.deliverynote.pdf.customer_data'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('Cliente'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getWorkOrder()->getCustomer()->getName(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.windfarm'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getWorkOrder()->getWindfarm()->getName(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.city'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getWorkOrder()->getWindfarm()->getCity(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // windfarm data header table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.deliverynote.pdf.windfarm_data'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('Aerogenerador'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, '---', 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.work_in'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, '---', 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.blade_number'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, '---', 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.serial_number'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, '---', 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // windfarm data header table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.deliverynote.pdf.business_data'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.team_leader'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getTeamLeader()->getFullname(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.team_technician_1'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getTeamTechnician1() ? $dn->getTeamTechnician1()->getFullname() : '---', 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.team_technician_2'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getTeamTechnician2() ? $dn->getTeamTechnician2()->getFullname() : '---', 1, 1, 'L', false);
        if ($dn->getTeamTechnician3()) {
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
            $this->tcpdf->SetFont('', 'B', 7);
            $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.team_technician_3'), 1, 0, 'L', false);
            $this->tcpdf->SetFont('', '', 7);
            $this->tcpdf->Cell(80, 5, $dn->getTeamTechnician3()->getFullname(), 1, 1, 'L', false);
        }
        if ($dn->getTeamTechnician4()) {
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
            $this->tcpdf->SetFont('', 'B', 7);
            $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.team_technician_4'), 1, 0, 'L', false);
            $this->tcpdf->SetFont('', '', 7);
            $this->tcpdf->Cell(80, 5, $dn->getTeamTechnician4()->getFullname(), 1, 1, 'L', false);
        }
        if ($dn->getVehicle()) {
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
            $this->tcpdf->SetFont('', 'B', 7);
            $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.vehicle.title'), 1, 0, 'L', false);
            $this->tcpdf->SetFont('', '', 7);
            $this->tcpdf->Cell(80, 5, $dn->getVehicle()->getName(), 1, 1, 'L', false);
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
            $this->tcpdf->SetFont('', 'B', 7);
            $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.vehicle.licence_plate'), 1, 0, 'L', false);
            $this->tcpdf->SetFont('', '', 7);
            $this->tcpdf->Cell(80, 5, $dn->getVehicle()->getLicensePlate(), 1, 1, 'L', false);
        }
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // access type table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.deliverynote.repair_access_types'), 1, 1, 'C', true);

        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.crane_company'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getCraneCompany(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.crane_driver'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getCraneDriver(), 1, 1, 'L', false);


        return $this->tcpdf;
    }
}
