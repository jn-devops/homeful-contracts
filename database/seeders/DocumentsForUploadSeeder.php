<?php

namespace Database\Seeders;

use App\Models\Requirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentsForUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Government ID 1',
            'Government ID 2',
            'Original Certificate of Employment with Compensation',
            'One (1) Month Latest Original and HR-Certified Payslip',
            'Updated/Latest Employee\'s Statement of Accumulated Value (ESAV)',
            'Birth Certificate',
            '4 pcs. of 1x1 Photo with White Background',
            'Proof of Billing Address',
            'Letter of Consent/Authorization of buyer duly received by Employer (per Data Privacy Law)',
            'At least (3) Mos. Certified Latest payslip',
            'Employment Contract',
            'Overseas Employment Certificate',
            'Copy of Passport with Appropriate Visa (Working Visa)',
            'Working Permit (Permit to Stay indicating work as the purpose)',
            'Notarized and/or Consularized Buyer\'s Special Power of Attorney (SPA)',
            'Authorized Representative Information Sheet (ARIS) with 2 pcs. 1x1 picture',
            'Valid ID of AIF',
            'Working Permit Card',
            'Income Tax Return - BIR 1701 (Certified by BIR)',
            'Audited Financial Statement',
            'Official Receipt / Deposit Slip with Bank of Income Tax payment',
            'Business/ Mayor\'s permit',
            'DTI Business Registration',
            'Sketch of Business Location',
            'Letter of Consent/Authorization of buyer for Credit & Background Investigation (per Data Privacy Law)',
            'Marriage Certificate',
            'Government ID of spouse',
            'Court Decision on Annulment',
            'Marriage Contract',
            'Court Decision on Separation',
            'Death Certificate'
        ];

        foreach ($data as $index => $d) {
            Requirement::updateOrCreate(['description' => $d]);
        }
    }
}
