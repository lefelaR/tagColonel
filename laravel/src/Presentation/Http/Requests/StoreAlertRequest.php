<?php

declare(strict_types=1);

namespace Presentation\Http\Requests;

use Application\Alert\DTOs\CreateAlertDTO;
use Domain\Round\ValueObjects\PaceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $statuses = array_map(static fn (PaceStatus $status): string => $status->value, PaceStatus::cases());

        return [
            'course_id' => ['required', 'integer', 'min:1'],
            'cart_id' => ['required', 'integer', 'min:1'],
            'round_id' => ['nullable', 'integer', 'min:1'],
            'pace_status' => ['required', 'string', Rule::in($statuses)],
            'message' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): CreateAlertDTO
    {
        $validated = $this->validated();
        $roundId = $validated['round_id'] ?? null;

        return new CreateAlertDTO(
            (int) $validated['course_id'],
            (int) $validated['cart_id'],
            $roundId === null ? null : (int) $roundId,
            $validated['pace_status'],
            $validated['message'] ?? null,
        );
    }
}
