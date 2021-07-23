<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Fixtures;

use RichCongress\RecurrentFixturesTestBundle\DataFixture\AbstractFixture;
use RichId\TermsModuleBundle\Tests\Resources\Entity\DummyUser;

final class DummyUserFixtures extends AbstractFixture
{
    public const USER = '1';
    public const USER_2 = '2';
    public const USER_ADMIN = '3';

    protected function loadFixtures(): void
    {
        $this->createObject(
            DummyUser::class,
            self::USER,
            [
                'username' => 'my_user_1',
                'roles'    => ['ROLE_USER'],
            ]
        );

        $this->createObject(
            DummyUser::class,
            self::USER_2,
            [
                'username' => 'my_user_2',
                'roles'    => ['ROLE_USER'],
            ]
        );

        $this->createObject(
            DummyUser::class,
            self::USER_ADMIN,
            [
                'username' => 'my_user_3',
                'roles'    => ['ROLE_USER', 'ROLE_ADMIN'],
            ]
        );
    }
}
