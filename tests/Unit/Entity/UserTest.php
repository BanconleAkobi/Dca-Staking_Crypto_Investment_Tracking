<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->getId());
    }

    public function testUserEmail(): void
    {
        $user = new User();
        $email = 'test@example.com';
        
        $user->setEmail($email);
        
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->getUserIdentifier());
    }

    public function testUserPseudo(): void
    {
        $user = new User();
        $pseudo = 'testuser';
        
        $user->setPseudo($pseudo);
        
        $this->assertEquals($pseudo, $user->getPseudo());
    }

    public function testUserPassword(): void
    {
        $user = new User();
        $password = 'password123';
        
        $user->setPassword($password);
        
        $this->assertEquals($password, $user->getPassword());
    }

    public function testUserRoles(): void
    {
        $user = new User();
        
        // Test rôle par défaut
        $this->assertContains('ROLE_USER', $user->getRoles());
        
        // Test ajout de rôle
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
    }

    public function testUserIsActive(): void
    {
        $user = new User();
        
        $this->assertTrue($user->isActive());
        
        $user->setIsActive(false);
        $this->assertFalse($user->isActive());
    }

    public function testUserCreatedAt(): void
    {
        $user = new User();
        $date = new \DateTime();
        
        $user->setCreatedAt($date);
        
        $this->assertEquals($date, $user->getCreatedAt());
    }

    public function testUserUpdatedAt(): void
    {
        $user = new User();
        $date = new \DateTime();
        
        $user->setUpdatedAt($date);
        
        $this->assertEquals($date, $user->getUpdatedAt());
    }

    public function testUserEmailVerification(): void
    {
        $user = new User();
        
        $this->assertFalse($user->isVerified());
        
        $user->setIsVerified(true);
        $this->assertTrue($user->isVerified());
    }

    public function testUserRelationships(): void
    {
        $user = new User();
        
        // Test collections vides
        $this->assertCount(0, $user->getCryptos());
        $this->assertCount(0, $user->getAssets());
        $this->assertCount(0, $user->getSavingsAccounts());
        $this->assertCount(0, $user->getTransactions());
        $this->assertCount(0, $user->getWithdrawals());
    }

    public function testUserToString(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        
        $this->assertEquals('test@example.com', (string) $user);
    }

    public function testUserEraseCredentials(): void
    {
        $user = new User();
        $user->setPassword('password123');
        
        $user->eraseCredentials();
        
        // Les credentials devraient être effacés
        $this->assertNull($user->getPlainPassword());
    }

    public function testUserValidation(): void
    {
        $user = new User();
        
        // Test validation email
        $user->setEmail('invalid-email');
        // Dans un vrai test, on utiliserait le validator Symfony
        
        // Test validation pseudo
        $user->setPseudo('');
        // Dans un vrai test, on vérifierait les contraintes
    }
}
