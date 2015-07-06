<?php
use \PHPUnit_Framework_TestCase as TestCase;
use boot\Bootstrap;
use Entity\Brand;
use Entity\Actor;

/**
 * Brand test case.
 */
class BrandTest extends TestCase
{

    /**
     *
     * @var Brand
     */
    private $Brand;
    
    private $entityManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->entityManager = Bootstrap::getApp()->container->get("entityManager");
        // TODO Auto-generated BrandTest::setUp()
        
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * Tests Brand->setId()
     */
    public function testSetId()
    {
        $this->assertEquals(1, 2);
/*         $actor = new Actor();
        $actor->setFirstName("chen");
       $actor->setLastName("macro");
       $actor->setLastUpdate(new DateTime(time()));
       $this->entityManager -> persist($actor);
        $this->entityManager->flush($actor); */
    }

}

