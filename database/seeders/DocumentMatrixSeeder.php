<?php

namespace Database\Seeders;

use App\Models\Requirement;
use App\Models\RequirementMatrix;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentMatrixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'civil_status' => 'Single',
                'employment_status' => 'Locally Employed',
                'market_segment' => 'Everyhome',
                'documents' => [
                    'Government ID 1',
                    'Government ID 2',
                    'Original Certificate of Employment with Compensation',
                    'One (1) Month Latest Original and HR-Certified Payslip',
                    'Updated/Latest Employee\'s Statement of Accumulated Value (ESAV)',
                    'Birth Certificate',
                    '4 pcs. of 1x1 Photo with White Background',
                    'Proof of Billing Address',
                    'Letter of Consent/Authorization of buyer duly received by Employer (per Data Privacy Law)',
                    'At least (3) Mos. Certified Latest payslip'
                ]
            ],
            [
                'civil_status' => 'Single',
                'employment_status' => 'Locally Employed',
                'market_segment' => 'Extraordinary',
                'documents' => [
                    'Government ID 1',
                    'Government ID 2',
                    'Original Certificate of Employment with Compensation',
                    'One (1) Month Latest Original and HR-Certified Payslip',
                    'Updated/Latest Employee\'s Statement of Accumulated Value (ESAV)',
                    'Birth Certificate',
                    '4 pcs. of 1x1 Photo with White Background',
                    'Proof of Billing Address',
                    'Letter of Consent/Authorization of buyer duly received by Employer (per Data Privacy Law)',
                    'At least (3) Mos. Certified Latest payslip'
                ]
            ],
            [
                'civil_status' => 'Single',
                'employment_status' => 'Overseas Filipino Worker (OFW)',
                'market_segment' => 'Everyhome',
                'documents' => [
                    'Government ID 1',
                    'Government ID 2',
                    'Employment Contract',
                    'Overseas Employment Certificate',
                    'Updated/Latest Employee\'s Statement of Accumulated Value (ESAV)',
                    'Copy of Passport with Appropriate Visa (Working Visa)',
                    'Working Permit (Permit to Stay indicating work as the purpose)',
                    'Notarized and/or Consularized Buyer\'s Special Power of Attorney (SPA)',
                    'Authorized Representative Information Sheet (ARIS) with 2 pcs. 1x1 picture',
                    'Valid ID of AIF',
                    'Birth Certificate',
                    '4 pcs. of 1x1 Photo with White Background',
                    'Proof of Billing Address',
                    'Letter of Consent/Authorization of buyer duly received by Employer (per Data Privacy Law)',
                    'Working Permit Card'
                ]
            ],
            [
                'civil_status' => 'Married',
                'employment_status' => 'Self-Employed with Business',
                'market_segment' => 'Everyhome',
                'documents' => [
                    'Government ID 1',
                    'Government ID 2',
                    'Income Tax Return - BIR 1701 (Certified by BIR)',
                    'Audited Financial Statement',
                    'Official Receipt / Deposit Slip with Bank of Income Tax payment',
                    'Business/ Mayor\'s permit',
                    'DTI Business Registration',
                    'Sketch of Business Location',
                    'Marriage Certificate',
                    '4 pcs. of 1x1 Photo with White Background',
                    'Proof of Billing Address',
                    'Letter of Consent/Authorization of buyer for Credit & Background Investigation (per Data Privacy Law)'
                ]
            ],
            [
                'civil_status' => 'Widow/er',
                'employment_status' => 'Locally Employed',
                'market_segment' => 'Everyhome',
                'documents' => [
                    'Government ID 1',
                    'Government ID 2',
                    'Original Certificate of Employment with Compensation',
                    'One (1) Month Latest Original and HR-Certified Payslip',
                    'Updated/Latest Employee\'s Statement of Accumulated Value (ESAV)',
                    'Marriage Certificate',
                    'Death Certificate',
                    '4 pcs. of 1x1 Photo with White Background',
                    'Proof of Billing Address',
                    'Letter of Consent/Authorization of buyer duly received by Employer (per Data Privacy Law)',
                    'If email address of Employer is a public domain: e.g. Gmail, Yahoo, etc.',
                    'At least (3) Mos. Certified Latest payslip'
                ]
            ],
        ];

        foreach ($data as $index => $d) {
            $requirements = Requirement::whereIn('description', $d['documents'])
                ->pluck('description');


            RequirementMatrix::updateOrCreate([
                'civil_status' => $d['civil_status'],
                'employment_status' => $d['employment_status'],
                'market_segment' => $d['market_segment'],
            ], ['requirements' => $requirements]);
        }

    }
}
