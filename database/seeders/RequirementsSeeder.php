<?php

namespace Database\Seeders;

use App\Models\Requirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Requirement::updateOrCreate(['id' => 1 ], ['description' => 'Government ID 1 Image']);
        Requirement::updateOrCreate(['id' => 2 ], ['description' => 'Government ID 2 Image']);
        Requirement::updateOrCreate(['id' => 3 ], ['description' => 'Certificate of Employment Document']);
        Requirement::updateOrCreate(['id' => 4 ], ['description' => 'One Month Latest Payslip Document']);
        Requirement::updateOrCreate(['id' => 5 ], ['description' => 'ESAV Document']);
        Requirement::updateOrCreate(['id' => 6 ], ['description' => 'Birth Certificate Document']);
        Requirement::updateOrCreate(['id' => 7 ], ['description' => 'Photo Image']);
        Requirement::updateOrCreate(['id' => 8 ], ['description' => 'Proof of Billing Address Document']);
        Requirement::updateOrCreate(['id' => 9 ], ['description' => 'Letter of Consent Employer Document']);
        Requirement::updateOrCreate(['id' => 10 ], ['description' => 'Three Months Certified Payslips Document']);
        Requirement::updateOrCreate(['id' => 11 ], ['description' => 'Employment Contract Document']);
        Requirement::updateOrCreate(['id' => 12 ], ['description' => 'OFW Employment Certificate Document']);
        Requirement::updateOrCreate(['id' => 13 ], ['description' => 'Passport With Visa Image']);
        Requirement::updateOrCreate(['id' => 14 ], ['description' => 'Working Permit Document']);
        Requirement::updateOrCreate(['id' => 15 ], ['description' => 'Notarized SPA Document']);
        Requirement::updateOrCreate(['id' => 16 ], ['description' => 'Authorized Representative Info Sheet Document']);
        Requirement::updateOrCreate(['id' => 17 ], ['description' => 'Valid ID of AIF Image']);
        Requirement::updateOrCreate(['id' => 18 ], ['description' => 'Working Permit Card Document']);
        Requirement::updateOrCreate(['id' => 19 ], ['description' => 'ITR BIR 1701 Document']);
        Requirement::updateOrCreate(['id' => 20 ], ['description' => 'Audited Financial Statement Document']);
        Requirement::updateOrCreate(['id' => 21 ], ['description' => 'Official Receipt Tax Payment Document']);
        Requirement::updateOrCreate(['id' => 22 ], ['description' => 'Business Mayor\'s Permit Document']);
        Requirement::updateOrCreate(['id' => 23 ], ['description' => 'DTI Business Registration Document']);
        Requirement::updateOrCreate(['id' => 24 ], ['description' => 'Sketch of Business Location Document']);
        Requirement::updateOrCreate(['id' => 25 ], ['description' => 'Letter of Consent Credit Background Investigation Document']);
        Requirement::updateOrCreate(['id' => 26 ], ['description' => 'Marriage Certificate Document']);
        Requirement::updateOrCreate(['id' => 27 ], ['description' => 'Government ID of Spouse Image']);
        Requirement::updateOrCreate(['id' => 28 ], ['description' => 'Court Decision Annulment Document']);
        Requirement::updateOrCreate(['id' => 29 ], ['description' => 'Marraige Contract Document']);
        Requirement::updateOrCreate(['id' => 30 ], ['description' => 'Court Decision Separation Document']);
        Requirement::updateOrCreate(['id' => 31 ], ['description' => 'Death Certificate Document']);
    }
}
