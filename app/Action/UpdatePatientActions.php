<?php

namespace App\Action;

use App\Models\Inventory;
use App\Models\InventoryOrder;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\Width;
use phpDocumentor\Reflection\Types\Void_;

class UpdatePatientActions
{
    public static function personalDetails(): Action
    {
        return self::saveAction(
            Action::make('edit_personal')
                ->label('Personal Details')
                ->modalHeading('Update Personal Details')
                ->icon('heroicon-s-user')
                ->fillForm(fn($record) => [
                    'name' => $record->name,
                    'address' => $record->address,
                    'contact_number' => $record->contact_number,
                    'age' => $record->age,
                    'sex' => $record->sex,
                ])
                ->form([
                    Wizard::make([
                        Step::make('Personal Details')
                            ->icon('heroicon-s-user')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('contact_number')
                                            ->prefix('+63')
                                            ->mask('9999999999')
                                            ->required(),
                                        TextInput::make('age')
                                            ->numeric()
                                            ->required(),
                                        Select::make('sex')
                                            ->options(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])
                                            ->native(false)
                                            ->required(),
                                        Textarea::make('address')
                                            ->required()
                                            ->columnSpanFull()
                                            ->rows(3)
                                            ->autosize(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Personal Details Updated',
            'Patient personal information has been updated successfully.'
        );
    }

    public static function followUpSchedule(): Action
    {
        return self::saveAction(
            Action::make('edit_schedule')
                ->label('Follow-up Schedule')
                ->modalHeading('Update Follow-up Schedule')
                ->modalWidth(Width::ExtraLarge)
                ->hidden(function ($record) {
                    if ($record->follow_up_on === null) {
                        return false; // Hide if null
                    }

                    $follow_up_on = Carbon::parse($record->follow_up_on)->startOfDay();

                    return $follow_up_on->isFuture(); // Hide if future
                })
                ->icon('heroicon-s-calendar')
                ->fillForm(fn($record) => [
                    'follow_up_on' => $record->follow_up_on,
                ])
                ->form([
                    Wizard::make([
                        Step::make('Follow-up Schedule')
                            ->icon('heroicon-s-calendar')
                            ->schema([
                                DatePicker::make('follow_up_on')
                                    ->displayFormat('F d, Y')
                                    ->closeOnDateSelection()
                                    ->minDate(now())
                                    ->required()
                                    ->hiddenLabel()
                                    ->disabledDates(function () {
                                        $disabledDates = [];
                                        $start = now()->startOfYear();
                                        $end = now()->addYears(2)->endOfYear();

                                        for ($date = $start; $date <= $end; $date->addDay()) {
                                            if ($date->isSunday()) {
                                                $disabledDates[] = $date->format('Y-m-d');
                                            }
                                        }

                                        // dd($disabledDates);
                                        return $disabledDates;
                                    })
                                    ->suffixIcon('heroicon-s-calendar')
                                    ->native(false),
                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Billing Information Updated',
            'Patient billing information has been updated successfully.'
        );
    }

    public static function clinicalAssessment(): Action
    {
        return self::saveAction(
            Action::make('edit_clinical')
                ->label('Clinical Assessment')
                ->modalHeading('Update Clinical Assessment')
                ->icon('heroicon-s-document-text')
                ->fillForm(fn($record) => [
                    'diagnosis' => $record->diagnosis,
                    'special_instructions' => $record->special_instructions,
                    'follow_up_on' => $record->follow_up_on,
                ])
                ->form([
                    Wizard::make([
                        Step::make('Clinical Assessment')
                            ->icon('heroicon-s-document-text')
                            ->schema([
                                Textarea::make('diagnosis')
                                    ->label('Diagnosis')
                                    ->rows(3)
                                    ->autosize()
                                    ->maxLength(500),
                                Textarea::make('special_instructions')
                                    ->label('Special Instructions')
                                    ->rows(3)
                                    ->autosize()
                                    ->maxLength(500),
                                // DatePicker::make('follow_up_on')
                                //     ->label('Follow-up Date')
                                //     ->native(false),
                            ]),
                    ])->columnSpanFull()
                        ->contained(false),
                ]),
            'Clinical Assessment Updated',
            'Patient clinical assessment has been updated successfully.'
        );
    }

    public static function opticalSpecs(): Action
    {

        $updateAmount = function ($get, $set) {
            $ids = [
                $get('frame_type'),
                $get('color'),
                $get('lens_supply'),
            ];

            $costs = Inventory::whereIn('id', array_filter($ids))
                ->pluck('cost_per_unit', 'id');

            $total = 0;
            foreach ($ids as $id) {
                $total += $costs[$id] ?? 0;
            }

            $deposit = round($total / 2) ?? 0;

            $set('amount', $total);
            $set('deposit', $deposit);
            $set('balance', $total - $deposit);
        };

        function belowContent($cat_name, Get $get)
        {
            $cost = Inventory::where('id', $get("{$cat_name}"))->value('cost_per_unit');
            return $cost > 0 ? '₱' . number_format($cost) . '.00' : '';
        }

        return Action::make('edit_optical')
            ->hidden(fn($record) => Order::where('patient_id', $record->id)->exists())
            ->label('Specifications & Billing')
            ->modalHeading('Update Optical Specs & Billing Info')
            ->modalWidth(Width::FiveExtraLarge)
            ->icon('heroicon-s-eye')
            ->form([
                Wizard::make([
                    Step::make('Optical Specifications & Billing Information')
                        ->icon('heroicon-s-eye')
                        ->columns(3)
                        ->schema([
                            Section::make()
                                ->columnSpan(2)
                                ->schema([
                                    Select::make('frame_type')
                                        ->label('Frame Type')
                                        ->options(
                                            Inventory::whereHas('category', fn($q) => $q->where('name', 'Frame Type'))
                                                ->pluck('name', 'id')
                                        )
                                        ->belowContent(fn(Get $get) => belowContent('frame_type', $get))
                                        ->live()
                                        ->afterStateUpdated($updateAmount)
                                        ->native(false),

                                    Select::make('color')
                                        ->label('Frame Color')
                                        ->options(
                                            Inventory::whereHas('category', fn($q) => $q->where('name', 'Frame Color'))
                                                ->pluck('name', 'id')
                                        )
                                        ->belowContent(fn(Get $get) => belowContent('color', $get))
                                        ->live()
                                        ->afterStateUpdated($updateAmount)
                                        ->native(false),

                                    Select::make('lens_supply')
                                        ->label('Lens Supply')
                                        ->options(
                                            Inventory::whereHas('category', fn($q) => $q->where('name', 'Lens Supply'))
                                                ->pluck('name', 'id')
                                        )
                                        ->belowContent(fn(Get $get) => belowContent('lens_supply', $get))
                                        ->live()
                                        ->afterStateUpdated($updateAmount)
                                        ->native(false),
                                ]),

                            Section::make()
                                ->inlineLabel()
                                ->schema([
                                    TextInput::make('amount')
                                        ->hiddenLabel()
                                        ->beforeLabel('Total Amount')
                                        ->prefix('₱')
                                        ->disabled()
                                        ->live()
                                        ->dehydrated()
                                        ->afterStateUpdated(function ($get, $set) {
                                            return $set('deposit', $get('amount') / 2);
                                        })
                                        ->required(),

                                    TextInput::make('deposit')
                                        ->hiddenLabel()
                                        ->beforeLabel('Deposit')
                                        ->numeric()
                                        ->live()
                                        ->afterStateUpdated(function ($get, $set) {
                                            return $set('balance', $get('amount') - $get('deposit'));
                                        })
                                        ->prefix('₱')
                                        ->required(),

                                    TextInput::make('balance')
                                        ->hiddenLabel()
                                        ->beforeLabel('Balance')
                                        ->numeric()
                                        ->prefix('₱')
                                        ->disabled()
                                        ->dehydrated(),
                                ])

                        ]),
                ])->columnSpanFull()
                    ->contained(false),
            ])->modalSubmitActionLabel('Save Changes')
            ->action(function (array $data, $record) {

                // dd($data);
    
                $order = Order::create([
                    'patient_id' => $record->id,
                    'order_number' => 'EQP' . random_int(100000, 999999),
                    'amount' => $data['amount'],
                    'deposit' => $data['deposit'],
                    'balance' => $data['balance'],
                ]);

                $frame = Inventory::where('id', $data['frame_type'])->select('cost_per_unit', 'name')->first();
                $color = Inventory::where('id', $data['color'])->select('cost_per_unit', 'name')->first();
                $lens = Inventory::where('id', $data['lens_supply'])->select('cost_per_unit', 'name')->first();

                InventoryOrder::create([
                    'inventory_id' => $data['frame_type'],
                    'order_id' => $order->id,
                    'name_on_purchase' => $frame->name,
                    'price_on_purchase' => $frame->cost_per_unit,
                ]);

                InventoryOrder::create([
                    'inventory_id' => $data['color'],
                    'order_id' => $order->id,
                    'name_on_purchase' => $color->name,
                    'price_on_purchase' => $color->cost_per_unit,
                ]);
                InventoryOrder::create([
                    'inventory_id' => $data['lens_supply'],
                    'order_id' => $order->id,
                    'name_on_purchase' => $lens->name,
                    'price_on_purchase' => $lens->cost_per_unit,
                ]);

                Inventory::where('id', $data['frame_type'])->decrement('stock');
                Inventory::where('id', $data['color'])->decrement('stock');
                Inventory::where('id', $data['lens_supply'])->decrement('stock');


                Notification::make()
                    ->success()
                    ->title('Optical Specifications Updated')
                    ->body('Patient optical specifications have been updated successfully.')
                    ->send();
            });

    }


    public static function saveAction(
        Action $action,
        string $notificationTitle = 'Updated Successfully',
        string $notificationBody = 'The record has been updated successfully.'
    ): Action {
        return $action
            ->modalSubmitActionLabel('Save Changes')
            ->action(function (array $data, $record) use ($notificationTitle, $notificationBody) {

                dd($data);
                $record->update($data);

                Notification::make()
                    ->success()
                    ->title($notificationTitle)
                    ->body($notificationBody)
                    ->send();
            });
    }

    /**
     * Get all update actions as an array
     */
    public static function all(): array
    {
        return [
            self::personalDetails(),
            self::followUpSchedule(),
            self::clinicalAssessment(),
            self::opticalSpecs(),
        ];
    }
}
