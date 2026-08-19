<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Actions\Settings\SendVerificationEmailAction;
use App\Concerns\ResolvesCurrentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lattice\Actions\Components\Action;
use Lattice\Facades\Effects;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\TextInput;
use Lattice\Form\FormDefinition;
use Lattice\Http\LatticeResponse;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\StackDirection;

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
                        TextInput::make('name', __('common.field.name'))
                            ->value($user->name)
                            ->autoComplete('name')
                            ->placeholder(__('common.placeholder.full-name'))
                            ->required()
                            ->rules(['string', 'max:255']),
                        TextInput::make('email', __('common.field.email-address'))
                            ->email()
                            ->value($user->email)
                            ->autoComplete('username')
                            ->placeholder(__('common.field.email-address'))
                            ->required()
                            ->rules(['string', 'max:255', Rule::unique(User::class)->ignore($user->id)]),
                    ]),
                ...$this->verificationNotice($user, $request),
                Button::make(__('common.action.save'))->submit(),
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

        return Effects::respond()->toast(__('settings.profile.updated'))->toRoute('settings.edit');
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
                ->direction(StackDirection::Row)
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make(__('settings.profile.unverified')),
                    Action::use(SendVerificationEmailAction::class),
                ]),
        ];

        if ($request->session()->get('status') === 'verification-link-sent') {
            $components[] = Text::make(__('settings.profile.verification-sent'));
        }

        return $components;
    }
}
