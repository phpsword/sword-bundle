<?php

declare(strict_types=1);

namespace Sword\SwordBundle\Test\Security\Voter;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Sword\SwordBundle\Security\User;
use Sword\SwordBundle\Security\Voter\CapabilityVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CapabilityVoterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @dataProvider capabilities
     */
    public function testVote(string $capability, int $expected): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getCapabilities')
            ->once()
            ->andReturn([
                'edit_posts',
                'edit_pages',
                'create_posts',
                'delete_users',
                'manage_options',
            ]);

        $token = Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')
            ->withNoArgs()
            ->twice()
            ->andReturn($user);

        $voter = new CapabilityVoter();

        $this->assertSame($expected, $voter->vote($token, null, [$capability]));
    }

    public function testVoteUnauthenticatedUser(): void
    {
        $token = Mockery::mock(TokenInterface::class);
        $token->shouldReceive('getUser')
            ->withNoArgs()
            ->once()
            ->andReturn(null);

        $voter = new CapabilityVoter();

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($token, null, ['edit_posts']));
    }

    public function capabilities(): array
    {
        return [
            ['edit_posts', VoterInterface::ACCESS_GRANTED],
            ['edit_pages', VoterInterface::ACCESS_GRANTED],
            ['create_posts', VoterInterface::ACCESS_GRANTED],
            ['delete_users', VoterInterface::ACCESS_GRANTED],
            ['manage_options', VoterInterface::ACCESS_GRANTED],
            ['edit_products', VoterInterface::ACCESS_DENIED],
            ['', VoterInterface::ACCESS_DENIED],
        ];
    }
}
