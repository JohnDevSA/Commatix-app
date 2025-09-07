<?php

namespace Database\Seeders;

use App\Models\AccessScope;
use App\Models\DocumentType;
use App\Models\Industry;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Get access scopes
        $industryScope = AccessScope::where('name', 'industry_template')->first();
        $globalScope = AccessScope::where('name', 'global')->first();

        if (!$industryScope || !$globalScope) {
            $this->command->error('Access scopes not found. Please run AccessScopeSeeder first.');
            return;
        }

        // 1. Universal Business Operations (Global - All Industries Need These)
        $this->createUniversalBusinessDocuments($globalScope);

        // 2. South African Legal & Compliance (Global - Mandatory for all SA businesses)
        $this->createSAComplianceDocuments($globalScope);

        // 3. Human Resources & Employment (Global - All businesses have employees)
        $this->createHRAndEmploymentDocuments($globalScope);

        // 4. Financial & Accounting (Global - All businesses handle money)
        $this->createFinancialAccountingDocuments($globalScope);

        // 5. Industry-Specific Documents (Only what's truly unique to each industry)
        $this->createIndustrySpecificDocuments($industryScope);

        $this->command->info('✅ Comprehensive document types seeded successfully for South African businesses');
    }

    private function createUniversalBusinessDocuments($globalScope)
    {
        $this->command->info('Creating Universal Business Documents...');

        $documents = [
            // Identity & Verification
            [
                'name' => 'Identity Document',
                'description' => 'South African ID document, passport, or other official identification',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Proof of Address',
                'description' => 'Utility bill, bank statement, or municipal account (not older than 3 months)',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],

            // Payment & Banking
            [
                'name' => 'Proof of Payment',
                'description' => 'Bank receipts, EFT confirmations, payment slips, and transaction records',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Banking Details',
                'description' => 'Bank account confirmation letter or bank statement showing account details',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Invoice Documents',
                'description' => 'Invoices, quotes, purchase orders, and billing documentation',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],

            // Contracts & Agreements
            [
                'name' => 'Service Agreement',
                'description' => 'Service contracts, terms of service, and client agreements',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Supplier Contracts',
                'description' => 'Vendor agreements, supplier contracts, and procurement documents',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],

            // Communication & Correspondence
            [
                'name' => 'Official Correspondence',
                'description' => 'Letters, emails, notices, and official business communication',
                'allowed_file_types' => ['pdf', 'docx', 'jpg', 'png'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
        ];

        $this->createDocuments($documents, 'general', $globalScope);
    }

    private function createSAComplianceDocuments($globalScope)
    {
        $this->command->info('Creating South African Legal & Compliance Documents...');

        $documents = [
            // Company Registration & Legal
            [
                'name' => 'Company Registration (CK)',
                'description' => 'Certificate of Incorporation from CIPC (Companies and Intellectual Property Commission)',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Memorandum of Incorporation (MOI)',
                'description' => 'Company MOI and any amendments filed with CIPC',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],

            // Tax & Revenue
            [
                'name' => 'VAT Registration Certificate',
                'description' => 'VAT registration certificate from SARS',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Tax Clearance Certificate',
                'description' => 'SARS tax clearance certificate for compliance purposes',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'PAYE Registration',
                'description' => 'Pay-As-You-Earn registration certificate from SARS',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],

            // B-BBEE & Transformation
            [
                'name' => 'B-BBEE Certificate',
                'description' => 'Valid Broad-Based Black Economic Empowerment certificate',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Employment Equity Report',
                'description' => 'Annual employment equity reports submitted to Department of Labour',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],

            // Data Protection & Privacy
            [
                'name' => 'POPIA Compliance Documentation',
                'description' => 'Protection of Personal Information Act compliance records and policies',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Data Processing Consent',
                'description' => 'Customer consent forms and data processing agreements',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
        ];

        $this->createDocuments($documents, 'general', $globalScope);
    }

    private function createHRAndEmploymentDocuments($globalScope)
    {
        $this->command->info('Creating HR & Employment Documents...');

        $documents = [
            // Recruitment & Onboarding
            [
                'name' => 'Employment Application',
                'description' => 'Job application forms and candidate submissions',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'CV/Resume',
                'description' => 'Curriculum vitae and resume documents',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Reference Letters',
                'description' => 'Employment references and recommendation letters',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 5,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Qualifications & Certificates',
                'description' => 'Educational certificates, professional qualifications, and training certificates',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Background Check Results',
                'description' => 'Criminal background checks and security clearance documents',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 5,
                'allows_multiple' => true,
            ],

            // Employment Contracts & Documentation
            [
                'name' => 'Employment Contract',
                'description' => 'Signed employment agreements and contract amendments',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Job Description',
                'description' => 'Detailed job descriptions and role specifications',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Performance Reviews',
                'description' => 'Performance appraisals, evaluations, and development plans',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],

            // Compliance & Legal
            [
                'name' => 'UIF Registration',
                'description' => 'Unemployment Insurance Fund registration and employee declarations',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 5,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Medical Certificate',
                'description' => 'Medical fitness certificates and occupational health records',
                'allowed_file_types' => ['pdf', 'jpg', 'png'],
                'max_file_size_mb' => 5,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Disciplinary Records',
                'description' => 'Disciplinary action records, warnings, and corrective measures',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],

            // Benefits & Compensation
            [
                'name' => 'Salary Slips',
                'description' => 'Monthly salary slips and wage statements',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 5,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Leave Applications',
                'description' => 'Annual leave, sick leave, and special leave applications',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 5,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Pension Fund Documentation',
                'description' => 'Retirement fund applications and benefit statements',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
        ];

        $this->createDocuments($documents, 'general', $globalScope);
    }

    private function createFinancialAccountingDocuments($globalScope)
    {
        $this->command->info('Creating Financial & Accounting Documents...');

        $documents = [
            // Financial Statements & Reports
            [
                'name' => 'Financial Statements',
                'description' => 'Annual financial statements, balance sheets, and income statements',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 25,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Management Accounts',
                'description' => 'Monthly/quarterly management accounts and financial reports',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Cash Flow Statements',
                'description' => 'Cash flow projections and liquidity analysis reports',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],

            // Banking & Transactions
            [
                'name' => 'Bank Statements',
                'description' => 'Monthly bank statements and transaction records',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Bank Reconciliations',
                'description' => 'Monthly bank reconciliation statements and supporting documents',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],

            // Auditing & Compliance
            [
                'name' => 'Audit Reports',
                'description' => 'External audit reports and management letters',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Tax Returns',
                'description' => 'Annual tax returns and supporting schedules',
                'allowed_file_types' => ['pdf', 'xlsx'],
                'max_file_size_mb' => 25,
                'allows_multiple' => true,
            ],

            // Credit & Insurance
            [
                'name' => 'Credit Applications',
                'description' => 'Loan applications, credit facility requests, and supporting documents',
                'allowed_file_types' => ['pdf', 'docx'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Insurance Policies',
                'description' => 'Business insurance policies, certificates, and claims documentation',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
        ];

        $this->createDocuments($documents, 'general', $globalScope);
    }

    private function createIndustrySpecificDocuments($industryScope)
    {
        $this->command->info('Creating Industry-Specific Documents...');

        // Only truly unique documents that are specific to each industry
        $this->createFinancialServicesSpecific($industryScope);
        $this->createHealthcareSpecific($industryScope);
        $this->createEducationSpecific($industryScope);
        $this->createMiningSpecific($industryScope);
        $this->createManufacturingSpecific($industryScope);
        $this->createAgricultureSpecific($industryScope);
        $this->createTechnologySpecific($industryScope);
        $this->createConstructionSpecific($industryScope);
        $this->createLogisticsSpecific($industryScope);
        $this->createRetailSpecific($industryScope);
    }

    private function createFinancialServicesSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'FICA Documentation',
                'description' => 'Financial Intelligence Centre Act compliance documentation and KYC records',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
            [
                'name' => 'FSB/FSCA License',
                'description' => 'Financial Sector Conduct Authority licensing and regulatory approvals',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Credit Bureau Reports',
                'description' => 'Credit reports from TransUnion, Experian, XDS, or Compuscan',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Anti-Money Laundering Records',
                'description' => 'AML compliance documentation and suspicious transaction reports',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'financial_services', $industryScope);
    }

    private function createHealthcareSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'HPCSA Registration',
                'description' => 'Health Professions Council of South Africa registration certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Patient Medical Records',
                'description' => 'Confidential patient medical histories and treatment records',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 50,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Medical Device Certificates',
                'description' => 'SAHPRA medical device registration and compliance certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Clinical Trial Documentation',
                'description' => 'Clinical research protocols and ethics committee approvals',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 30,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'healthcare', $industryScope);
    }

    private function createEducationSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'SACE Registration',
                'description' => 'South African Council for Educators professional registration',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 5,
                'allows_multiple' => false,
            ],
            [
                'name' => 'SAQA Accreditation',
                'description' => 'South African Qualifications Authority accreditation certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Student Academic Records',
                'description' => 'Confidential student transcripts and academic progress records',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'education', $industryScope);
    }

    private function createMiningSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'Mining Rights License',
                'description' => 'Department of Mineral Resources and Energy mining rights and licenses',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 20,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Environmental Authorization',
                'description' => 'Environmental impact assessments and water use licenses',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 50,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Mine Health & Safety Compliance',
                'description' => 'Mine Health and Safety Inspectorate compliance certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 25,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'mining', $industryScope);
    }

    private function createManufacturingSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'SABS Product Certification',
                'description' => 'South African Bureau of Standards product compliance certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'ISO Quality Certificates',
                'description' => 'International Organization for Standardization quality management certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'manufacturing', $industryScope);
    }

    private function createAgricultureSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'Land Use Rights Certificate',
                'description' => 'Department of Agriculture land use permits and certificates',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Organic Certification',
                'description' => 'Certified organic farming compliance and inspection reports',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'agriculture', $industryScope);
    }

    private function createTechnologySpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'ICASA Type Approval',
                'description' => 'Independent Communications Authority telecommunications equipment approval',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Software IP Registration',
                'description' => 'Intellectual property registration for software and technology',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'technology', $industryScope);
    }

    private function createConstructionSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'NHBRC Registration',
                'description' => 'National Home Builders Registration Council registration certificate',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'CIDB Certification',
                'description' => 'Construction Industry Development Board contractor registration',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => false,
            ],
            [
                'name' => 'Municipal Building Approvals',
                'description' => 'Local municipality building plan approvals and permits',
                'allowed_file_types' => ['pdf', 'dwg'],
                'max_file_size_mb' => 30,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'construction', $industryScope);
    }

    private function createLogisticsSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'Operating License',
                'description' => 'Department of Transport goods and passenger operating licenses',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Cross-Border Transport Permits',
                'description' => 'SADC and international transport permits and documentation',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 15,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'logistics', $industryScope);
    }

    private function createRetailSpecific($industryScope)
    {
        $documents = [
            [
                'name' => 'Consumer Protection Compliance',
                'description' => 'Consumer Protection Act compliance documentation and policies',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
            [
                'name' => 'Product Safety Certificates',
                'description' => 'Product safety compliance and consumer goods certification',
                'allowed_file_types' => ['pdf'],
                'max_file_size_mb' => 10,
                'allows_multiple' => true,
            ],
        ];
        $this->createIndustryDocuments($documents, 'retail', $industryScope);
    }

    private function createDocuments(array $documents, string $industryCode, $scope)
    {
        foreach ($documents as $doc) {
            DocumentType::firstOrCreate(
                [
                    'name' => $doc['name'],
                    'access_scope_id' => $scope->id,
                    'tenant_id' => null,
                ],
                array_merge($doc, [
                    'industry_category' => $industryCode,
                    'access_scope_id' => $scope->id,
                    'tenant_id' => null,
                ])
            );
        }
    }

    private function createIndustryDocuments(array $documents, string $industryCode, $industryScope)
    {
        foreach ($documents as $doc) {
            DocumentType::firstOrCreate(
                [
                    'name' => $doc['name'],
                    'industry_category' => $industryCode,
                    'access_scope_id' => $industryScope->id,
                ],
                array_merge($doc, [
                    'industry_category' => $industryCode,
                    'access_scope_id' => $industryScope->id,
                    'tenant_id' => null,
                ])
            );
        }

        $this->command->info("✅ Created industry-specific documents for {$industryCode}");
    }
}
