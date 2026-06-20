<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Actions\Settings\SendVerificationEmailAction;
use App\Concerns\ResolvesCurrentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Component;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

#[AsForm('settings.profile')]
class ProfileSettingsForm extends FormDefinition
{
    use ResolvesCurrentUser;

    public function definition(Form $form, Request $request): Form
    {
        $user = $this->currentUser();

        return $form
            ->method(HttpMethod::Patch)
            ->schema([
                Grid::make('profile-fields')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name', 'Name')
                            ->value($user->name)
                            ->autoComplete('name')
                            ->placeholder('Full name')
                            ->required()
                            ->rules(['string', 'max:255']),
                        TextInput::make('email', 'Email address')
                            ->email()
                            ->value($user->email)
                            ->autoComplete('username')
                            ->placeholder('Email address')
                            ->required()
                            ->rules(['string', 'max:255', Rule::unique(User::class)->ignore($user->id)]),
                    ]),
                ...$this->verificationNotice($user, $request),
                Button::make('Save')->submit(),
            ])
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $user = $this->currentUser();

        $user->fill($this->validate($request));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $this->toast(Variant::Success, __('Profile updated.'))->toRoute('settings.edit');
    }

    /**
     * @return array<int, Component>
     */
    private function verificationNotice(User $user, Request $request): array
    {
        if ($user->hasVerifiedEmail()) {
            return [];
        }

        $components = [
            Stack::make('profile-verification-notice')
                ->direction('row')
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make('Your email address is unverified.'),
                    Action::use(SendVerificationEmailAction::class),
                ]),
        ];

        if ($request->session()->get('status') === 'verification-link-sent') {
            $components[] = Text::make('A new verification link has been sent to your email address.');
        }

        return $components;
    }
}
