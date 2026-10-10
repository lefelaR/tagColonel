<?php

declare(strict_types=1);

namespace Application\Cart\UseCases;

use Application\Cart\DTOs\GetLiveCartsDTO;
use Application\Cart\DTOs\LiveCartDTO;
use Application\Cart\DTOs\LiveCartsResultDTO;
use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;

// SOLID DIP: reads through CartRepositoryInterface. Redis is selected in the provider.
final class GetLiveCartsUseCase
{
    public function __construct(private readonly CartRepositoryInterface $carts) {}

    public function execute(GetLiveCartsDTO $dto): LiveCartsResultDTO
    {
        $rows = $this->carts->findLiveByCourse(new CourseId($dto->courseId));
        $carts = [];

        foreach ($rows as $row) {
            $carts[] = new LiveCartDTO(
                $row['cart_id'],
                $row['latitude'],
                $row['longitude'],
                $row['speed'],
            );
        }

        return new LiveCartsResultDTO($carts);
    }
}
