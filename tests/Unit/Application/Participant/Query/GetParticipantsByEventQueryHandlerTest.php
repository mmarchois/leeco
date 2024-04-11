<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Participant\Query;

use App\Application\Participant\Query\GetParticipantsByEventQuery;
use App\Application\Participant\Query\GetParticipantsByEventQueryHandler;
use App\Application\Participant\View\ParticipantView;
use App\Domain\Pagination;
use App\Domain\Participant\Repository\ParticipantRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class GetParticipantsByEventQueryHandlerTest extends TestCase
{
    public function testParticipants(): void
    {
        $participantRepository = $this->createMock(ParticipantRepositoryInterface::class);
        $participants = [
            new ParticipantView(
                '33795ae7-395a-440a-9fa8-f72343d62eb0',
                'Mathieu',
                'MARCHOIS',
                'mathieu.marchois@gmail.com',
                'accessCode',
                true,
            ),
            new ParticipantView(
                'e8e165c3-64bf-46e5-b612-ce42c6096025',
                'Hélène',
                'MARCHOIS',
                'helene.m.maitre@gmail.com',
                'accessCode2',
                false,
            ),
        ];

        $participantRepository
            ->expects(self::once())
            ->method('findParticipantsByEvent')
            ->with('37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9', '69b88cdb-6783-4bca-b15c-ced4488e6a63', 20, 1)
            ->willReturn([
                'participants' => $participants,
                'count' => 2,
            ]);

        $query = new GetParticipantsByEventQuery(
            '37fa0f81-d9dd-4bbd-900b-9cc3b39d21e9',
            '69b88cdb-6783-4bca-b15c-ced4488e6a63',
            1,
            20,
        );
        $handler = new GetParticipantsByEventQueryHandler($participantRepository);

        $this->assertEquals(new Pagination($participants, 2, 1, 20), ($handler)($query));
    }
}
