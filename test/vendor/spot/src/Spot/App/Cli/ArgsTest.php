<?php

namespace Spot\App\Cli;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-06-06 at 17:55:51.
 */
class ArgsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Args
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testCreate_from_string_line() {
        $this->assertEquals($args[0], Args::createFromString("command"));
    }
}
