<?php

namespace App\Filament\Pages\Auth;

use App\Models\Experience;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Collection;

class EditProfile extends BaseEditProfile
{
    public Collection $experiences;

    protected static string $view = 'filament.pages.auth.edit-profile';

    protected ?string $heading = 'Profile Information';

    public function mount(): void
    {
        parent::mount();
        $this->experiences = $this->getUser()->experiences;
    }

    public function createAction(): Action
    {
        return Action::make('create')
            ->label(__('Add Work Experience'))
            ->color('primary')
            ->icon('heroicon-o-plus')
            ->form(self::experienceForm())
            ->action(function (array $data) {
                $experience = auth()->user()->experiences()->create([
                    'company_name'    => $data['company_name'],
                    'job_title'       => $data['job_title'],
                    'job_description' => $data['job_description'],
                    'start_date'      => $data['start_date'],
                    'end_date'        => $data['end_date'],
                ]);
                $this->experiences->prepend($experience);
                Notification::make()
                    ->title(__('Work experience added'))
                    ->success()
                    ->send();
            });
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->color('primary')
            ->icon('heroicon-o-pencil')
            ->fillForm(function (array $arguments) {
                return $this->experiences
                    ->first(fn (Experience $experience) => $experience->id === $arguments['experience'])
                    ->attributesToArray();
			})
            ->form(self::experienceForm())
            ->action(function (array $data, array $arguments) {
                Experience::where('id', $arguments['experience'])->update([
                    'company_name'    => $data['company_name'],
                    'job_title'       => $data['job_title'],
                    'job_description' => $data['job_description'],
                    'start_date'      => $data['start_date'],
                    'end_date'        => $data['end_date'],
                ]);

                Notification::make()
                    ->title(__('Work experience updated'))
                    ->success()
                    ->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->requiresConfirmation()
            ->action(fn (array $arguments) => Experience::where('id', '=', $arguments['experience'])->delete());
    }

    public function experienceForm(): array
    {
        return [
            TextInput::make('company_name')
                ->label(__('Company name'))
                ->required(),
            TextInput::make('job_title')
                ->label(__('Job title'))
                ->required(),
            TextInput::make('job_description')
                ->label(__('Job description'))
                ->required(),
            Grid::make()
                ->schema([
                    Datepicker::make('start_date')
                        ->label(__('Start date'))
                        ->required(),
                    Datepicker::make('end_date')
                        ->label(__('End date'))
                        ->after(fn (Get $get) => $get('start_date')),
                ]),
        ];
    }

    public function getHeading(): string
    {
        return __('Profile Information');
    }
}
