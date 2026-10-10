<?php

declare(strict_types=1);

namespace Presentation\Http\Requests;

use Application\Cart\DTOs\IngestGpsDTO;
use Illuminate\Foundation\Http\FormRequest;

// HTTP validation only. Coordinate and speed rules mirror the value objects.
final class IngestGpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'integer', 'min:1'],
            'course_id' => ['required', 'integer', 'min:1'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'speed' => ['required', 'numeric', 'between:0,15'],
            'hole' => ['required', 'integer', 'between:1,18'],
        ];
    }

    public function toDto(): IngestGpsDTO
    {
        return new IngestGpsDTO(
            $this->integer('cart_id'),
            $this->integer('course_id'),
            $this->float('lat'),
            $this->float('lng'),
            $this->float('speed'),
            $this->integer('hole'),
        );
    }
}
