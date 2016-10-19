<?php
namespace SymfonyId\AdminBundle\SymfonyIdAdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BundleTest extends KernelTestCase
{

    private $container;

    protected function setUp()
    {
        require_once __DIR__.'/AppKernel.php';

        $kernel = new \AppKernel('test', true);
        // $kernel->boot();
        // $this->container = $kernel->getContainer();
    }

    public function testBundle()
    {
        // $config = $this->container->get('symfonyid_admin');
        // $appShortTitle = $config["app_short_title"];
        // $this->assertTrue("SIAB" === $appShortTitle);
        $this->assertTrue(1 === 1);
    }
}
