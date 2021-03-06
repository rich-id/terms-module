<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Stubs;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use RichCongress\WebTestBundle\OverrideService\AbstractOverrideService;

final class EntityManagerStub extends AbstractOverrideService implements EntityManagerInterface
{
    /** @var string|array<string> */
    public static $overridenServices = EntityManagerInterface::class;

    /** @var array<object> */
    protected $persistedEntities = [];

    /** @var array<object> */
    protected $removedEntities = [];

    /** @var EntityManagerInterface */
    protected $innerService;

    public function getRepository($className)
    {
        return $this->innerService->getRepository($className);
    }

    public function getCache()
    {
        return $this->innerService->getCache();
    }

    public function getConnection()
    {
        return $this->innerService->getConnection();
    }

    public function getExpressionBuilder()
    {
        return $this->innerService->getExpressionBuilder();
    }

    public function beginTransaction(): void
    {
        $this->innerService->beginTransaction();
    }

    public function transactional($func)
    {
        return $this->innerService->transactional($func);
    }

    public function commit(): void
    {
        $this->innerService->commit();
    }

    public function rollback(): void
    {
        $this->innerService->rollback();
    }

    public function createQuery($dql = '')
    {
        return $this->innerService->createQuery($dql);
    }

    public function createNamedQuery($name)
    {
        return $this->innerService->createNamedQuery($name);
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        return $this->innerService->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name)
    {
        return $this->innerService->createNamedNativeQuery($name);
    }

    public function createQueryBuilder()
    {
        return $this->innerService->createQueryBuilder();
    }

    public function getReference($entityName, $id)
    {
        return $this->innerService->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        return $this->innerService->getPartialReference($entityName, $identifier);
    }

    public function close(): void
    {
        $this->innerService->close();
    }

    public function copy($entity, $deep = false)
    {
        return $this->innerService->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null): void
    {
        $this->innerService->lock($entity, $lockMode, $lockVersion);
    }

    public function getEventManager()
    {
        return $this->innerService->getEventManager();
    }

    public function getConfiguration()
    {
        return $this->innerService->getConfiguration();
    }

    public function isOpen()
    {
        return $this->innerService->isOpen();
    }

    public function getUnitOfWork()
    {
        return $this->innerService->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        return $this->innerService->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode)
    {
        return $this->innerService->newHydrator($hydrationMode);
    }

    public function getProxyFactory()
    {
        return $this->innerService->getProxyFactory();
    }

    public function getFilters()
    {
        return $this->innerService->getFilters();
    }

    public function isFiltersStateClean()
    {
        return $this->innerService->isFiltersStateClean();
    }

    public function hasFilters()
    {
        return $this->innerService->hasFilters();
    }

    public function getClassMetadata($className)
    {
        /* @phpstan-ignore-next-line */
        return $this->innerService->getClassMetadata($className);
    }

    public function find($className, $id)
    {
        return $this->innerService->find($className, $id);
    }

    public function persist($object): void
    {
        $this->persistedEntities[] = $object;
        $this->innerService->persist($object);
    }

    public function remove($object): void
    {
        $this->removedEntities[] = $object;
        $this->innerService->remove($object);
    }

    public function merge($object)
    {
        return $this->innerService->merge($object);
    }

    public function clear($objectName = null): void
    {
        $this->innerService->clear($objectName);
    }

    public function detach($object): void
    {
        $this->innerService->detach($object);
    }

    public function refresh($object): void
    {
        $this->innerService->refresh($object);
    }

    public function flush(): void
    {
        $this->innerService->flush();
    }

    public function initializeObject($obj): void
    {
        $this->innerService->initializeObject($obj);
    }

    public function contains($object)
    {
        return $this->innerService->contains($object);
    }

    public function getMetadataFactory()
    {
        /* @phpstan-ignore-next-line */
        return $this->innerService->getMetadataFactory();
    }

    /** @return array<object> */
    public function getPersistedEntities(): array
    {
        return $this->persistedEntities;
    }

    /** @return array<object> */
    public function getRemovedEntities(): array
    {
        return $this->removedEntities;
    }
}
