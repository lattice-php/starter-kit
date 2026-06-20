<?php
declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Models\TeamInvitation;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AcceptTeamInvitationRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invitation' => ['required', function (string $attribute, mixed $value, Closure $fail): void {
                $user = $this->user();

                if (! $value instanceof TeamInvitation || ! $user instanceof User || strtolower($value->email) !== strtolower($user->email)) {
                    $fail(__('This invitation was sent to a different email address.'));
                } elseif ($value->isAccepted()) {
                    $fail(__('This invitation has already been accepted.'));
                } elseif ($value->isExpired()) {
                    $fail(__('This invitation has expired.'));
                }
            }],
        ];
    }

    public function validationData(): array
    {
        return array_merge(parent::validationData(), [
            'invitation' => $this->route('invitation'),
        ]);
    }
}
