<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkflowTemplate;
use App\Models\Milestone;
use App\Models\DocumentType;
use App\Models\AccessScope;
use App\Models\StatusType;
use App\Models\Industry;
use Illuminate\Support\Str;

class ComprehensiveWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creating comprehensive workflow templates with document requirements...');

        // Get required references
        $globalScope = AccessScope::where('name', 'global')->first();
        $industryScope = AccessScope::where('name', 'industry_template')->first();
        $defaultStatus = StatusType::first();

        // Define our 5 industry workflows with modern UX approach
        $workflows = [
            [
                'name' => 'FICA/KYC Client Onboarding',
                'description' => 'Comprehensive client onboarding workflow for financial services with FICA compliance and modern digital experience',
                'industry_category' => 'financial_services',
                'template_type' => 'system',
                'complexity_level' => 'complex',
                'estimated_duration_days' => 14,
                'access_scope_id' => $industryScope->id,
                'workflow_code' => 'FIN_FICA_KYC_ONBOARDING',
                'milestones' => [
                    [
                        'name' => '📋 Client Information Collection',
                        'description' => 'Digital collection of all required client information and documentation with real-time validation',
                        'sequence_order' => 1,
                        'estimated_duration_days' => 2,
                        'milestone_type' => 'documentation',
                        'priority' => 'high',
                        'requires_docs' => true,
                        'completion_criteria' => 'All FICA required documents submitted, validated, and digitally verified',
                        'documents' => [
                            'Identity Document' => [
                                'required' => true,
                                'instructions' => 'Upload clear, high-resolution copy of valid SA ID or passport. Document must be in color and all text clearly readable.',
                                'validation_rules' => 'file_type:pdf,jpg,png|max_size:5MB|min_resolution:300dpi'
                            ],
                            'Proof of Address' => [
                                'required' => true,
                                'instructions' => 'Municipal bill, bank statement, or lease agreement not older than 3 months. Address must match ID document.',
                                'validation_rules' => 'file_type:pdf,jpg,png|max_size:5MB|date_not_older_than:90_days'
                            ],
                            'Proof of Income' => [
                                'required' => true,
                                'instructions' => 'Latest salary slip, bank statement showing salary deposits, or employment letter with salary details.',
                                'validation_rules' => 'file_type:pdf,jpg,png|max_size:10MB'
                            ],
                            'Tax Clearance Certificate' => [
                                'required' => false,
                                'instructions' => 'SARS tax clearance certificate if available. Not mandatory but speeds up approval process.',
                                'validation_rules' => 'file_type:pdf|max_size:5MB'
                            ],
                        ]
                    ],
                    [
                        'name' => '🔍 AI-Powered Document Verification',
                        'description' => 'Automated verification of submitted documents using AI and official database checks',
                        'sequence_order' => 2,
                        'estimated_duration_days' => 3,
                        'milestone_type' => 'review',
                        'priority' => 'critical',
                        'requires_approval' => true,
                        'completion_criteria' => 'All documents AI-verified, cross-referenced with official databases, and manually reviewed where needed',
                        'documents' => [
                            'Verification Report' => [
                                'required' => true,
                                'instructions' => 'System-generated verification report with AI confidence scores and manual review notes.',
                                'validation_rules' => 'auto_generated:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '⚖️ Risk Assessment & Compliance',
                        'description' => 'Comprehensive risk assessment and regulatory compliance screening with real-time scoring',
                        'sequence_order' => 3,
                        'estimated_duration_days' => 2,
                        'milestone_type' => 'approval',
                        'priority' => 'high',
                        'requires_approval' => true,
                        'completion_criteria' => 'Risk assessment completed, compliance checks passed, and approval granted by authorized personnel',
                        'documents' => [
                            'Risk Assessment Report' => [
                                'required' => true,
                                'instructions' => 'Detailed risk evaluation including credit score, transaction patterns, and regulatory flags.',
                                'validation_rules' => 'auto_generated:true|requires_approval:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '🏦 Digital Account Setup',
                        'description' => 'Automated account creation with personalized service configuration and welcome experience',
                        'sequence_order' => 4,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'task',
                        'priority' => 'medium',
                        'completion_criteria' => 'Account created, services configured, and client onboarding portal activated',
                        'documents' => [
                            'Account Opening Form' => [
                                'required' => true,
                                'instructions' => 'Digital account opening form with e-signature capability. All fields must be completed.',
                                'validation_rules' => 'digital_signature:required|all_fields_completed:true'
                            ],
                            'Terms and Conditions' => [
                                'required' => true,
                                'instructions' => 'Digital acceptance of terms and conditions with timestamp and IP address logging.',
                                'validation_rules' => 'digital_signature:required|timestamp:logged'
                            ],
                        ]
                    ],
                    [
                        'name' => '🎉 Welcome & Activation',
                        'description' => 'Personalized welcome experience with account activation and feature introduction',
                        'sequence_order' => 5,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'notification',
                        'priority' => 'low',
                        'auto_complete' => true,
                        'completion_criteria' => 'Welcome package sent, account activated, and client successfully logged into portal',
                        'documents' => []
                    ],
                ]
            ],
            [
                'name' => 'Healthcare Patient Registration',
                'description' => 'Complete patient registration with medical history collection and POPIA compliance',
                'industry_category' => 'healthcare',
                'template_type' => 'system',
                'complexity_level' => 'medium',
                'estimated_duration_days' => 3,
                'access_scope_id' => $industryScope->id,
                'workflow_code' => 'HC_PATIENT_REGISTRATION',
                'milestones' => [
                    [
                        'name' => '🏥 Patient Information Intake',
                        'description' => 'Secure collection of patient demographics and insurance information with privacy protection',
                        'sequence_order' => 1,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'documentation',
                        'priority' => 'high',
                        'requires_docs' => true,
                        'completion_criteria' => 'All patient information forms completed with required signatures and consent',
                        'documents' => [
                            'Identity Document' => [
                                'required' => true,
                                'instructions' => 'Valid SA ID, passport, or birth certificate for minors. Must be current and legible.',
                                'validation_rules' => 'file_type:pdf,jpg,png|max_size:5MB'
                            ],
                            'Medical Aid Card' => [
                                'required' => false,
                                'instructions' => 'Current medical aid membership card or certificate if applicable.',
                                'validation_rules' => 'file_type:pdf,jpg,png|max_size:3MB'
                            ],
                            'Emergency Contact Information' => [
                                'required' => true,
                                'instructions' => 'Complete emergency contact details including relationship and alternative contact methods.',
                                'validation_rules' => 'form_completed:true|contact_verified:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '📋 Medical History Collection',
                        'description' => 'Comprehensive medical history gathering with digital health record integration',
                        'sequence_order' => 2,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'documentation',
                        'priority' => 'high',
                        'requires_docs' => true,
                        'completion_criteria' => 'Medical history forms completed, current medications documented, and health data integrated',
                        'documents' => [
                            'Medical History Form' => [
                                'required' => true,
                                'instructions' => 'Complete medical background including allergies, chronic conditions, and family history.',
                                'validation_rules' => 'digital_form:required|medical_review:pending'
                            ],
                            'Current Medication List' => [
                                'required' => true,
                                'instructions' => 'List all current prescriptions, dosages, and over-the-counter medications.',
                                'validation_rules' => 'medication_list:validated|drug_interactions:checked'
                            ],
                            'Previous Medical Records' => [
                                'required' => false,
                                'instructions' => 'Medical records from previous healthcare providers if available and relevant.',
                                'validation_rules' => 'file_type:pdf|max_size:20MB|medical_format:accepted'
                            ],
                        ]
                    ],
                    [
                        'name' => '✅ Registration Approval',
                        'description' => 'Final review and approval of patient registration with system integration',
                        'sequence_order' => 3,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'approval',
                        'priority' => 'medium',
                        'requires_approval' => true,
                        'completion_criteria' => 'Registration approved by medical staff and patient record created in system',
                        'documents' => []
                    ],
                ]
            ],
            [
                'name' => 'Employee Onboarding & HR Setup',
                'description' => 'Complete employee onboarding with SA labor law compliance and digital experience',
                'industry_category' => 'general',
                'template_type' => 'system',
                'complexity_level' => 'medium',
                'estimated_duration_days' => 10,
                'access_scope_id' => $globalScope->id,
                'workflow_code' => 'HR_EMPLOYEE_ONBOARDING',
                'milestones' => [
                    [
                        'name' => '📄 Employment Documentation',
                        'description' => 'Digital collection and signing of all employment contracts and legal documents',
                        'sequence_order' => 1,
                        'estimated_duration_days' => 2,
                        'milestone_type' => 'documentation',
                        'priority' => 'critical',
                        'requires_docs' => true,
                        'completion_criteria' => 'All employment documents digitally signed and legally compliant',
                        'documents' => [
                            'Employment Contract' => [
                                'required' => true,
                                'instructions' => 'Digitally signed original employment contract with all terms and conditions.',
                                'validation_rules' => 'digital_signature:required|legal_review:approved'
                            ],
                            'Identity Document' => [
                                'required' => true,
                                'instructions' => 'Valid SA ID, passport with work permit, or asylum seeker permit.',
                                'validation_rules' => 'work_authorization:verified|document_authentic:true'
                            ],
                            'Tax Number Certificate' => [
                                'required' => true,
                                'instructions' => 'SARS tax number certificate or individual tax number registration.',
                                'validation_rules' => 'sars_verified:true|tax_compliance:checked'
                            ],
                            'Banking Details' => [
                                'required' => true,
                                'instructions' => 'Bank account details for salary payments with proof of banking relationship.',
                                'validation_rules' => 'bank_verified:true|account_active:true'
                            ],
                            'Qualification Certificates' => [
                                'required' => true,
                                'instructions' => 'Academic and professional certificates relevant to the position.',
                                'validation_rules' => 'qualifications_verified:true|accreditation_checked:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '💻 Digital System Access Setup',
                        'description' => 'Automated creation of system accounts with role-based access permissions',
                        'sequence_order' => 2,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'task',
                        'priority' => 'high',
                        'completion_criteria' => 'All system accounts created, tested, and security training completed',
                        'documents' => [
                            'IT Security Policy' => [
                                'required' => true,
                                'instructions' => 'Digital acknowledgment of IT security policies and cybersecurity training completion.',
                                'validation_rules' => 'digital_signature:required|training_completed:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '🎓 Interactive Induction Training',
                        'description' => 'Gamified company induction with role-specific training modules and assessments',
                        'sequence_order' => 3,
                        'estimated_duration_days' => 5,
                        'milestone_type' => 'task',
                        'priority' => 'medium',
                        'completion_criteria' => 'All training modules completed with passing grades and practical assessments',
                        'documents' => [
                            'Training Completion Certificate' => [
                                'required' => true,
                                'instructions' => 'Digital certificate of completed training modules with assessment scores.',
                                'validation_rules' => 'assessment_passed:true|completion_rate:100%'
                            ],
                            'Safety Training Certificate' => [
                                'required' => true,
                                'instructions' => 'Workplace safety compliance training with emergency procedure knowledge.',
                                'validation_rules' => 'safety_test_passed:true|emergency_procedures:demonstrated'
                            ],
                        ]
                    ],
                    [
                        'name' => '📅 Probation Review Setup',
                        'description' => 'Automated scheduling of probation reviews with performance tracking integration',
                        'sequence_order' => 4,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'task',
                        'priority' => 'low',
                        'completion_criteria' => 'Probation schedule created, performance metrics configured, and manager notified',
                        'documents' => []
                    ],
                    [
                        'name' => '🌟 Welcome & Integration Complete',
                        'description' => 'Personalized welcome experience with team integration and buddy system activation',
                        'sequence_order' => 5,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'notification',
                        'priority' => 'low',
                        'auto_complete' => true,
                        'completion_criteria' => 'Welcome package delivered, team introductions completed, and buddy assigned',
                        'documents' => []
                    ],
                ]
            ],
            [
                'name' => 'Real Estate Lead to Sale',
                'description' => 'Complete real estate sales process with digital tools and automated milestone tracking',
                'industry_category' => 'real_estate',
                'template_type' => 'system',
                'complexity_level' => 'complex',
                'estimated_duration_days' => 45,
                'access_scope_id' => $industryScope->id,
                'workflow_code' => 'RE_LEAD_TO_SALE',
                'milestones' => [
                    [
                        'name' => '🎯 AI-Powered Lead Qualification',
                        'description' => 'Intelligent lead assessment with automated scoring and financial pre-qualification',
                        'sequence_order' => 1,
                        'estimated_duration_days' => 3,
                        'milestone_type' => 'task',
                        'priority' => 'high',
                        'completion_criteria' => 'Lead qualified, budget confirmed, and CRM profile created with scoring',
                        'documents' => [
                            'Pre-qualification Form' => [
                                'required' => true,
                                'instructions' => 'Digital financial pre-qualification with automated affordability calculation.',
                                'validation_rules' => 'financial_capacity:calculated|credit_pre_check:completed'
                            ],
                        ]
                    ],
                    [
                        'name' => '🏠 Virtual & Physical Property Tours',
                        'description' => 'Hybrid property viewing experience with VR tours and detailed documentation',
                        'sequence_order' => 2,
                        'estimated_duration_days' => 7,
                        'milestone_type' => 'task',
                        'priority' => 'medium',
                        'completion_criteria' => 'Properties viewed, client preferences captured, and shortlist created',
                        'documents' => [
                            'Property Viewing Report' => [
                                'required' => true,
                                'instructions' => 'Digital documentation of all properties viewed with client feedback and ratings.',
                                'validation_rules' => 'viewing_logged:true|client_feedback:recorded'
                            ],
                        ]
                    ],
                    [
                        'name' => '📋 Digital Offer to Purchase',
                        'description' => 'Electronic offer preparation with legal review and automated compliance checks',
                        'sequence_order' => 3,
                        'estimated_duration_days' => 5,
                        'milestone_type' => 'documentation',
                        'priority' => 'critical',
                        'requires_docs' => true,
                        'completion_criteria' => 'Legal offer prepared, reviewed, and submitted with all supporting documents',
                        'documents' => [
                            'Offer to Purchase' => [
                                'required' => true,
                                'instructions' => 'Legally binding digital purchase offer with electronic signatures and terms.',
                                'validation_rules' => 'legal_review:approved|digital_signature:required|terms_complete:true'
                            ],
                            'Proof of Deposit' => [
                                'required' => true,
                                'instructions' => 'Bank-verified proof of deposit payment with transaction reference.',
                                'validation_rules' => 'bank_verified:true|amount_confirmed:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '🏦 Automated Bond Application',
                        'description' => 'Streamlined home loan application with multiple bank submissions and tracking',
                        'sequence_order' => 4,
                        'estimated_duration_days' => 21,
                        'milestone_type' => 'approval',
                        'priority' => 'critical',
                        'requires_approval' => true,
                        'completion_criteria' => 'Home loan approved by bank with favorable terms and conditions',
                        'documents' => [
                            'Bond Application Form' => [
                                'required' => true,
                                'instructions' => 'Complete digital loan application with automated data validation.',
                                'validation_rules' => 'application_complete:true|data_validated:true'
                            ],
                            'Proof of Income' => [
                                'required' => true,
                                'instructions' => 'Salary certificates, bank statements, and employment verification.',
                                'validation_rules' => 'income_verified:true|employment_confirmed:true'
                            ],
                            'Bank Statements' => [
                                'required' => true,
                                'instructions' => '3 months recent bank statements with transaction analysis.',
                                'validation_rules' => 'statements_recent:90_days|transaction_analysis:completed'
                            ],
                            'Credit Report' => [
                                'required' => true,
                                'instructions' => 'Official credit bureau report with real-time scoring.',
                                'validation_rules' => 'credit_bureau:official|score_current:true'
                            ],
                        ]
                    ],
                    [
                        'name' => '🔑 Digital Transfer Process',
                        'description' => 'Electronic property transfer with blockchain verification and smart contracts',
                        'sequence_order' => 5,
                        'estimated_duration_days' => 9,
                        'milestone_type' => 'task',
                        'priority' => 'high',
                        'completion_criteria' => 'Property transfer completed, keys handed over, and ownership registered',
                        'documents' => [
                            'Transfer Documents' => [
                                'required' => true,
                                'instructions' => 'Digital transfer documentation with blockchain verification.',
                                'validation_rules' => 'blockchain_verified:true|legal_compliance:confirmed'
                            ],
                            'Property Insurance' => [
                                'required' => true,
                                'instructions' => 'Valid property insurance certificate with adequate coverage.',
                                'validation_rules' => 'insurance_active:true|coverage_adequate:true'
                            ],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Manufacturing Quality Control Process',
                'description' => 'IoT-enabled quality control workflow with real-time monitoring and automation',
                'industry_category' => 'manufacturing',
                'template_type' => 'system',
                'complexity_level' => 'medium',
                'estimated_duration_days' => 7,
                'access_scope_id' => $industryScope->id,
                'workflow_code' => 'MFG_QUALITY_CONTROL',
                'milestones' => [
                    [
                        'name' => '📦 Smart Material Inspection',
                        'description' => 'IoT-powered incoming material inspection with automated quality scoring',
                        'sequence_order' => 1,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'task',
                        'priority' => 'high',
                        'requires_docs' => true,
                        'completion_criteria' => 'All materials inspected, approved, and digitally tagged for tracking',
                        'documents' => [
                            'Material Certificate' => [
                                'required' => true,
                                'instructions' => 'Supplier-provided material certificates with blockchain verification.',
                                'validation_rules' => 'supplier_verified:true|material_authentic:true'
                            ],
                            'Quality Inspection Report' => [
                                'required' => true,
                                'instructions' => 'Automated inspection results with IoT sensor data and visual analysis.',
                                'validation_rules' => 'iot_verified:true|quality_threshold:met'
                            ],
                        ]
                    ],
                    [
                        'name' => '⚙️ Real-time Production Control',
                        'description' => 'Continuous monitoring of production processes with predictive quality analytics',
                        'sequence_order' => 2,
                        'estimated_duration_days' => 3,
                        'milestone_type' => 'task',
                        'priority' => 'critical',
                        'completion_criteria' => 'Production completed within specifications with continuous monitoring data',
                        'documents' => [
                            'Production Log Sheet' => [
                                'required' => true,
                                'instructions' => 'Real-time production records with IoT sensor data and operator inputs.',
                                'validation_rules' => 'realtime_logged:true|sensor_data:complete'
                            ],
                            'Process Control Chart' => [
                                'required' => true,
                                'instructions' => 'Statistical process control data with trend analysis and alerts.',
                                'validation_rules' => 'spc_compliant:true|trend_analysis:completed'
                            ],
                        ]
                    ],
                    [
                        'name' => '🔬 Automated Final Testing',
                        'description' => 'AI-powered comprehensive testing of finished products with quality prediction',
                        'sequence_order' => 3,
                        'estimated_duration_days' => 2,
                        'milestone_type' => 'review',
                        'priority' => 'critical',
                        'requires_approval' => true,
                        'completion_criteria' => 'All tests passed, quality certified, and predictive analysis completed',
                        'documents' => [
                            'Test Results Report' => [
                                'required' => true,
                                'instructions' => 'Comprehensive automated test results with AI quality predictions.',
                                'validation_rules' => 'all_tests_passed:true|ai_quality_score:acceptable'
                            ],
                            'Quality Certificate' => [
                                'required' => true,
                                'instructions' => 'Digital quality certification with blockchain verification.',
                                'validation_rules' => 'blockchain_certified:true|quality_standards:met'
                            ],
                        ]
                    ],
                    [
                        'name' => '📋 Smart Packaging & Shipping',
                        'description' => 'Automated packaging with IoT tracking and intelligent shipping optimization',
                        'sequence_order' => 4,
                        'estimated_duration_days' => 1,
                        'milestone_type' => 'approval',
                        'priority' => 'medium',
                        'requires_approval' => true,
                        'completion_criteria' => 'Products packaged, tracked, and ready for optimized shipment',
                        'documents' => [
                            'Packaging Specification' => [
                                'required' => true,
                                'instructions' => 'Smart packaging requirements with IoT tracking integration.',
                                'validation_rules' => 'packaging_optimized:true|tracking_enabled:true'
                            ],
                            'Shipping Manifest' => [
                                'required' => true,
                                'instructions' => 'AI-optimized shipping documentation with route and delivery predictions.',
                                'validation_rules' => 'route_optimized:true|delivery_predicted:true'
                            ],
                        ]
                    ],
                ]
            ],
        ];

        // Create workflows and their milestones
        foreach ($workflows as $workflowData) {
            $this->command->info("Creating workflow: {$workflowData['name']}");

            // Create the workflow template
            $workflow = WorkflowTemplate::create([
                'uuid' => Str::uuid(),
                'workflow_code' => $workflowData['workflow_code'],
                'name' => $workflowData['name'],
                'description' => $workflowData['description'],
                'industry_category' => $workflowData['industry_category'],
                'template_type' => $workflowData['template_type'],
                'complexity_level' => $workflowData['complexity_level'],
                'estimated_duration_days' => $workflowData['estimated_duration_days'],
                'access_scope_id' => $workflowData['access_scope_id'],
                'status_id' => $defaultStatus?->id,
                'status_type_id' => $defaultStatus?->id,
                'version_number' => '1.0',
                'version_status' => 'active',
                'is_published' => true,
                'is_public' => true,
                'is_system_template' => true,
                'created_by' => 1, // Assuming super admin user ID is 1
                'version_created_at' => now(),
                'version_created_by' => 1,
            ]);

            // Create milestones and document requirements
            foreach ($workflowData['milestones'] as $milestoneData) {
                $milestone = Milestone::create([
                    'workflow_template_id' => $workflow->id,
                    'name' => $milestoneData['name'],
                    'description' => $milestoneData['description'],
                    'sequence_order' => $milestoneData['sequence_order'],
                    'estimated_duration_days' => $milestoneData['estimated_duration_days'],
                    'milestone_type' => $milestoneData['milestone_type'],
                    'priority' => $milestoneData['priority'],
                    'status_id' => $defaultStatus?->id,
                    'status_type_id' => $defaultStatus?->id,
                    'requires_docs' => $milestoneData['requires_docs'] ?? false,
                    'requires_approval' => $milestoneData['requires_approval'] ?? false,
                    'can_be_skipped' => $milestoneData['can_be_skipped'] ?? false,
                    'auto_complete' => $milestoneData['auto_complete'] ?? false,
                    'completion_criteria' => $milestoneData['completion_criteria'],
                    'sla_days' => $milestoneData['estimated_duration_days'],
                    'actions' => [],
                ]);

                // Attach document requirements
                if (!empty($milestoneData['documents'])) {
                    $sequenceOrder = 1;
                    foreach ($milestoneData['documents'] as $documentName => $requirements) {
                        $documentType = DocumentType::where('name', $documentName)->first();
                        if ($documentType) {
                            $milestone->documentRequirements()->attach($documentType->id, [
                                'is_required' => $requirements['required'],
                                'allows_multiple' => false,
                                'sequence_order' => $sequenceOrder++,
                                'instructions' => $requirements['instructions'],
                                'validation_rules' => $requirements['validation_rules'] ?? null,
                            ]);
                        } else {
                            $this->command->warn("⚠️ Document type '{$documentName}' not found. Skipping...");
                        }
                    }
                }
            }

            $this->command->info("✅ Created workflow '{$workflow->name}' with " . count($workflowData['milestones']) . " milestones");
        }

        $this->command->info('🎉 Successfully created all 5 comprehensive workflow templates with modern UX features!');
        $this->command->info('📊 Summary:');
        $this->command->info('   • FICA/KYC Client Onboarding (Financial Services) - 5 milestones');
        $this->command->info('   • Healthcare Patient Registration - 3 milestones');
        $this->command->info('   • Employee Onboarding & HR Setup - 5 milestones');
        $this->command->info('   • Real Estate Lead to Sale - 5 milestones');
        $this->command->info('   • Manufacturing Quality Control - 4 milestones');
        $this->command->info('🚀 Ready for beautiful task creation with document requirements!');
    }
}
