<?php

namespace App\Filament\Pages;

use App\Events\OnboardingCompleted;
use App\Models\OnboardingProgress;
use App\Models\Tenant;
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

/**
 * TenantOnboarding Page
 *
 * 6-step wizard for tenant onboarding after database provisioning.
 * Tracks progress in OnboardingProgress model and fires completion event.
 *
 * Steps:
 * 1. Company Information
 * 2. User Role & Team
 * 3. Primary Use Case
 * 4. SA Integrations
 * 5. POPIA Consent (mandatory compliance)
 * 6. Pricing Selection
 */
class TenantOnboarding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $slug = 'tenant-onboarding';

    protected string $view = 'filament.pages.tenant-onboarding';

    protected static ?string $title = 'Welcome to Commatix';

    protected static ?string $navigationLabel = 'Get Started';

    protected static ?int $navigationSort = 1;

    // Hide from navigation - onboarding is now handled outside Filament
    public static function shouldRegisterNavigation(): bool
    {
        // Always hide - we now use the standalone onboarding flow at /onboarding
        return false;
    }

    public ?OnboardingProgress $progress = null;

    public ?Tenant $tenant = null;

    public ?array $data = [];

    /**
     * Mount the page and check tenant provisioning status.
     */
    public function mount(): void
    {
        // Check if user has a tenant assigned
        if (! Auth::user()->tenant_id) {
            // User doesn't have a tenant yet - this is expected for new registrations
            // The onboarding wizard will create the tenant
            $this->tenant = null;

            return;
        }

        // Get current tenant
        $this->tenant = Tenant::find(Auth::user()->tenant_id);

        // Check if tenant is ready for onboarding
        if ($this->tenant && $this->tenant->onboarding_status === 'provisioning') {
            // Redirect to provisioning status page
            Notification::make()
                ->title('Your workspace is being set up')
                ->body('Please wait while we prepare your Commatix workspace...')
                ->info()
                ->send();

            // Redirect handled via abort with redirect
            abort(redirect()->route('filament.pages.provisioning-status'));
        }

        if ($this->tenant && $this->tenant->onboarding_status !== 'ready' && ! $this->tenant->onboarding_completed) {
            // Something went wrong, show error
            Notification::make()
                ->title('Onboarding unavailable')
                ->body('There was an issue setting up your workspace. Please contact support.')
                ->danger()
                ->send();

            abort(500, 'Tenant not ready for onboarding');
        }

        // Get or create onboarding progress
        $this->progress = OnboardingProgress::firstOrCreate(
            ['tenant_id' => $this->tenant->id],
            [
                'current_step' => 1,
                'started_at' => now(),
            ]
        );

        // Load existing step data into form
        $this->form->fill([
            'step_1_data' => $this->progress->getStepData(1) ?? [],
            'step_2_data' => $this->progress->getStepData(2) ?? [],
            'step_3_data' => $this->progress->getStepData(3) ?? [],
            'step_4_data' => $this->progress->getStepData(4) ?? [],
            'step_5_data' => $this->progress->getStepData(5) ?? [],
            'step_6_data' => $this->progress->getStepData(6) ?? [],
        ]);
    }

    /**
     * Define the wizard form.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Wizard::make([
                    $this->companyInformationStep(),
                    $this->userRoleTeamStep(),
                    $this->primaryUseCaseStep(),
                    $this->saIntegrationsStep(),
                    $this->popiaConsentStep(),
                    $this->pricingSelectionStep(),
                ])
                    ->startOnStep($this->progress->current_step ?? 1)
                    ->persistStepInQueryString('step')
                    ->submitAction(view('filament.pages.onboarding.submit-button'))
                    ->nextAction(
                        fn ($action) => $action
                            ->label('Next Step')
                            ->icon('heroicon-m-arrow-right')
                            ->iconPosition('after')
                    )
                    ->previousAction(
                        fn ($action) => $action
                            ->label('Previous')
                            ->icon('heroicon-m-arrow-left')
                    )
                    ->skippable(false)
                    ->contained(false),
            ])
            ->statePath('data');
    }

    /**
     * Step 1: Company Information
     */
    protected function companyInformationStep(): Step
    {
        return Step::make('company_info')
            ->label('Company Information')
            ->description('Tell us about your organization')
            ->icon('heroicon-o-building-office-2')
            ->schema([
                // Basic Information Section
                \Filament\Schemas\Components\Section::make('Basic Information')
                    ->description('Core details about your company')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('step_1_data.company_name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Acme (Pty) Ltd')
                            ->helperText('Your official registered company name')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.trading_name')
                            ->label('Trading Name (if different)')
                            ->maxLength(255)
                            ->placeholder('Acme Solutions')
                            ->helperText('The name you trade under (optional)')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.company_registration_number')
                            ->label('Company Registration Number')
                            ->required()
                            ->placeholder('2019/123456/07')
                            ->helperText('Format: YYYY/NNNNNN/NN')
                            ->regex('/^\d{4}\/\d{6}\/\d{2}$/')
                            ->validationMessages([
                                'regex' => 'Registration number must be in format: YYYY/NNNNNN/NN (e.g., 2019/123456/07)',
                            ])
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.vat_number')
                            ->label('VAT Number')
                            ->placeholder('4123456789')
                            ->helperText('10-digit VAT number (optional)')
                            ->regex('/^\d{10}$/')
                            ->validationMessages([
                                'regex' => 'VAT number must be exactly 10 digits',
                            ])
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.tax_reference_number')
                            ->label('Tax Reference Number')
                            ->placeholder('9876543210')
                            ->helperText('SARS tax reference number (optional)')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Company Classification Section
                \Filament\Schemas\Components\Section::make('Company Classification')
                    ->description('Help us understand your business')
                    ->schema([
                        \Filament\Forms\Components\Select::make('step_1_data.industry')
                            ->label('Industry')
                            ->required()
                            ->options([
                                'financial_services' => 'Financial Services',
                                'professional_services' => 'Professional Services',
                                'retail' => 'Retail',
                                'manufacturing' => 'Manufacturing',
                                'healthcare' => 'Healthcare',
                                'technology' => 'Technology',
                                'construction' => 'Construction',
                                'education' => 'Education',
                                'hospitality' => 'Hospitality',
                                'real_estate' => 'Real Estate',
                                'other' => 'Other',
                            ])
                            ->searchable()
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('step_1_data.company_type')
                            ->label('Company Type')
                            ->required()
                            ->options([
                                'pty_ltd' => '(Pty) Ltd - Private Company',
                                'public' => 'Public Company',
                                'close_corp' => 'Close Corporation (CC)',
                                'partnership' => 'Partnership',
                                'sole_prop' => 'Sole Proprietor',
                                'npo' => 'Non-Profit Organization (NPO)',
                                'trust' => 'Trust',
                            ])
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('step_1_data.bee_level')
                            ->label('B-BBEE Level')
                            ->options([
                                '1' => 'Level 1 (135% Procurement Recognition)',
                                '2' => 'Level 2 (125% Procurement Recognition)',
                                '3' => 'Level 3 (110% Procurement Recognition)',
                                '4' => 'Level 4 (100% Procurement Recognition)',
                                '5' => 'Level 5 (80% Procurement Recognition)',
                                '6' => 'Level 6 (60% Procurement Recognition)',
                                '7' => 'Level 7 (50% Procurement Recognition)',
                                '8' => 'Level 8 (10% Procurement Recognition)',
                                'non-compliant' => 'Non-Compliant (0%)',
                            ])
                            ->helperText('Your current B-BBEE contributor level (optional)')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('step_1_data.company_size')
                            ->label('Company Size')
                            ->required()
                            ->options([
                                '1-5' => '1-5 employees',
                                '6-10' => '6-10 employees',
                                '11-25' => '11-25 employees',
                                '26-50' => '26-50 employees',
                                '50+' => '50+ employees',
                            ])
                            ->helperText('How many people work at your company?')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Contact Information Section
                \Filament\Schemas\Components\Section::make('Contact Information')
                    ->description('Primary contact details')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('step_1_data.primary_contact_person')
                            ->label('Primary Contact Person')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Smith')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.primary_email')
                            ->label('Primary Email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->placeholder('john@company.co.za')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.primary_phone')
                            ->label('Primary Phone')
                            ->required()
                            ->tel()
                            ->placeholder('+27 12 345 6789')
                            ->helperText('Format: +27 XX XXX XXXX')
                            ->regex('/^\+27\s?\d{2}\s?\d{3}\s?\d{4}$/')
                            ->validationMessages([
                                'regex' => 'Phone number must be in format: +27 XX XXX XXXX',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Physical Address Section
                \Filament\Schemas\Components\Section::make('Physical Address')
                    ->description('Your business location')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('step_1_data.physical_address_line1')
                            ->label('Address Line 1')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('123 Main Street')
                            ->columnSpan(2),

                        \Filament\Forms\Components\TextInput::make('step_1_data.physical_address_line2')
                            ->label('Address Line 2')
                            ->maxLength(255)
                            ->placeholder('Unit 4B')
                            ->columnSpan(2),

                        \Filament\Forms\Components\TextInput::make('step_1_data.physical_city')
                            ->label('City')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Johannesburg')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('step_1_data.physical_province')
                            ->label('Province')
                            ->required()
                            ->options(fn () => $this->getSAProvinces())
                            ->searchable()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.physical_postal_code')
                            ->label('Postal Code')
                            ->required()
                            ->placeholder('2000')
                            ->helperText('4-digit postal code')
                            ->regex('/^\d{4}$/')
                            ->validationMessages([
                                'regex' => 'Postal code must be exactly 4 digits',
                            ])
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Postal Address Section
                \Filament\Schemas\Components\Section::make('Postal Address')
                    ->description('Where should we send mail?')
                    ->schema([
                        \Filament\Forms\Components\Checkbox::make('step_1_data.same_as_physical')
                            ->label('Same as physical address')
                            ->live()
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set, \Filament\Schemas\Components\Utilities\Get $get) {
                                if ($state) {
                                    // Copy physical address to postal address
                                    $set('step_1_data.postal_address_line1', $get('step_1_data.physical_address_line1'));
                                    $set('step_1_data.postal_address_line2', $get('step_1_data.physical_address_line2'));
                                    $set('step_1_data.postal_city', $get('step_1_data.physical_city'));
                                    $set('step_1_data.postal_province', $get('step_1_data.physical_province'));
                                    $set('step_1_data.postal_code', $get('step_1_data.physical_postal_code'));
                                }
                            })
                            ->columnSpanFull(),

                        \Filament\Forms\Components\TextInput::make('step_1_data.postal_address_line1')
                            ->label('Address Line 1')
                            ->maxLength(255)
                            ->placeholder('PO Box 12345')
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_1_data.same_as_physical'))
                            ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_1_data.same_as_physical'))
                            ->columnSpan(2),

                        \Filament\Forms\Components\TextInput::make('step_1_data.postal_address_line2')
                            ->label('Address Line 2')
                            ->maxLength(255)
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_1_data.same_as_physical'))
                            ->columnSpan(2),

                        \Filament\Forms\Components\TextInput::make('step_1_data.postal_city')
                            ->label('City')
                            ->maxLength(255)
                            ->placeholder('Johannesburg')
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_1_data.same_as_physical'))
                            ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_1_data.same_as_physical'))
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('step_1_data.postal_province')
                            ->label('Province')
                            ->options(fn () => $this->getSAProvinces())
                            ->searchable()
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_1_data.same_as_physical'))
                            ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_1_data.same_as_physical'))
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_1_data.postal_code')
                            ->label('Postal Code')
                            ->placeholder('2000')
                            ->regex('/^\d{4}$/')
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_1_data.same_as_physical'))
                            ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_1_data.same_as_physical'))
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->afterValidation(function () {
                $this->saveStepProgress(1);
            });
    }

    /**
     * Step 2: User Role & Team
     */
    protected function userRoleTeamStep(): Step
    {
        return Step::make('user_role_team')
            ->label('Your Role & Team')
            ->description('How will you use Commatix?')
            ->icon('heroicon-o-user-group')
            ->schema([
                // Your Role Section
                \Filament\Schemas\Components\Section::make('Your Role')
                    ->description('Tell us about your position')
                    ->schema([
                        \Filament\Forms\Components\Select::make('step_2_data.role')
                            ->label('Role in Company')
                            ->required()
                            ->options([
                                'owner_founder' => 'Owner / Founder',
                                'ceo_md' => 'CEO / Managing Director',
                                'director' => 'Director',
                                'manager' => 'Manager',
                                'administrator' => 'Administrator',
                                'team_member' => 'Team Member',
                                'other' => 'Other',
                            ])
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('step_2_data.job_title')
                            ->label('Job Title (Optional)')
                            ->maxLength(255)
                            ->placeholder('e.g., Operations Manager')
                            ->helperText('Your specific job title within the company')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // Team Structure Section
                \Filament\Schemas\Components\Section::make('Team Structure')
                    ->description('Help us understand your team')
                    ->schema([
                        \Filament\Forms\Components\Select::make('step_2_data.team_size')
                            ->label('Team Size')
                            ->required()
                            ->options([
                                'just_me' => 'Just me',
                                '2-5' => '2-5 people',
                                '6-10' => '6-10 people',
                                '11-25' => '11-25 people',
                                '26-50' => '26-50 people',
                                '50+' => '50+ people',
                            ])
                            ->helperText('How many people work at your company?')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Toggle::make('step_2_data.has_divisions')
                            ->label('Do you have departments or divisions?')
                            ->helperText('Different teams or business units within your company')
                            ->live()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('step_2_data.divisions')
                            ->label('Departments / Divisions')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label('Division Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., Sales, Operations, Finance')
                                    ->distinct()
                                    ->columnSpanFull(),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_2_data.has_divisions'))
                            ->minItems(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_2_data.has_divisions') ? 1 : 0)
                            ->maxItems(10)
                            ->helperText('Add your company\'s departments or divisions (max 10)')
                            ->columnSpanFull()
                            ->defaultItems(0),
                    ])
                    ->columnSpanFull(),

                // Team Members Section
                \Filament\Schemas\Components\Section::make('Invite Team Members')
                    ->description('Invite colleagues to join your workspace (optional)')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('step_2_data.invite_team_now')
                            ->label('Invite team members now?')
                            ->helperText('You can always invite team members later from your dashboard')
                            ->live()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Repeater::make('step_2_data.team_invites')
                            ->label('Team Member Emails')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('colleague@company.co.za')
                                    ->distinct()
                                    ->columnSpan(2),

                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label('Name (Optional)')
                                    ->maxLength(255)
                                    ->placeholder('John Smith')
                                    ->columnSpan(1),
                            ])
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['email'] ?? 'New invite')
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_2_data.invite_team_now'))
                            ->minItems(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_2_data.invite_team_now') ? 1 : 0)
                            ->maxItems(20)
                            ->helperText('Enter email addresses of team members to invite (max 20)')
                            ->columns(3)
                            ->columnSpanFull()
                            ->defaultItems(0),

                        \Filament\Forms\Components\Placeholder::make('invite_note')
                            ->content('💡 Invited team members will receive an email to join your Commatix workspace')
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_2_data.invite_team_now'))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->afterValidation(function () {
                $this->saveStepProgress(2);
            });
    }

    /**
     * Step 3: Primary Use Case
     */
    protected function primaryUseCaseStep(): Step
    {
        return Step::make('primary_use_case')
            ->label('Primary Use Case')
            ->description('What do you want to achieve?')
            ->icon('heroicon-o-rocket-launch')
            ->schema([
                // Primary Use Case Selection Section
                \Filament\Schemas\Components\Section::make('What will you use Commatix for?')
                    ->description('Select your primary use case to get started with industry-specific templates')
                    ->schema([
                        \Filament\Forms\Components\Radio::make('step_3_data.use_case')
                            ->label('Primary Use Case')
                            ->required()
                            ->options([
                                'email_marketing' => 'Email Marketing',
                                'sms_campaigns' => 'SMS Campaigns',
                                'workflow_automation' => 'Workflow Automation',
                                'task_management' => 'Task Management',
                                'multi_channel' => 'Multi-Channel Communication',
                            ])
                            ->descriptions([
                                'email_marketing' => 'Create, send, and track email campaigns to your subscribers with advanced analytics.',
                                'sms_campaigns' => 'Send bulk SMS messages to your contacts with delivery tracking and response management.',
                                'workflow_automation' => 'Automate business processes with customizable workflows, milestones, and approvals.',
                                'task_management' => 'Organize, assign, and track tasks across your team with deadlines and priorities.',
                                'multi_channel' => 'Combine email, SMS, and workflow automation for comprehensive communication management.',
                            ])
                            ->live()
                            ->columnSpanFull(),

                        // Feature Preview Section (reactive based on selection)
                        \Filament\Forms\Components\Placeholder::make('email_marketing_features')
                            ->label('What you\'ll get:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Pre-built email campaign templates</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Subscriber list management with segmentation</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Email analytics and performance tracking</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Automated follow-up sequences</span>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_3_data.use_case') !== 'email_marketing')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('sms_campaigns_features')
                            ->label('What you\'ll get:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Bulk SMS sending with Vonage integration</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Contact management with mobile numbers</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Delivery tracking and response management</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>SMS credit management and top-ups</span>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_3_data.use_case') !== 'sms_campaigns')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('workflow_automation_features')
                            ->label('What you\'ll get:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Industry-specific workflow templates</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Milestone tracking with approval workflows</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Document management and attachments</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Automated notifications and reminders</span>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_3_data.use_case') !== 'workflow_automation')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('task_management_features')
                            ->label('What you\'ll get:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Task creation and assignment system</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Priority levels and deadline tracking</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Team collaboration and comments</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Progress tracking and reporting</span>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_3_data.use_case') !== 'task_management')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('multi_channel_features')
                            ->label('What you\'ll get:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>All features from email, SMS, workflows, and tasks</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Comprehensive communication management dashboard</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Cross-channel campaign coordination</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-success-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Unified analytics and reporting</span>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_3_data.use_case') !== 'multi_channel')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Additional Preferences Section
                \Filament\Schemas\Components\Section::make('Additional Preferences')
                    ->description('Help us personalize your experience')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('step_3_data.include_sample_data')
                            ->label('Include sample data')
                            ->helperText('We\'ll add example campaigns, workflows, and tasks to help you get started')
                            ->default(true)
                            ->inline(false)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Textarea::make('step_3_data.specific_goals')
                            ->label('What are your specific goals? (Optional)')
                            ->placeholder('Tell us about your business goals or specific challenges you want to solve...')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('This helps us provide better guidance and recommendations')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->afterValidation(function () {
                $this->saveStepProgress(3);
            });
    }

    /**
     * Step 4: SA Integrations
     */
    protected function saIntegrationsStep(): Step
    {
        return Step::make('sa_integrations')
            ->label('SA Integrations')
            ->description('Connect your business tools')
            ->icon('heroicon-o-link')
            ->schema([
                // Integration Introduction
                \Filament\Schemas\Components\Section::make('Connect Your South African Business Tools')
                    ->description('Integrate with popular South African payment, accounting, and banking services (optional)')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('integrations_intro')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="text-sm text-gray-600">
                                    <p>Connect Commatix with the tools you already use. You can set these up now or configure them later from your dashboard.</p>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Payment Gateway Section
                \Filament\Schemas\Components\Section::make('Payment Gateways')
                    ->description('Accept payments from your customers')
                    ->schema([
                        \Filament\Forms\Components\CheckboxList::make('step_4_data.payment_gateways')
                            ->label('Select Payment Providers')
                            ->options([
                                'payfast' => 'PayFast - South Africa\'s leading payment gateway',
                                'yoco' => 'Yoco - Card payments for SMEs',
                                'ozow' => 'Ozow - Instant EFT payments',
                                'peach_payments' => 'Peach Payments - Enterprise payment processing',
                            ])
                            ->descriptions([
                                'payfast' => 'Accept credit cards, instant EFT, and more with PayFast',
                                'yoco' => 'Perfect for retail and small businesses with card readers',
                                'ozow' => 'Real-time bank transfers with instant verification',
                                'peach_payments' => 'Advanced payment processing with fraud protection',
                            ])
                            ->helperText('Select the payment gateways you use or plan to use')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Toggle::make('step_4_data.configure_payment_now')
                            ->label('I want to configure payment credentials now')
                            ->helperText('You can also configure these later in Settings')
                            ->live()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('payment_config_note')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-blue-800">
                                            <strong>Note:</strong> Payment gateway configuration requires API keys from your payment provider. You can skip this step and configure it later from your dashboard settings.
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_4_data.configure_payment_now'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Accounting Software Section
                \Filament\Schemas\Components\Section::make('Accounting Software')
                    ->description('Sync with your accounting system')
                    ->schema([
                        \Filament\Forms\Components\Radio::make('step_4_data.accounting_software')
                            ->label('Which accounting software do you use?')
                            ->options([
                                'none' => 'None / Not applicable',
                                'sage' => 'Sage Business Cloud / Sage One',
                                'xero' => 'Xero',
                                'quickbooks' => 'QuickBooks',
                                'pastel' => 'Pastel Accounting',
                                'other' => 'Other',
                            ])
                            ->descriptions([
                                'none' => 'I don\'t use accounting software',
                                'sage' => 'Popular in South Africa for SMEs',
                                'xero' => 'Cloud-based accounting platform',
                                'quickbooks' => 'QuickBooks Online',
                                'pastel' => 'South African accounting software',
                                'other' => 'Another accounting system',
                            ])
                            ->live()
                            ->default('none')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\TextInput::make('step_4_data.other_accounting_software')
                            ->label('Please specify')
                            ->placeholder('e.g., MYOB, FreshBooks, etc.')
                            ->maxLength(255)
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_4_data.accounting_software') !== 'other')
                            ->required(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_4_data.accounting_software') === 'other')
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('accounting_integration_note')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-green-800">
                                            <strong>Great choice!</strong> You\'ll be able to connect your accounting software from the Integrations section in your dashboard after onboarding.
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => in_array($get('step_4_data.accounting_software'), ['none', null]))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Banking Integration Section
                \Filament\Schemas\Components\Section::make('Banking Integration')
                    ->description('Connect to South African banks (optional)')
                    ->schema([
                        \Filament\Forms\Components\Select::make('step_4_data.primary_bank')
                            ->label('Primary Bank')
                            ->options([
                                'none' => 'Not now',
                                'absa' => 'Absa',
                                'standard_bank' => 'Standard Bank',
                                'fnb' => 'FNB (First National Bank)',
                                'nedbank' => 'Nedbank',
                                'capitec' => 'Capitec',
                                'investec' => 'Investec',
                                'discovery_bank' => 'Discovery Bank',
                                'tymebank' => 'TymeBank',
                                'other' => 'Other',
                            ])
                            ->helperText('We\'ll help you set up bank reconciliation and statements sync')
                            ->default('none')
                            ->searchable()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('banking_api_note')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-amber-800">
                                            <strong>Coming soon:</strong> Direct banking integration via Open Banking APIs. For now, you can manually import bank statements for reconciliation.
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),

                // Other Integrations Section
                \Filament\Schemas\Components\Section::make('Other Integrations')
                    ->description('Additional services you might use')
                    ->schema([
                        \Filament\Forms\Components\CheckboxList::make('step_4_data.other_integrations')
                            ->label('Select any that apply')
                            ->options([
                                'google_workspace' => 'Google Workspace (Gmail, Calendar, Drive)',
                                'microsoft_365' => 'Microsoft 365 (Outlook, Teams, OneDrive)',
                                'slack' => 'Slack',
                                'zapier' => 'Zapier (for custom integrations)',
                                'whatsapp_business' => 'WhatsApp Business API',
                            ])
                            ->helperText('These integrations can be configured later from your dashboard')
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),

                // Skip All Section
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('skip_integrations')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="text-center py-4">
                                    <p class="text-sm text-gray-600">
                                        Not ready to set up integrations? No problem! You can configure all of these later from your dashboard.
                                    </p>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->afterValidation(function () {
                $this->saveStepProgress(4);
            });
    }

    /**
     * Step 5: POPIA Consent (Mandatory)
     */
    protected function popiaConsentStep(): Step
    {
        return Step::make('popia_consent')
            ->label('POPIA Consent')
            ->description('Data protection compliance')
            ->icon('heroicon-o-shield-check')
            ->schema([
                // POPIA Introduction Section
                \Filament\Schemas\Components\Section::make('Your Data Protection Rights')
                    ->description('We are committed to protecting your personal information in accordance with South African law')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('popia_intro')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-4">
                                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex gap-3">
                                            <svg class="w-6 h-6 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="text-sm text-blue-900">
                                                <strong class="block mb-2">About POPIA (Protection of Personal Information Act)</strong>
                                                <p class="mb-2">POPIA is South Africa\'s data protection law that gives you control over your personal information. We need your consent to process your data and provide you with our services.</p>
                                                <p><strong>Your Rights:</strong> You can access, correct, or delete your personal information at any time. You can also withdraw your consent whenever you wish.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Mandatory Processing Consent Section
                \Filament\Schemas\Components\Section::make('Required: Data Processing Consent')
                    ->description('This consent is mandatory to use Commatix')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('processing_consent_text')
                            ->label('What we will do with your information:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 leading-relaxed">
                                    <p class="mb-3">'.\App\Models\ConsentRecord::getConsentText(\App\Models\ConsentRecord::TYPE_PROCESSING).'</p>
                                    <div class="mt-4 pt-4 border-t border-gray-300">
                                        <strong class="block mb-2">What information we collect:</strong>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            <li>Company and contact information</li>
                                            <li>User account details (name, email, phone)</li>
                                            <li>Usage data and activity logs</li>
                                            <li>Communication preferences</li>
                                            <li>Billing and payment information</li>
                                        </ul>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-gray-300">
                                        <strong class="block mb-2">How we protect your data:</strong>
                                        <ul class="list-disc list-inside space-y-1 ml-2">
                                            <li>All data stored securely in South Africa</li>
                                            <li>Encrypted transmission and storage</li>
                                            <li>Regular security audits</li>
                                            <li>Strict access controls</li>
                                            <li>POPIA-compliant data retention policies</li>
                                        </ul>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Checkbox::make('step_5_data.processing_consent')
                            ->label('I consent to data processing (Required)')
                            ->helperText('You must consent to data processing to use Commatix')
                            ->required()
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'You must consent to data processing to continue.',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Optional Marketing Consent Section
                \Filament\Schemas\Components\Section::make('Optional: Marketing Communications')
                    ->description('Stay informed about Commatix updates and offers')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('marketing_consent_text')
                            ->label('Marketing communications include:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                        <li>Product updates and new features</li>
                                        <li>Tips and best practices</li>
                                        <li>Special offers and promotions</li>
                                        <li>Newsletters and industry insights</li>
                                        <li>Webinar and event invitations</li>
                                    </ul>
                                    <p class="mt-3 text-xs text-gray-600 italic">You can unsubscribe at any time via the link in our emails or by replying STOP to SMS messages.</p>
                                </div>
                            '))
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Checkbox::make('step_5_data.marketing_consent')
                            ->label('I consent to receiving marketing communications (Optional)')
                            ->helperText('We\'ll send you helpful updates, tips, and occasional offers')
                            ->default(false)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\CheckboxList::make('step_5_data.marketing_channels')
                            ->label('Preferred communication channels')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'in_app' => 'In-app notifications',
                            ])
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => ! $get('step_5_data.marketing_consent'))
                            ->default(['email', 'in_app'])
                            ->helperText('Select how you\'d like to receive marketing communications')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Optional Profiling Consent Section
                \Filament\Schemas\Components\Section::make('Optional: Personalization & Analytics')
                    ->description('Help us improve your experience')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('profiling_consent_text')
                            ->label('What this enables:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                                        <li>Personalized dashboard and recommendations</li>
                                        <li>Usage analytics to improve features</li>
                                        <li>Customized workflow suggestions based on your industry</li>
                                        <li>Performance insights and optimization tips</li>
                                    </ul>
                                    <p class="mt-3 text-xs text-gray-600 italic">All profiling is done securely within Commatix. We never share your behavioral data with third parties.</p>
                                </div>
                            '))
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Checkbox::make('step_5_data.profiling_consent')
                            ->label('I consent to personalization and analytics (Optional)')
                            ->helperText('This helps us provide you with a better, more tailored experience')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),

                // Optional Third Party Sharing Consent Section
                \Filament\Schemas\Components\Section::make('Optional: Third-Party Service Providers')
                    ->description('For enhanced functionality and integrations')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('third_party_consent_text')
                            ->label('Why we share data with third parties:')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-700 mb-3">To provide you with the best service, we work with trusted third-party providers:</p>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700 mb-3">
                                        <li><strong>Payment processors</strong> (PayFast, Yoco) - for secure payments</li>
                                        <li><strong>Email service</strong> (Resend) - for sending emails</li>
                                        <li><strong>SMS service</strong> (Vonage) - for sending SMS messages</li>
                                        <li><strong>Cloud hosting</strong> - for secure data storage</li>
                                        <li><strong>Analytics tools</strong> - for service improvement</li>
                                    </ul>
                                    <p class="text-xs text-gray-600 italic">All third parties are contractually bound to protect your information and comply with POPIA. We only share the minimum data necessary for each service.</p>
                                </div>
                            '))
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Checkbox::make('step_5_data.third_party_consent')
                            ->label('I consent to sharing data with third-party service providers (Optional)')
                            ->helperText('Required for payment processing, email/SMS sending, and integrations')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),

                // Data Protection Summary Section
                \Filament\Schemas\Components\Section::make('Your Rights Summary')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('rights_summary')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-6 h-6 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-green-900">
                                            <strong class="block mb-2">Remember: You are in control</strong>
                                            <ul class="space-y-1">
                                                <li>✓ View and download your data at any time</li>
                                                <li>✓ Request corrections to your information</li>
                                                <li>✓ Delete your account and data</li>
                                                <li>✓ Withdraw consent at any time from Settings</li>
                                                <li>✓ Contact our Data Protection Officer: <a href="mailto:privacy@commatix.co.za" class="text-green-700 underline">privacy@commatix.co.za</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->afterValidation(function () {
                $this->saveStepProgress(5);
            });
    }

    /**
     * Step 6: Pricing Selection
     */
    protected function pricingSelectionStep(): Step
    {
        return Step::make('pricing_selection')
            ->label('Choose Your Plan')
            ->description('Select the right plan for you')
            ->icon('heroicon-o-currency-dollar')
            ->schema([
                // Pricing Introduction Section
                \Filament\Schemas\Components\Section::make('Choose Your Commatix Plan')
                    ->description('Select the plan that best fits your business needs. All plans include a 14-day free trial.')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('pricing_intro')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-blue-900">
                                            <strong class="block mb-1">14-Day Free Trial - No Credit Card Required</strong>
                                            <p>Try Commatix risk-free. Your trial starts immediately after completing onboarding. Cancel anytime.</p>
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Plan Selection Radio
                \Filament\Schemas\Components\Section::make('Select Your Plan')
                    ->schema([
                        \Filament\Forms\Components\Radio::make('step_6_data.selected_plan')
                            ->label('Choose a plan')
                            ->required()
                            ->options([
                                'starter' => 'Starter - Perfect for small teams',
                                'professional' => 'Professional - For growing businesses',
                                'enterprise' => 'Enterprise - Advanced features & support',
                            ])
                            ->descriptions([
                                'starter' => 'R 499/month + VAT | Up to 5 users | 1,000 emails/month | 500 SMS credits',
                                'professional' => 'R 1,499/month + VAT | Up to 20 users | 10,000 emails/month | 2,500 SMS credits',
                                'enterprise' => 'R 3,999/month + VAT | Unlimited users | 50,000 emails/month | 10,000 SMS credits',
                            ])
                            ->live()
                            ->default('professional')
                            ->columnSpanFull(),

                        // Starter Plan Details
                        \Filament\Forms\Components\Placeholder::make('starter_plan_details')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-6 bg-white border-2 border-commatix-300 rounded-lg">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">Starter Plan</h3>
                                            <p class="text-sm text-gray-600">Perfect for small teams getting started</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-commatix-600">R 499</div>
                                            <div class="text-sm text-gray-600">per month + VAT</div>
                                        </div>
                                    </div>

                                    <div class="space-y-3 mb-4">
                                        <h4 class="font-semibold text-gray-900">What\'s included:</h4>
                                        <ul class="space-y-2 text-sm">
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span><strong>Up to 5 users</strong></span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>1,000 emails per month</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>500 SMS credits per month</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>5 GB storage</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Basic workflow templates</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Email support (48h response)</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_6_data.selected_plan') !== 'starter')
                            ->columnSpanFull(),

                        // Professional Plan Details
                        \Filament\Forms\Components\Placeholder::make('professional_plan_details')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-6 bg-gradient-to-br from-commatix-50 to-white border-2 border-commatix-500 rounded-lg shadow-md">
                                    <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                        <span class="bg-commatix-500 text-white px-4 py-1 rounded-full text-xs font-bold">MOST POPULAR</span>
                                    </div>
                                    <div class="flex items-start justify-between mb-4 mt-2">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">Professional Plan</h3>
                                            <p class="text-sm text-gray-600">Best for growing businesses</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-commatix-600">R 1,499</div>
                                            <div class="text-sm text-gray-600">per month + VAT</div>
                                        </div>
                                    </div>

                                    <div class="space-y-3 mb-4">
                                        <h4 class="font-semibold text-gray-900">Everything in Starter, plus:</h4>
                                        <ul class="space-y-2 text-sm">
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span><strong>Up to 20 users</strong></span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>10,000 emails per month</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>2,500 SMS credits per month</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>25 GB storage</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Advanced workflow templates & automations</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Custom integrations (PayFast, Sage, Xero)</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Priority email & chat support (24h response)</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Advanced analytics & reporting</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_6_data.selected_plan') !== 'professional')
                            ->columnSpanFull(),

                        // Enterprise Plan Details
                        \Filament\Forms\Components\Placeholder::make('enterprise_plan_details')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-6 bg-gradient-to-br from-gray-900 to-gray-800 text-white border-2 border-sa-gold-500 rounded-lg shadow-lg">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-white">Enterprise Plan</h3>
                                            <p class="text-sm text-gray-300">For organizations requiring advanced features</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-3xl font-bold text-sa-gold-500">R 3,999</div>
                                            <div class="text-sm text-gray-300">per month + VAT</div>
                                        </div>
                                    </div>

                                    <div class="space-y-3 mb-4">
                                        <h4 class="font-semibold text-white">Everything in Professional, plus:</h4>
                                        <ul class="space-y-2 text-sm text-gray-100">
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span><strong>Unlimited users</strong></span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>50,000 emails per month</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>10,000 SMS credits per month</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>100 GB storage</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Dedicated account manager</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Custom workflow development</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>White-label options</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>24/7 priority phone & chat support (4h response)</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>SLA guarantee (99.9% uptime)</span>
                                            </li>
                                            <li class="flex items-start gap-2">
                                                <svg class="w-5 h-5 text-sa-gold-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Onboarding & training sessions</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_6_data.selected_plan') !== 'enterprise')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Billing Cycle Section
                \Filament\Schemas\Components\Section::make('Billing Preferences')
                    ->schema([
                        \Filament\Forms\Components\Radio::make('step_6_data.billing_cycle')
                            ->label('Billing Cycle')
                            ->required()
                            ->options([
                                'monthly' => 'Monthly - Pay as you go',
                                'annual' => 'Annual - Save 15% (2 months free)',
                            ])
                            ->descriptions([
                                'monthly' => 'Billed monthly, cancel anytime',
                                'annual' => 'Billed annually, save 15% compared to monthly billing',
                            ])
                            ->default('monthly')
                            ->live()
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Placeholder::make('annual_savings')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex gap-3">
                                        <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="text-sm text-green-900">
                                            <strong>Save 15% with annual billing!</strong>
                                            <p class="mt-1">Pay for 10 months, get 12 months of service. Cancel anytime and receive a prorated refund.</p>
                                        </div>
                                    </div>
                                </div>
                            '))
                            ->hidden(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('step_6_data.billing_cycle') !== 'annual')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                // Add-ons Section
                \Filament\Schemas\Components\Section::make('Optional Add-ons')
                    ->description('Need more resources? Add these anytime.')
                    ->schema([
                        \Filament\Forms\Components\CheckboxList::make('step_6_data.addons')
                            ->label('Select add-ons (optional)')
                            ->options([
                                'extra_users' => 'Additional users - R 99/user/month',
                                'extra_emails' => 'Extra email credits - R 0.10 per email',
                                'extra_sms' => 'Extra SMS credits - R 0.35 per SMS',
                                'extra_storage' => 'Additional storage - R 149 per 10GB/month',
                            ])
                            ->helperText('You can add or remove these at any time from your dashboard')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),

                // Trial & Payment Info
                \Filament\Schemas\Components\Section::make('Next Steps')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('trial_info')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="space-y-4">
                                    <div class="p-4 bg-commatix-50 border border-commatix-200 rounded-lg">
                                        <h4 class="font-semibold text-gray-900 mb-2">What happens after you complete setup?</h4>
                                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                                            <li>Your 14-day free trial starts immediately</li>
                                            <li>Explore all features with no limitations</li>
                                            <li>We\'ll send you a reminder 2 days before trial ends</li>
                                            <li>Add payment method before trial ends to continue service</li>
                                            <li>Cancel anytime - no questions asked</li>
                                        </ol>
                                    </div>

                                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                        <h4 class="font-semibold text-gray-900 mb-2">Accepted Payment Methods</h4>
                                        <div class="flex flex-wrap gap-3 items-center text-sm text-gray-600">
                                            <span>✓ Credit/Debit Cards (Visa, Mastercard)</span>
                                            <span>✓ EFT (via PayFast/Ozow)</span>
                                            <span>✓ Debit Orders</span>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-600">All prices exclude VAT (15%). Payment processed securely by PayFast.</p>
                                    </div>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->afterValidation(function () {
                $this->saveStepProgress(6);
            });
    }

    /**
     * Save progress for a specific step.
     */
    protected function saveStepProgress(int $step): void
    {
        $stepKey = "step_{$step}_data";
        $stepData = $this->data[$stepKey] ?? [];

        // Save step data to OnboardingProgress
        $this->progress->saveStepData($step, $stepData);

        // Mark step as completed
        $this->progress->completeStep($step);

        // Show success notification
        Notification::make()
            ->title("Step {$step} saved")
            ->body('Your progress has been saved.')
            ->success()
            ->send();
    }

    /**
     * Handle wizard completion (after step 6).
     */
    public function submit()
    {
        // Get all form data
        $data = $this->form->getState();

        // Mark onboarding as completed
        $this->progress->update([
            'completed_at' => now(),
        ]);

        // Update tenant record
        $this->tenant->update([
            'onboarding_completed' => true,
            'onboarding_status' => 'completed',
            'onboarding_completed_at' => now(),
            'selected_use_case' => $data['step_3_data']['use_case'] ?? null,
            'selected_integrations' => $data['step_4_data']['integrations'] ?? [],
        ]);

        // Clear wizard data from tenant (no longer needed)
        $this->tenant->update([
            'setup_wizard_data' => null,
        ]);

        // Fire OnboardingCompleted event for post-onboarding automation
        event(new OnboardingCompleted($this->tenant, $this->progress));

        // Show success notification
        Notification::make()
            ->title('Welcome to Commatix!')
            ->body('Your workspace is ready. Let\'s get started!')
            ->success()
            ->duration(5000)
            ->send();

        // Redirect to dashboard
        return redirect()->route('filament.pages.dashboard');
    }

    /**
     * Save & Exit functionality.
     */
    public function saveAndExit()
    {
        Notification::make()
            ->title('Progress saved')
            ->body('You can continue your onboarding anytime.')
            ->success()
            ->send();

        return redirect()->route('filament.pages.dashboard');
    }

    /**
     * Get the page heading.
     */
    public function getHeading(): string
    {
        return 'Welcome to Commatix';
    }

    /**
     * Get the page subheading.
     */
    public function getSubheading(): ?string
    {
        $completionPercentage = $this->progress?->getCompletionPercentage() ?? 0;

        return "Let's get your workspace set up. You're {$completionPercentage}% complete.";
    }

    /**
     * Get South African provinces from database.
     */
    protected function getSAProvinces(): array
    {
        return DB::table('sa_provinces')
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }
}
