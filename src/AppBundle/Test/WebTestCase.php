<?php


namespace AppBundle\Test;

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    protected function generateSchema(ContainerInterface $container)
    {
        $em = $this->getDoctrine($container);

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
     * @param ContainerInterface $container
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getDoctrine(ContainerInterface $container)
    {
        return $container->get('doctrine')->getManager();
    }
}