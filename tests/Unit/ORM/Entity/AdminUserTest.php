<?php

declare(strict_types=1);

namespace Tests\Unit\ORM\Entity;

use App\ORM\Entity\AdminUser;
use PHPUnit\Framework\TestCase;

final class AdminUserTest extends TestCase
{
    /**
     * @test
     */
    public function testEncryptPassword(): void
    {
        $plain  = 'password';
        $hashed = AdminUser::encryptPassword($plain);

        $this->assertNotEquals($plain, $hashed);
        $this->assertEquals(60, strlen($hashed));
        $this->assertTrue(password_verify($plain, $hashed));
    }
}
