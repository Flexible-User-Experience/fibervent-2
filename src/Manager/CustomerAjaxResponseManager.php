<?php

namespace App\Manager;

use App\Model\AjaxResponse;
use App\Repository\AuditRepository;

/**
 * Class CustomerAjaxResponseManager.
 *
 * @category Manager
 */
class CustomerAjaxResponseManager
{
    /**
     * @var AuditRepository
     */
    private $ar;

    /**
     * Methods.
     */

    /**
     * CustomerAjaxResponseManager constructor.
     *
     * @param AuditRepository $ar
     */
    public function __construct(AuditRepository $ar)
    {
        $this->ar = $ar;
    }

    /**
     * @param int $cid
     *
     * @return false|string
     */
    public function getAuditsAjaxResponseFromCustomerId($cid)
    {
        $ajaxResponse = new AjaxResponse();
        $ajaxResponse->setData($this->ar->getAuditsFromCustomerIdForAjaxSelectLoad($cid));

        return $ajaxResponse->getJsonEncodedResult();
    }
}
