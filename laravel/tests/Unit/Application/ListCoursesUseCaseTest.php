<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use Application\Course\DTOs\ListCoursesDTO;
use Application\Course\UseCases\ListCoursesUseCase;
use Domain\Course\Models\Course;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryCourseRepository;

final class ListCoursesUseCaseTest extends TestCase
{
    public function test_lists_courses_in_id_order(): void
    {
        $courses = new InMemoryCourseRepository();
        $second = new Course(['name' => 'Links']);
        $second->id = 2;
        $first = new Course(['name' => 'Peninsula']);
        $first->id = 1;
        $courses->save($second);
        $courses->save($first);

        $result = (new ListCoursesUseCase($courses))->execute(new ListCoursesDTO());

        $this->assertSame([1, 2], array_map(static fn ($course) => $course->id, $result->courses));
        $this->assertSame('Peninsula', $result->courses[0]->name);
    }
}
