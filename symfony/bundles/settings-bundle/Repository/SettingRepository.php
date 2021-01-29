<?php

namespace Bundles\SettingsBundle\Repository;

use Bundles\SettingsBundle\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class SettingRepository extends ServiceEntityRepository
{
    public function __construct(Registry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    /**
     * @return array
     */
    public function all() : array
    {
        $settings = [];
        $entities = $this->findAll();

        foreach ($entities as $entity) {
            $settings[$entity->getProperty()] = $entity->getValue();
        }

        return $settings;
    }

    /**
     * @param string      $property
     * @param string|null $default
     *
     * @return string|null
     */
    public function get(string $property, ?string $default = null) : ?string
    {
        $entity = $this->findOneByProperty($property);

        if ($entity) {
            return $entity->getValue();
        }

        return $default;
    }

    /**
     * @param string $property
     * @param string $value
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function set(string $property, string $value)
    {
        $entity = $this->findOneByProperty($property);

        if (!$entity) {
            $entity = new Setting();
            $entity->setProperty($property);
        }

        $entity->setValue($value);

        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * @param string $property
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(string $property)
    {
        $entity = $this->findOneByProperty($property);

        if ($entity) {
            $this->_em->remove($entity);
            $this->_em->flush();
        }
    }
}