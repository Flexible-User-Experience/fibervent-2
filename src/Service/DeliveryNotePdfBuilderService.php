<?php

namespace App\Service;

use App\Entity\DeliveryNote;
use App\Entity\DeliveryNoteTimeRegister;
use App\Entity\NonStandardUsedMaterial;
use App\Enum\AuditLanguageEnum;
use App\Enum\TimeRegisterShiftEnum;
use App\Enum\TimeRegisterTypeEnum;
use App\Manager\DeliveryNoteTimeRegisterManager;
use Exception;
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
     * @var DeliveryNoteTimeRegisterManager
     */
    private DeliveryNoteTimeRegisterManager $dntrm;

    /**
     * Methods.
     */

    /**
     * DeliveryNotePdfBuilderService constructor.
     *
     * @param TranslatorInterface             $ts
     * @param SmartAssetsHelperService        $sahs
     * @param DeliveryNoteTimeRegisterManager $dntrm
     */
    public function __construct(TranslatorInterface $ts, SmartAssetsHelperService $sahs, DeliveryNoteTimeRegisterManager $dntrm)
    {
        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->ts = $ts;
        $this->sahs = $sahs;
        $this->dntrm = $dntrm;
    }

    /**
     * @param DeliveryNote $dn
     *
     * @return TCPDF
     * @throws Exception
     */
    public function build(DeliveryNote $dn) {
        $this->ts->setLocale(AuditLanguageEnum::DEFAULT_LANGUAGE_STRING);
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetMargins(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, self::PDF_MARGIN_RIGHT, true);
        $this->tcpdf->SetAutoPageBreak(true, self::PDF_MARGIN_BOTTOM);
        $this->tcpdf->AddPage('P', 'A4', true, true);

        // LEFT COLUMN
        // left image header
        $this->tcpdf->Image($this->sahs->getAbsoluteAssetFilePath('/build/fibervent_logo_white_landscape.jpg'), self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP, 60, 0, 'JPEG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Colors, line width and bold font
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetLineWidth(0.1);
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT, self::PDF_MARGIN_TOP + 18);
        $this->tcpdf->SetFont('', '', 8);
        $this->tcpdf->SetTextColor(50, 118, 179);
        $this->tcpdf->Cell(85, 5, $this->ts->trans('fibervent.name'), 0, 1, 'L', false);
        $this->tcpdf->SetTextColor(0);
        $this->tcpdf->Cell(85, 5, $this->ts->trans('fibervent.address_1'), 0, 1, 'L', false);
        $this->tcpdf->Cell(85, 5, $this->ts->trans('fibervent.address_2'), 0, 1, 'L', false);
        $this->tcpdf->Cell(85, 5, $this->ts->trans('fibervent.tel'), 0, 1, 'L', false);
        $this->tcpdf->Cell(85, 5, $this->ts->trans('fibervent.email'), 0, 1, 'L', false, 'mailto:info@fibervent.com');
        $this->tcpdf->SetTextColor(50, 118, 179);
        $this->tcpdf->Cell(85, 5, $this->ts->trans('fibervent.web'), 0, 1, 'L', false, 'http://www.fibervent.com/');
        $this->tcpdf->Cell(85, 5, '', 0, 1, 'L', false);
        $this->tcpdf->SetTextColor(0);

        $dntrs = $this->dntrm->getDeliveryNoteTimeRegistersSortedAndFormatedArray($dn);
        // morning trip table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::TRIP] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.trip').' '.$this->ts->trans('enum.time_register_shift.morning'),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.presencemonitoring.end')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.presencemonitoring.arrival'))
            );
        }
        if (count($dntrs[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::TRIP]) > 0) {
            $this->drawTotalHourCells($dntrs['total_trip_morning_hours']);
            $this->tcpdf->Cell(10, 5, '', 0, 1, 'L', false);
        }
        // morning works table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::WORK] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.work').' '.$this->ts->trans('enum.time_register_shift.morning'),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.begin')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.end'))
            );
        }
        // afternoon works table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::WORK] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.work').' '.strtolower($this->ts->trans('enum.time_register_shift.afternoon')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.begin')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.end'))
            );
        }
        // night works table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::WORK] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.work').' '.strtolower($this->ts->trans('enum.time_register_shift.night')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.begin')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.end'))
            );
        }
        if (count($dntrs[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::WORK]) > 0 || count($dntrs[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::WORK]) > 0 || count($dntrs[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::WORK]) > 0) {
            $this->drawTotalHourCells($dntrs['total_work_hours']);
            $this->tcpdf->Cell(10, 5, '', 0, 1, 'L', false);
        }
        // morning stops table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::STOP] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.stop').' '.$this->ts->trans('enum.time_register_shift.morning'),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.stop')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.end'))
            );
        }
        // afternoon stops table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::STOP] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.stop').' '.strtolower($this->ts->trans('enum.time_register_shift.afternoon')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.stop')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.end'))
            );
        }
        // night stops table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::STOP] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.stop').' '.strtolower($this->ts->trans('enum.time_register_shift.night')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.stop')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.deliverynotetimeregister.end'))
            );
        }
        if (count($dntrs[TimeRegisterShiftEnum::MORNING][TimeRegisterTypeEnum::STOP]) > 0 || count($dntrs[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::STOP]) > 0 || count($dntrs[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::STOP]) > 0) {
            $this->drawTotalHourCells($dntrs['total_stop_hours']);
            $this->tcpdf->Cell(10, 5, '', 0, 1, 'L', false);
        }
        // afternoon trip table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::TRIP] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.trip').' '.strtolower($this->ts->trans('enum.time_register_shift.afternoon')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.presencemonitoring.end')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.presencemonitoring.arrival'))
            );
        }
        if (count($dntrs[TimeRegisterShiftEnum::AFTERNOON][TimeRegisterTypeEnum::TRIP]) > 0) {
            $this->drawTotalHourCells($dntrs['total_trip_afternoon_hours']);
            $this->tcpdf->Cell(10, 5, '', 0, 1, 'L', false);
        }
        // night trip table section
        /** @var DeliveryNoteTimeRegister $dntr */
        foreach ($dntrs[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::TRIP] as $dntr) {
            $this->drawTimeRegister(
                $dntr,
                $this->ts->trans('enum.time_register_type.trip').' '.strtolower($this->ts->trans('enum.time_register_shift.night')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.presencemonitoring.end')),
                $this->ts->trans('admin.presencemonitoring.hour').' '.strtolower($this->ts->trans('admin.presencemonitoring.arrival'))
            );
        }
        if (count($dntrs[TimeRegisterShiftEnum::NIGHT][TimeRegisterTypeEnum::TRIP]) > 0) {
            $this->drawTotalHourCells($dntrs['total_trip_night_hours']);
            $this->tcpdf->Cell(10, 5, '', 0, 1, 'L', false);
        }
        $maxLefColumnHeightReached = $this->tcpdf->GetY();

        // RIGHT COLUMN
        // delivery note header table info
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetAbsXY(self::PDF_MARGIN_LEFT + self::H_DIVISOR, self::PDF_MARGIN_TOP);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(14, 5, $this->ts->trans('admin.deliverynote.title'), 1, 0, 'C', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(20, 5, $dn->getId(), 1, 0, 'C', false);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(14, 5, $this->ts->trans('admin.workorder.title'), 1, 0, 'C', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(20, 5, $dn->getWorkOrder()->getProjectNumber(), 1, 0, 'C', false);
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
        $this->tcpdf->Cell(80, 5, $dn->getRepairWindmillSectionsString(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.blade_number'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, '---'/* TODO */, 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.pdf.serial_number'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, '---'/* TODO */, 1, 1, 'L', false);
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
        if ($dn->getTeamTechnician2()) {
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
            $this->tcpdf->SetFont('', 'B', 7);
            $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.team_technician_2'), 1, 0, 'L', false);
            $this->tcpdf->SetFont('', '', 7);
            $this->tcpdf->Cell(80, 5, $dn->getTeamTechnician2()->getFullname(), 1, 1, 'L', false);
        }
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
            $this->tcpdf->Cell(80, 5, $dn->getVehicle()->getName().' · '.$dn->getVehicle()->getLicensePlate(), 1, 1, 'L', false);
        }
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // access type table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.deliverynote.repair_access_types'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.repair_access_types'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getRepairAccessTypesString(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.crane_company'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getCraneCompany(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(22, 5, $this->ts->trans('admin.deliverynote.crane_driver'), 1, 0, 'L', false);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(80, 5, $dn->getCraneDriver(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // job description table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.deliverynote.pdf.job_description'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(102, 5, '----'/* TODO iterations */, 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        // non standard userd materials table info
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(102, 5, $this->ts->trans('admin.nonstandardusedmaterial.title'), 1, 1, 'C', true);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->Cell(13, 5, $this->ts->trans('admin.nonstandardusedmaterial.quantity'), 1, 0, 'R', true);
        $this->tcpdf->Cell(17, 5, $this->ts->trans('admin.nonstandardusedmaterial.item'), 1, 0, 'L', true);
        $this->tcpdf->Cell(72, 5, $this->ts->trans('admin.nonstandardusedmaterial.description'), 1, 1, 'L', true);
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetFont('', '', 7);
        /** @var NonStandardUsedMaterial $nsum */
        foreach ($dn->getNonStandardUsedMaterials() as $nsum) {
            $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
            $this->tcpdf->Cell(13, 5, $nsum->getQuantity(), 1, 0, 'R', false);
            $this->tcpdf->Cell(17, 5, $this->ts->trans($nsum->getItemString()), 1, 0, 'L', false);
            $this->tcpdf->Cell(72, 5, $nsum->getDescription(), 1, 1, 'L', false);
        }
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(102, 5, '', 0, 1, 'C', false);

        $fixedYPoint = $this->tcpdf->GetY();
        if ($fixedYPoint < $maxLefColumnHeightReached) {
            $fixedYPoint = $maxLefColumnHeightReached;
        }

        // observations table info
        $this->tcpdf->SetXY(self::PDF_MARGIN_LEFT, $fixedYPoint);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(194, 5, $this->ts->trans('admin.deliverynote.observations_long'), 1, 1, 'C', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(194, 5, $dn->getObservations(), 1, 1, 'L', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + self::H_DIVISOR);
        $this->tcpdf->Cell(194, 5, '', 0, 1, 'C', false);

        // final sign boxes
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(60, 6, $this->ts->trans('admin.deliverynote.pdf.customer_sing_box'), 1, 0, 'L', true);
        $this->tcpdf->Cell(15, 6, '', 0, 0, 'L', false);
        $this->tcpdf->Cell(60, 6, $this->ts->trans('admin.deliverynote.pdf.fibervent_sing_box'), 1, 1, 'L', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(60, 16, $this->ts->trans('admin.deliverynote.pdf.dni_sing_box'), 1, 0, 'L', false, '', 0, false, 'T', 'B');
        $this->tcpdf->Cell(15, 16, '', 0, 0, 'L', false);
        $this->tcpdf->Cell(60, 16, $this->ts->trans('admin.deliverynote.pdf.dni_sing_box'), 1, 1, 'L', false, '', 0, false, 'T', 'B');

        return $this->tcpdf;
    }

    /**
     * @param DeliveryNoteTimeRegister $dntr
     * @param string                   $head
     * @param string                   $title1
     * @param string                   $title2
     */
    private function drawTimeRegister(DeliveryNoteTimeRegister $dntr, string $head, string $title1, string $title2)
    {
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT);
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(31, 10, $head, 1, 0, 'L', true);
        $this->tcpdf->SetFillColor(183, 223, 234);
        $this->tcpdf->Cell(17, 5, $title1, 1, 0, 'R', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(14, 5, $dntr->getBeginString(), 1, 1, 'C', false);
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + 31);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(17, 5, $title2, 1, 0, 'R', true);
        $this->tcpdf->SetFont('', '', 7);
        if ($dntr->getComment()) {
            $this->tcpdf->Cell(14, 5, $dntr->getEndString(), 1, 0, 'C', false);
            $this->tcpdf->Cell(27, 5, $dntr->getComment(), 1, 1, 'L', false);
        } else {
            $this->tcpdf->Cell(14, 5, $dntr->getEndString(), 1, 1, 'C', false);
        }
    }

    /**
     * @param int|string $value
     */
    private function drawTotalHourCells($value)
    {
        $this->tcpdf->SetX(self::PDF_MARGIN_LEFT + 31);
        $this->tcpdf->SetFillColor(108, 197, 205);
        $this->tcpdf->SetFont('', 'B', 7);
        $this->tcpdf->Cell(17, 5, $this->ts->trans('admin.presencemonitoring.total_hours'), 1, 0, 'R', true);
        $this->tcpdf->SetFont('', '', 7);
        $this->tcpdf->Cell(14, 5, $value, 1, 1, 'C', true);
    }
}
