<?php

namespace App\Filament\Resources\MessageTemplates\Schemas;

use Filament\Forms\Components;
use Filament\Schemas\Components as SchemaComponents;
use Filament\Schemas\Schema;

class MessageTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaComponents\Section::make('Template Details')
                    ->description('Basic information about this message template')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Welcome Email, Order Confirmation SMS')
                            ->helperText('A descriptive name for this template')
                            ->columnSpan(2),

                        Components\Textarea::make('description')
                            ->rows(2)
                            ->placeholder('Describe when and how this template should be used...')
                            ->columnSpanFull(),

                        Components\Select::make('channel')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'whatsapp' => 'WhatsApp',
                            ])
                            ->default('email')
                            ->required()
                            ->reactive()
                            ->helperText('Communication channel for this template'),

                        Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active templates can be used in campaigns')
                            ->inline(false),
                    ])
                    ->columns(2)
                    ->collapsible(),

                SchemaComponents\Section::make('Message Content')
                    ->description('Template content with variable placeholders')
                    ->icon('heroicon-o-pencil-square')
                    ->schema([
                        Components\TextInput::make('subject')
                            ->label('Subject Line')
                            ->maxLength(255)
                            ->placeholder('e.g., Welcome to {{company_name}}!')
                            ->helperText('For email templates only. Use {{variable}} for placeholders')
                            ->visible(fn ($get) => $get('channel') === 'email')
                            ->required(fn ($get) => $get('channel') === 'email')
                            ->columnSpanFull(),

                        Components\Textarea::make('content')
                            ->required()
                            ->rows(10)
                            ->placeholder("Example:\n\nHello {{first_name}},\n\nWelcome to {{company_name}}!\n\n{{custom_message}}\n\nBest regards,\n{{sender_name}}")
                            ->helperText('Use {{variable_name}} for dynamic content')
                            ->columnSpanFull(),

                        SchemaComponents\Grid::make(2)
                            ->schema([
                                Components\Placeholder::make('character_count')
                                    ->label('Character Count')
                                    ->content(fn ($get) => $get('content') ? strlen($get('content')) : 0),

                                Components\Placeholder::make('sms_parts')
                                    ->label('Estimated SMS Parts')
                                    ->content(fn ($get) => $get('content') && $get('channel') === 'sms'
                                        ? ceil(strlen($get('content')) / 160)
                                        : 'N/A')
                                    ->visible(fn ($get) => $get('channel') === 'sms'),
                            ]),
                    ])
                    ->collapsible(),

                SchemaComponents\Section::make('Template Variables')
                    ->description('Available variables that can be used in this template')
                    ->icon('heroicon-o-variable')
                    ->schema([
                        Components\TagsInput::make('variables')
                            ->placeholder('Add variable names (without {{}})')
                            ->helperText('Add custom variables like: first_name, company_name, order_id, etc.')
                            ->suggestions([
                                'first_name',
                                'last_name',
                                'email',
                                'phone',
                                'company_name',
                                'campaign_name',
                            ])
                            ->columnSpanFull(),

                        Components\Placeholder::make('common_variables')
                            ->label('Common Variables')
                            ->content(fn () => '{{first_name}}, {{last_name}}, {{email}}, {{phone}}, {{company_name}}, {{campaign_name}}')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                SchemaComponents\Section::make('Preview')
                    ->description('Preview how your template will look')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Components\Placeholder::make('preview')
                            ->label(fn ($get) => $get('channel') === 'email' ? 'Email Preview' : 'Message Preview')
                            ->content(function ($get) {
                                $content = $get('content') ?? '';
                                $subject = $get('subject') ?? '';

                                if ($get('channel') === 'email' && $subject) {
                                    return "Subject: {$subject}\n\n{$content}";
                                }

                                return $content ?: 'Enter content to see preview...';
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
