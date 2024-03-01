<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Stubs;

use DateTimeInterface;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
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

    public function find(string $className, mixed $id, /*LockMode|int|null*/ $lockMode = null, ?int $lockVersion = null): ?object
    {
        return $this->innerService->find($className, $id);
    }

    public function persist(object $object): void
    {
        $this->persistedEntities[] = $object;
        $this->innerService->persist($object);
    }

    public function remove(object $object): void
    {
        $this->removedEntities[] = $object;
        $this->innerService->remove($object);
    }

    public function clear(): void
    {
        $this->innerService->clear();
    }

    public function detach(object $object): void
    {
        $this->innerService->detach($object);
    }

    public function refresh(object $object, LockMode|int|null $lockMode = null): void
    {
        $this->innerService->refresh($object);
    }

    public function flush(): void
    {
        $this->innerService->flush();
    }

    public function getRepository(/*string*/ $className): EntityRepository
    {
        return $this->innerService->getRepository($className);
    }

    public function getClassMetadata(/*string*/ $className): ClassMetadata
    {
        return $this->innerService->getClassMetadata($className);
    }

    public function getMetadataFactory(): ClassMetadataFactory
    {
        return $this->innerService->getMetadataFactory();
    }

    public function initializeObject(object $obj): void
    {
        $this->innerService->initializeObject($obj);
    }

    public function contains(object $object): bool
    {
        return $this->innerService->contains($object);
    }

    public function getCache(): ?Cache
    {
        return $this->innerService->getCache();
    }

    public function getConnection(): Connection
    {
        return $this->innerService->getConnection();
    }

    public function getExpressionBuilder(): Expr
    {
        return $this->innerService->getExpressionBuilder();
    }

    public function beginTransaction(): void
    {
        $this->innerService->beginTransaction();
    }

    public function wrapInTransaction(callable $func): mixed
    {
        return $this->innerService->wrapInTransaction($func);
    }

    public function commit(): void
    {
        $this->innerService->commit();
    }

    public function rollback(): void
    {
        $this->innerService->rollback();
    }

    public function createQuery(/*string*/ $dql = ''): Query
    {
        return $this->innerService->createQuery($dql);
    }

    public function createNativeQuery(/*string*/ $sql, ResultSetMapping $rsm): NativeQuery
    {
        return $this->innerService->createNativeQuery($sql, $rsm);
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->innerService->createQueryBuilder();
    }

    public function getReference(/*string*/ $entityName, mixed $id): ?object
    {
        return $this->innerService->getReference($entityName, $id);
    }

    public function close(): void
    {
        $this->innerService->close();
    }

    public function lock(/*object*/ $entity, /*LockMode|int*/ $lockMode, /*DateTimeInterface|int|null*/ $lockVersion = null): void
    {
        $this->innerService->lock($entity, $lockMode, $lockVersion);
    }

    public function getEventManager(): EventManager
    {
        return $this->innerService->getEventManager();
    }

    public function getConfiguration(): Configuration
    {
        return $this->innerService->getConfiguration();
    }

    public function isOpen(): bool
    {
        return $this->innerService->isOpen();
    }

    public function getUnitOfWork(): UnitOfWork
    {
        return $this->innerService->getUnitOfWork();
    }

    public function newHydrator(/*string|int*/ $hydrationMode): AbstractHydrator
    {
        return $this->innerService->newHydrator($hydrationMode);
    }

    public function getProxyFactory(): ProxyFactory
    {
        return $this->innerService->getProxyFactory();
    }

    public function getFilters(): FilterCollection
    {
        return $this->innerService->getFilters();
    }

    public function isFiltersStateClean(): bool
    {
        return $this->innerService->isFiltersStateClean();
    }

    public function hasFilters(): bool
    {
        return $this->innerService->hasFilters();
    }

    // pre doctrine/orm 3.x

    public function transactional($func)
    {
        return $this->innerService->transactional($func);
    }

    public function createNamedQuery($name)
    {
        return $this->innerService->createNamedQuery($name);
    }

    public function createNamedNativeQuery($name)
    {
        return $this->innerService->createNamedNativeQuery($name);
    }

    public function getPartialReference($entityName, $identifier)
    {
        return $this->innerService->getPartialReference($entityName, $identifier);
    }

    public function copy($entity, $deep = false)
    {
        return $this->innerService->copy($entity, $deep);
    }

    public function getHydrator($hydrationMode)
    {
        return $this->innerService->getHydrator($hydrationMode);
    }
}
