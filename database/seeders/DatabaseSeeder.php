<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\BarangaySetting;
use App\Models\Household;
use App\Models\Resident;
use App\Models\Official;
use App\Models\DocumentIssued;
use App\Models\BarangayId;
use App\Models\BlotterRecord;
use App\Models\BlotterParty;
use App\Models\Business;
use App\Models\HealthRecord;
use App\Models\ServiceRequest;
use App\Models\Announcement;
use App\Models\RevenueRecord;
use App\Models\MeetingMinute;
use App\Models\Purok;
use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        User::create(['name'=>'Admin User','email'=>'admin@barangay.local','password'=>Hash::make('password'),'role'=>'admin']);
        User::create(['name'=>'Maria Santos','email'=>'secretary@barangay.local','password'=>Hash::make('password'),'role'=>'secretary']);
        User::create(['name'=>'Juan Dela Cruz','email'=>'staff@barangay.local','password'=>Hash::make('password'),'role'=>'staff']);

        // Settings
        BarangaySetting::create(['barangay_name'=>'Masagana','municipality'=>'San Pablo City','province'=>'Laguna','region'=>'IV-A']);

        // Puroks
        foreach (range(1, 10) as $i) {
            Purok::create(['name' => 'Purok ' . $i, 'is_active' => true, 'sort_order' => $i]);
        }

        // Document Templates - full templates with header, body, footer
        DocumentTemplate::create([
            'slug'=>'barangay_clearance','label'=>'Barangay Clearance','is_active'=>true,'fee'=>'₱50.00','sort_order'=>1,'orientation'=>'portrait','paper_size'=>'letter',
            'header_line_1'=>'Republic of the Philippines',
            'header_line_2'=>'{{municipality}}',
            'header_line_3'=>'{{province}}',
            'header_line_4'=>'Barangay {{barangay}}',
            'show_logo'=>true,'show_seal'=>true,'show_qr_code'=>true,'show_control_number'=>true,
            'body_paragraphs'=>[
                ['text'=>'TO WHOM IT MAY CONCERN:'],
                ['text'=>'This is to certify that **{{full_name}}**, of legal age, {{sex}}, {{civil_status}}, and a resident of {{purok}}, Barangay {{barangay}}, {{municipality}}, {{province}}, is a person of good moral character and has NO derogatory record in this office.'],
                ['text'=>'This certification is being issued at the request of the above-named person for **{{purpose}}** purposes.'],
            ],
            'prepared_by_title'=>'Barangay Staff','approved_by_title'=>'Punong Barangay',
        ]);
        DocumentTemplate::create([
            'slug'=>'certificate_of_residency','label'=>'Certificate of Residency','is_active'=>true,'fee'=>'₱50.00','sort_order'=>2,'orientation'=>'portrait','paper_size'=>'letter',
            'header_line_1'=>'Republic of the Philippines',
            'header_line_2'=>'{{municipality}}',
            'header_line_3'=>'{{province}}',
            'header_line_4'=>'Barangay {{barangay}}',
            'show_logo'=>true,'show_seal'=>true,'show_qr_code'=>true,'show_control_number'=>true,
            'body_paragraphs'=>[
                ['text'=>'TO WHOM IT MAY CONCERN:'],
                ['text'=>'This is to certify that **{{full_name}}** is a bonafide resident of **{{purok}}**, Barangay {{barangay}}, {{municipality}}, {{province}} since **birth**.'],
                ['text'=>'This certification is issued upon request for **{{purpose}}** purposes.'],
            ],
            'prepared_by_title'=>'Barangay Staff','approved_by_title'=>'Punong Barangay',
        ]);
        DocumentTemplate::create([
            'slug'=>'certificate_of_indigency','label'=>'Certificate of Indigency','is_active'=>true,'fee'=>'Free','sort_order'=>3,'orientation'=>'portrait','paper_size'=>'letter',
            'header_line_1'=>'Republic of the Philippines',
            'header_line_2'=>'{{municipality}}',
            'header_line_3'=>'{{province}}',
            'header_line_4'=>'Barangay {{barangay}}',
            'show_logo'=>true,'show_seal'=>true,'show_qr_code'=>true,'show_control_number'=>true,
            'body_paragraphs'=>[
                ['text'=>'TO WHOM IT MAY CONCERN:'],
                ['text'=>'This is to certify that **{{full_name}}**, of legal age, {{sex}}, {{civil_status}}, and a resident of {{purok}}, Barangay {{barangay}}, {{municipality}}, {{province}}, is an **INDIGENT** individual/family based on the records of this barangay.'],
                ['text'=>'This certification is being issued for **{{purpose}}** purposes.'],
            ],
            'prepared_by_title'=>'Barangay Staff','approved_by_title'=>'Punong Barangay',
        ]);
        DocumentTemplate::create([
            'slug'=>'good_moral_character','label'=>'Certificate of Good Moral Character','is_active'=>true,'fee'=>'₱50.00','sort_order'=>4,'orientation'=>'portrait','paper_size'=>'letter',
            'header_line_1'=>'Republic of the Philippines',
            'header_line_2'=>'{{municipality}}',
            'header_line_3'=>'{{province}}',
            'header_line_4'=>'Barangay {{barangay}}',
            'show_logo'=>true,'show_seal'=>true,'show_qr_code'=>true,'show_control_number'=>true,
            'body_paragraphs'=>[
                ['text'=>'TO WHOM IT MAY CONCERN:'],
                ['text'=>'This is to certify that **{{full_name}}**, of legal age, {{sex}}, {{civil_status}}, residing at {{purok}}, Barangay {{barangay}}, {{municipality}}, {{province}}, is known to be a person of good moral character.'],
                ['text'=>'This certification is issued for **{{purpose}}** purposes.'],
            ],
            'prepared_by_title'=>'Barangay Staff','approved_by_title'=>'Punong Barangay',
        ]);
        DocumentTemplate::create([
            'slug'=>'business_clearance','label'=>'Business Permit Clearance','is_active'=>true,'fee'=>'₱100.00','sort_order'=>5,'orientation'=>'portrait','paper_size'=>'letter',
            'header_line_1'=>'Republic of the Philippines',
            'header_line_2'=>'{{municipality}}',
            'header_line_3'=>'{{province}}',
            'header_line_4'=>'Barangay {{barangay}}',
            'show_logo'=>true,'show_seal'=>true,'show_qr_code'=>true,'show_control_number'=>true,
            'body_paragraphs'=>[
                ['text'=>'TO WHOM IT MAY CONCERN:'],
                ['text'=>'This is to certify that **{{full_name}}** has been granted clearance to operate a business within Barangay {{barangay}}, {{municipality}}, {{province}}, subject to existing barangay ordinances and regulations.'],
                ['text'=>'This clearance is issued for **{{purpose}}** purposes.'],
            ],
            'prepared_by_title'=>'Barangay Staff','approved_by_title'=>'Punong Barangay',
        ]);
        DocumentTemplate::create([
            'slug'=>'barangay_id','label'=>'Barangay ID','is_active'=>true,'fee'=>'₱100.00','sort_order'=>6,'orientation'=>'portrait','paper_size'=>'letter',
            'header_line_1'=>'Republic of the Philippines',
            'header_line_2'=>'{{municipality}}',
            'header_line_3'=>'{{province}}',
            'header_line_4'=>'Barangay {{barangay}}',
            'show_logo'=>true,'show_seal'=>true,'show_qr_code'=>true,'show_control_number'=>true,
            'body_paragraphs'=>[
                ['text'=>'Official Barangay Identification Card'],
            ],
            'prepared_by_title'=>'Barangay Staff','approved_by_title'=>'Punong Barangay',
        ]);

        // Officials
        foreach ([['Pedro Reyes','Punong Barangay'],['Ana Garcia','Kagawad'],['Roberto Cruz','Kagawad'],['Miguel Torres','SK Chairperson']] as [$n,$p]) {
            Official::create(['name'=>$n,'position'=>$p,'term_start'=>'2023-01-01','term_end'=>'2025-12-31']);
        }

        // Households
        $streets = ['Rizal St.','Mabini Ave.','Bonifacio Blvd.','Magsaysay St.','Aguinaldo Ave.','Luna St.','Del Pilar St.','Quezon Ave.'];
        $households = [];
        for ($i = 1; $i <= 15; $i++) {
            $num = rand(1, 200);
            $street = $streets[array_rand($streets)];
            $purok = 'Purok ' . rand(1, 10);
            $households[] = Household::create([
                'house_number' => (string) $num,
                'street' => $street,
                'purok' => $purok,
                'zone' => 'Zone ' . rand(1, 5),
                'sitio' => collect([null, 'Sitio maybunga', 'Sitio kawayan', 'Sitio lumina'])->random(),
                'full_address' => "{$num} {$street}, {$purok}, Masagana",
            ]);
        }

        // Resident data arrays
        $fNames = ['Juan','Maria','Jose','Ana','Pedro','Carmen','Roberto','Teresa','Miguel','Cristina','Antonio','Rosa','Eduardo','Lourdes','Ricardo','Fernando','Catherine','Michael','Patricia','Elena','Ramon','Gloria','Alfredo','Belen','Rogelio'];
        $mNames = ['Santos','Garcia','Reyes','Cruz','Lopez','Torres','Rivera','Gonzales','Aquino','Bautista','Villanueva','Mendoza','Castillo','Ramos','Flores'];
        $lNames = ['Dela Cruz','Garcia','Reyes','Santos','Lopez','Torres','Rivera','Gonzales','Aquino','Bautista','Villanueva','Mendoza','Castillo','Ramos','Flores'];
        $suffixes = [null,null,null,null,null,null,null,'Jr.','Sr.','III','II'];
        $birthPlaces = ['San Pablo City, Laguna','Calamba City, Laguna','Sta. Rosa, Laguna','Biñan, Laguna','Los Baños, Laguna','Bay, Laguna','Quezon City','Manila','Cavite City','Batangas City'];
        $bloodTypes = ['A+','A-','B+','B-','AB+','AB-','O+','O-',null];
        $religions = ['Roman Catholic','Iglesia ni Cristo','Born Again Christian','Methodist','Islam','Buddhist',null,null];
        $civilStatuses = ['single','married','widowed','separated','cohabiting'];
        $occupations = ['Teacher','Farmer','Tricycle Driver','Vendor','Sari-sari Store Owner','Construction Worker','Nurse','OFW','Housewife','Student','Government Employee','Driver','Mechanic','Fisherman','Barber',null,null];
        $empStatuses = ['employed','unemployed','self_employed','underemployed','student','osy','retired',null];
        $incomeRanges = ['Below ₱10,000','₱10,000 - ₱20,000','₱20,000 - ₱50,000','₱50,000 - ₱100,000','Above ₱100,000',null];
        $educationLevels = ['elementary','high_school','vocational','college_undergrad','college_graduate','post_graduate',null];
        $disabilityTypes = ['Visual','Hearing','Mobility','Learning','Psychosocial',null,null,null,null];
        $resStatuses = ['homeowner','homeowner','homeowner','renter','renter','transient'];
        $relationships = ['head','spouse','child','child','child','relative','relative','boarder'];
        $emergencyRels = ['Spouse','Parent','Child','Sibling','Other'];

        $residents = [];
        for ($i = 0; $i < 50; $i++) {
            $age = rand(1, 85);
            $isHead = ($i % 8 === 0);
            $sex = rand(0, 1) ? 'male' : 'female';

            $residents[] = Resident::create([
                // IDs (auto-generated resident_id_number in model)
                'barangay_card_id' => 'BC-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'national_id_number' => rand(0, 1) ? 'PSN-' . rand(100000000000, 999999999999) : null,
                'voters_precinct_number' => $age >= 18 ? strtoupper(substr($lNames[array_rand($lNames)], 0, 3)) . '-' . rand(1, 50) . '-' . rand(1, 999) : null,

                // Household
                'household_id' => $households[array_rand($households)]->id,
                'relationship_to_head' => $isHead ? 'head' : $relationships[array_rand($relationships)],

                // Demographics
                'first_name' => $fNames[array_rand($fNames)],
                'middle_name' => $mNames[array_rand($mNames)],
                'last_name' => $lNames[array_rand($lNames)],
                'suffix' => $suffixes[array_rand($suffixes)],
                'birthdate' => now()->subYears($age)->subDays(rand(0, 364)),
                'birth_place' => $birthPlaces[array_rand($birthPlaces)],
                'sex' => $sex,
                'citizenship' => rand(0, 95) > 5 ? 'Filipino' : 'Dual Citizen',
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'religion' => $religions[array_rand($religions)],
                'civil_status' => $age < 18 ? 'single' : $civilStatuses[array_rand($civilStatuses)],

                // Address
                'purok' => 'Purok ' . rand(1, 10),
                'street_address' => rand(1, 200) . ' ' . $streets[array_rand($streets)],
                'residency_status' => $resStatuses[array_rand($resStatuses)],
                'length_of_residency_years' => rand(0, 40),
                'previous_address' => rand(0, 5) > 3 ? $birthPlaces[array_rand($birthPlaces)] : null,

                // Socio-Economic
                'occupation' => $age < 15 ? null : $occupations[array_rand($occupations)],
                'employment_status' => $age < 15 ? null : $empStatuses[array_rand($empStatuses)],
                'monthly_income_range' => $age < 15 ? null : $incomeRanges[array_rand($incomeRanges)],
                'educational_attainment' => $age < 6 ? null : $educationLevels[array_rand($educationLevels)],

                // Sector flags
                'is_registered_voter' => $age >= 18 ? rand(0, 10) > 3 : false,
                'is_pwd' => rand(0, 100) === 0,
                'type_of_disability' => null, // Set after if is_pwd
                'is_senior_citizen' => $age >= 60,
                'is_4ps_beneficiary' => $age >= 5 && rand(0, 10) === 0,
                'is_solo_parent' => $age >= 18 && rand(0, 15) === 0,
                'is_indigent' => rand(0, 12) === 0,

                // Contact
                'contact_number' => '09' . rand(100000000, 999999999),
                'email' => rand(0, 5) > 2 ? strtolower($fNames[array_rand($fNames)] . '.' . $lNames[array_rand($lNames)] . '@gmail.com') : null,

                // Emergency
                'emergency_contact_name' => $fNames[array_rand($fNames)] . ' ' . $lNames[array_rand($lNames)],
                'emergency_contact_number' => '09' . rand(100000000, 999999999),
                'emergency_relationship' => $emergencyRels[array_rand($emergencyRels)],

                // Status
                'is_active' => true,
            ]);

            // Set disability type for PWDs
            if ($residents[$i]->is_pwd) {
                $residents[$i]->update(['type_of_disability' => $disabilityTypes[array_rand($disabilityTypes)]]);
            }
        }

        // Documents
        $docTypes = ['barangay_clearance','certificate_of_residency','certificate_of_indigency','business_clearance'];
        for ($i = 0; $i < 20; $i++) {
            $t = $docTypes[array_rand($docTypes)];
            DocumentIssued::create([
                'resident_id' => $residents[array_rand($residents)]->id,
                'document_type' => $t,
                'control_number' => DocumentIssued::generateControlNumber($t),
                'issued_by' => User::inRandomOrder()->first()->id,
                'issued_at' => now()->subDays(rand(0, 90)),
                'purpose' => collect(['Employment','Loan','School Enrollment','Business Permit','Travel','Bank Transaction','Legal'])->random(),
                'qr_token' => DocumentIssued::generateQrToken(),
                'status' => 'valid',
            ]);
        }

        // Blotter
        for ($i = 0; $i < 5; $i++) {
            $b = BlotterRecord::create([
                'blotter_number' => BlotterRecord::generateBlotterNumber(),
                'incident_type' => collect(['theft','quarrel','noise_complaint','physical_injury','property_damage','domestic_dispute'])->random(),
                'location' => 'Purok ' . rand(1, 10),
                'incident_datetime' => now()->subDays(rand(1, 60)),
                'narrative' => 'Sample incident report for testing purposes.',
                'status' => collect(['pending','settled','escalated'])->random(),
                'recorded_by' => User::first()->id,
            ]);
            BlotterParty::create(['blotter_record_id'=>$b->id,'role'=>'complainant','name'=>$residents[array_rand($residents)]->fullName()]);
            BlotterParty::create(['blotter_record_id'=>$b->id,'role'=>'respondent','name'=>$residents[array_rand($residents)]->fullName()]);
        }

        // Businesses
        foreach (['Sari-Sari Store ni Aling Rosa','Beauty Parlor ni Tita','Internet Cafe','Talyer ni Mang Kiko','Laundry Shop'] as $bn) {
            Business::create([
                'business_name' => $bn,
                'owner_name' => $residents[array_rand($residents)]->fullName(),
                'business_type' => collect(['Retail','Services','Technology','Food','Manufacturing'])->random(),
                'business_address' => 'Purok ' . rand(1, 10),
                'date_registered' => now()->subMonths(rand(1, 24)),
            ]);
        }

        // Health Records
        $healthTitles = ['COVID-19 Vaccine','Flu Vaccine','Prenatal Checkup','Dental Checkup','Blood Pressure Monitoring','Eye Examination','Child Immunization'];
        for ($i = 0; $i < 10; $i++) {
            HealthRecord::create([
                'resident_id' => $residents[array_rand($residents)]->id,
                'record_type' => collect(['vaccination','checkup','health_program'])->random(),
                'title' => $healthTitles[array_rand($healthTitles)],
                'record_date' => now()->subDays(rand(1, 90)),
                'provider' => collect(['San Pablo Health Center','St. Paul Hospital','Barangay Health Station','Community Clinic'])->random(),
                'recorded_by' => User::first()->id,
            ]);
        }

        // Service Requests
        $srSubjects = ['Pothole on Rizal St.','Broken streetlight','Clogged drainage','Garbage collection delay','Flooding issue','Illegal dumping','Noisy construction'];
        for ($i = 0; $i < 6; $i++) {
            ServiceRequest::create([
                'request_number' => ServiceRequest::generateNumber(),
                'requester_name' => $residents[array_rand($residents)]->fullName(),
                'category' => collect(['road_repair','lighting','drainage','garbage','flooding','noise'])->random(),
                'subject' => $srSubjects[array_rand($srSubjects)],
                'description' => 'Service request description for ' . $srSubjects[array_rand($srSubjects)],
                'location' => 'Purok ' . rand(1, 10),
                'priority' => collect(['low','medium','high','urgent'])->random(),
                'status' => collect(['open','in_progress','resolved'])->random(),
                'created_by' => User::first()->id,
            ]);
        }

        // Announcements
        Announcement::create(['title'=>'Barangay Assembly Meeting','content'=>'All residents are invited to attend the regular barangay assembly this Saturday at 2:00 PM. Agenda includes budget review and community projects.','type'=>'meeting','priority'=>'important','publish_date'=>now(),'author_id'=>User::first()->id]);
        Announcement::create(['title'=>'Free Vaccination Drive','content'=>'Free flu vaccination for all senior citizens and children at the Barangay Health Center. Bring your health cards.','type'=>'health','priority'=>'normal','publish_date'=>now(),'author_id'=>User::first()->id]);
        Announcement::create(['title'=>'Emergency Water Interruption','content'=>'Scheduled water maintenance this Thursday from 8 AM to 5 PM. Please store enough water.','type'=>'emergency','priority'=>'urgent','publish_date'=>now(),'author_id'=>User::first()->id]);

        // Revenue
        $categories = ['barangay_clearance','certificate','id_card','business_permit','penalty'];
        for ($i = 0; $i < 8; $i++) {
            $cat = $categories[array_rand($categories)];
            RevenueRecord::create([
                'or_number' => 'OR-2026-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'category' => $cat,
                'description' => 'Payment for ' . str_replace('_', ' ', $cat),
                'payer_name' => $residents[array_rand($residents)]->fullName(),
                'amount' => collect([50, 100, 150, 200, 300, 500])->random(),
                'payment_date' => now()->subDays(rand(1, 60)),
                'payment_method' => collect(['cash','gcash','bank_transfer'])->random(),
                'received_by' => User::first()->id,
            ]);
        }

        // Meeting Minutes
        MeetingMinute::create([
            'meeting_number' => MeetingMinute::generateNumber(),
            'type' => 'regular',
            'meeting_date' => now()->subWeeks(2),
            'venue' => 'Barangay Hall',
            'agenda' => 'Regular session - budget review, community projects, safety concerns',
            'minutes_content' => 'Minutes content for the regular barangay assembly meeting.',
            'attendees' => 'All kagawads and SK representative present. 45 community members attended.',
            'recorded_by' => User::first()->id,
        ]);

        echo "Database seeded successfully!\n";
        echo "Admin: admin@barangay.local / password\n";
        echo "Secretary: secretary@barangay.local / password\n";
        echo "Staff: staff@barangay.local / password\n";
    }
}
