<?php


namespace tests\AppBundle\Repository;


use AppBundle\Entity\Statistic;
use AppBundle\Repository\StatisticRepository;
use AppBundle\Test\KernelTestCase;

class StatisticRepositoryTest extends KernelTestCase
{
    /** @var  StatisticRepository */
    protected $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->getEntityManager()->getRepository(
          'AppBundle:Statistic'
        );
    }

    public function testAddView()
    {
        /** @var Statistic $nbViews */
        $nbViews = $this->repository->findOneBy(array('name' => StatisticRepository::STATISTIC_NUMBER_VIEWS));
        $this->assertNull($nbViews);

        $this->repository->addView();

        $nbViews = $this->repository->findOneBy(array('name' => StatisticRepository::STATISTIC_NUMBER_VIEWS));
        $this->assertEquals(1, $nbViews->getIntegerValue());

        $this->repository->addView();

        $nbViews = $this->repository->findOneBy(array('name' => StatisticRepository::STATISTIC_NUMBER_VIEWS));
        $this->assertEquals(2, $nbViews->getIntegerValue());
    }

    public function testGetNumberOfViews()
    {
        $this->assertEquals(0, $this->repository->getNumberViews());

        $statistic = new Statistic();
        $statistic->setName(StatisticRepository::STATISTIC_NUMBER_VIEWS);
        $statistic->setValue(1);

        $this->getEntityManager()->persist($statistic);
        $this->getEntityManager()->flush($statistic);

        $this->assertEquals(1, $this->repository->getNumberViews());
    }
}