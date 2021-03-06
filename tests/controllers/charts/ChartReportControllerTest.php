<?php

use League\FactoryMuffin\Facade as FactoryMuffin;

/**
 * Class ChartReportControllerTest
 */
class ChartReportControllerTest extends TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers FireflyIII\Http\Controllers\Chart\ReportController::yearInOut
     */
    public function testYearInOut()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);

        $this->call('GET', '/chart/report/in-out/2015');
        $this->assertResponseOk();

    }

    /**
     * @covers FireflyIII\Http\Controllers\Chart\ReportController::yearInOut
     */
    public function testYearInOutShared()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);

        $this->call('GET', '/chart/report/in-out/2015/shared');
        $this->assertResponseOk();

    }

    /**
     * @covers FireflyIII\Http\Controllers\Chart\ReportController::yearInOutSummarized
     */
    public function testYearInOutSummarized()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);

        $this->call('GET', '/chart/report/in-out-sum/2015');
        $this->assertResponseOk();
    }

    /**
     * @covers FireflyIII\Http\Controllers\Chart\ReportController::yearInOutSummarized
     */
    public function testYearInOutSummarizedShared()
    {
        $user = FactoryMuffin::create('FireflyIII\User');
        $this->be($user);

        $this->call('GET', '/chart/report/in-out-sum/2015/shared');
        $this->assertResponseOk();
    }
}
