<?php

declare(strict_types=1);

namespace Presentation\Http\Requests;

use Application\Alert\DTOs\ListAlertsDTO;
use Illuminate\Foundation\Http\FormRequest;

final class ListAlertsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDto(): ListAlertsDTO
    {
        return new ListAlertsDTO($this->integer('course_id'));
    }
}
