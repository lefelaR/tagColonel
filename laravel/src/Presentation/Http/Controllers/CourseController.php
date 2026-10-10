<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers;

use Application\Course\DTOs\ListCoursesDTO;
use Application\Course\UseCases\ListCoursesUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Presentation\Http\Resources\CourseResource;

// Presentation only: DTO -> UseCase -> Resource. No Course::query() here.
final class CourseController
{
    public function index(ListCoursesUseCase $list): AnonymousResourceCollection
    {
        return CourseResource::collection($list->execute(new ListCoursesDTO())->courses);
    }
}
