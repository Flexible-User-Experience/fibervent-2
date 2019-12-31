<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class BackendControllerTest
 */
class BackendControllerTest extends WebTestCase
{
    /**
     * Test backend pages
     */
    public function testLoginPage()
    {
        $client = static::createClient();         // anonymous user
        $client->request('GET', '/admin/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Test HTTP request is successful.
     *
     * @dataProvider provideSuccessfulUrls
     *
     * @param string $url
     */
    public function testAdminPagesAreSuccessful($url)
    {
        $client = static::createClient([], [     // authenticated user
            'PHP_AUTH_USER' => 'test1',
            'PHP_AUTH_PW'   => '$testpwd1',
        ]);
        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Successful Urls provider.
     *
     * @return array
     */
    public function provideSuccessfulUrls()
    {
        return array(
            array('/admin/dashboard'),
            array('/admin/customers/customer/list'),
            array('/admin/customers/customer/create'),
            array('/admin/customers/customer/1/edit'),
            array('/admin/customers/customer/1/map'),
            array('/admin/customers/state/list'),
            array('/admin/customers/state/create'),
            array('/admin/customers/state/1/delete'),
            array('/admin/customers/state/1/edit'),
            array('/admin/windfarms/windfarm/list'),
            array('/admin/windfarms/windfarm/create'),
            array('/admin/windfarms/windfarm/1/edit'),
            array('/admin/windfarms/windfarm/1/show'),
            array('/admin/windfarms/windfarm/1/map'),
            array('/admin/windfarms/windfarm/1/excel'),
            array('/admin/windfarms/windfarm/1/pdf'),
            array('/admin/windfarms/windmill/list'),
            array('/admin/windfarms/windmill/create'),
            array('/admin/windfarms/windmill/1/edit'),
            array('/admin/windfarms/turbine/list'),
            array('/admin/windfarms/turbine/create'),
            array('/admin/windfarms/turbine/1/edit'),
            array('/admin/windfarms/blade/list'),
            array('/admin/windfarms/blade/create'),
            array('/admin/windfarms/blade/1/edit'),
            array('/admin/windfarms/windmill-blade/list'),
            array('/admin/windfarms/windmill-blade/create'),
            array('/admin/windfarms/windmill-blade/1/delete'),
            array('/admin/windfarms/windmill-blade/1/edit'),
            array('/admin/audits/audit/list'),
            array('/admin/audits/audit/create'),
            array('/admin/audits/audit/1/delete'),
            array('/admin/audits/audit/1/edit'),
            array('/admin/audits/audit/1/show'),
            array('/admin/audits/damage/list'),
            array('/admin/audits/damage/create'),
            array('/admin/audits/damage/1/edit'),
            array('/admin/audits/damage-category/list'),
            array('/admin/audits/damage-category/create'),
            array('/admin/audits/damage-category/1/edit'),
            array('/admin/audits/blade-damage/list'),
            array('/admin/audits/blade-damage/create'),
            array('/admin/audits/blade-damage/1/edit'),
            array('/admin/audits/blade-photo/list'),
            array('/admin/audits/blade-photo/create'),
            array('/admin/audits/blade-photo/1/edit'),
            array('/admin/audits/observation/1/edit'),
            array('/admin/audits/observation/list'),
            array('/admin/audits/observation/create'),
            array('/admin/audits/observation/1/edit'),
            array('/admin/audits/photo/list'),
            array('/admin/audits/photo/create'),
            array('/admin/audits/photo/1/delete'),
            array('/admin/audits/photo/1/edit'),
            array('/admin/audits/audit-windmill-blade/list'),
            array('/admin/audits/audit-windmill-blade/create'),
            array('/admin/audits/audit-windmill-blade/1/edit'),
            array('/admin/users/list'),
            array('/admin/users/create'),
            array('/admin/users/1/edit'),
            array('/admin/users/profile'),
            array('/admin/workorders/workorder/list'),
            array('/admin/workorders/workorder/create'),
            array('/admin/workorders/workorder/1/edit'),
            array('/admin/workorders/workordertask/list'),
            array('/admin/workorders/workordertask/create'),
            array('/admin/workorders/workordertask/1/edit'),
            array('/admin/workorders/vehicle/list'),
            array('/admin/workorders/vehicle/create'),
            array('/admin/workorders/vehicle/1/edit'),
            array('/admin/workorders/deliverynote/list'),
            array('/admin/workorders/deliverynote/create'),
            array('/admin/workorders/deliverynote/1/edit'),
            array('/admin/workorders/deliverynotetimeregister/list'),
            array('/admin/workorders/deliverynotetimeregister/create'),
            array('/admin/workorders/deliverynotetimeregister/1/edit'),
            array('/admin/workorders/nonstandardusedmaterial/list'),
            array('/admin/workorders/nonstandardusedmaterial/create'),
            array('/admin/workorders/nonstandardusedmaterial/1/edit'),
            array('/admin/workorders/workertimesheet/list'),
            array('/admin/workorders/workertimesheet/create'),
            array('/admin/workorders/workertimesheet/1/edit'),
            array('/admin/presencemonitoring/list'),
            array('/admin/presencemonitoring/create'),
            array('/admin/presencemonitoring/1/edit'),
        );
    }

    /**
     * Test HTTP request is not found.
     *
     * @dataProvider provideNotFoundUrls
     *
     * @param string $url
     */
    public function testAdminPagesAreNotFound($url)
    {
        $client = static::createClient([], [     // authenticated user
            'PHP_AUTH_USER' => 'test1',
            'PHP_AUTH_PW'   => '$testpwd1',
        ]);
        $client->request('GET', $url);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Not found Urls provider.
     *
     * @return array
     */
    public function provideNotFoundUrls()
    {
        return array(
            array('/admin/customers/customer/batch'),
            array('/admin/customers/customer/1/delete'),
            array('/admin/customers/country/batch'),
            array('/admin/customers/state/batch'),
            array('/admin/windfarms/windfarm/batch'),
            array('/admin/windfarms/windfarm/1/delete'),
            array('/admin/windfarms/windmill/batch'),
            array('/admin/windfarms/windmill/1/delete'),
            array('/admin/windfarms/blade/1/delete'),
            array('/admin/windfarms/turbine/1/delete'),
            array('/admin/audits/damage/1/delete'),
            array('/admin/audits/blade-damage/1/delete'),
            array('/admin/audits/blade-photo/1/delete'),
            array('/admin/audits/observation/1/delete'),
            array('/admin/audits/audit-windmill-blade/1/delete'),
            array('/admin/audits/damage-category/1/delete'),
            array('/admin/users/show'),
            array('/admin/users/1/delete'),
            array('/admin/users/batch'),
        );
    }
}
