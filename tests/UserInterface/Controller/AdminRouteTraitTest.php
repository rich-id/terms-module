<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\UserInterface\Controller;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\ControllerTestCase;
use RichId\TermsModuleBundle\Tests\Resources\Controller\DummyAdminRoute;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @covers \RichId\TermsModuleBundle\UserInterface\Controller\AdminRouteTrait
 * @TestConfig("kernel")
 */
final class AdminRouteTraitTest extends ControllerTestCase
{
    /** @var DummyAdminRoute */
    public $route;

    protected function beforeTest(): void
    {
        $this->route = new DummyAdminRoute();
    }

    public function testBuildAccessDeniedExceptionWithoutRoles(): void
    {
        $exception = ($this->route)();

        $this->assertInstanceOf(AccessDeniedException::class, $exception);
        $this->assertSame('You must define one or more roles in the configuration.', $exception->getMessage());
    }

    public function testBuildAccessDeniedExceptionWithOneRole(): void
    {
        $this->route->setAdminRoles(['ROLE_ADMIN']);

        $exception = ($this->route)();

        $this->assertInstanceOf(AccessDeniedException::class, $exception);
        $this->assertSame('Only the "ROLE_ADMIN" role is allowed to access the back office.', $exception->getMessage());
    }

    public function testBuildAccessDeniedExceptionWithMultipleRole(): void
    {
        $this->route->setAdminRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        $exception = ($this->route)();

        $this->assertInstanceOf(AccessDeniedException::class, $exception);
        $this->assertSame('Only the "ROLE_ADMIN, ROLE_SUPER_ADMIN" roles are allowed to access the back office.', $exception->getMessage());
    }
}
