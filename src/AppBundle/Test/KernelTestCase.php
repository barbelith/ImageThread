<?php


namespace AppBundle\Test;


use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class KernelTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var  KernelInterface */
    protected $kernel;

    protected function setUp()
    {
        parent::setUp();

        $this->kernel = $this->getRealKernel();
    }

    protected function tearDown()
    {
        if ($this->kernel) {
            $this->kernel->shutdown();
        }

        parent::tearDown();
    }

    public function getRealKernel()
    {
        require_once(__DIR__."/../../../app/AppKernel.php");

        /** @var KernelInterface $kernel */
        $kernel = new \AppKernel("test", false);
        $kernel->boot();

        $this->generateSchema($kernel->getContainer());

        return $kernel;
    }

    protected static function generateSchema(ContainerInterface $container)
    {
        $em = $container->get('doctrine')->getManager();

        // Get the metadata of the application to create the schema.
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            // Create SchemaTool
            $tool = new SchemaTool($em);
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }

    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
}