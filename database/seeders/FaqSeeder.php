<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
    {
        Faq::insert([
            [
                'category' => 'General & Accounts',
                'question' => 'Who can use S2U?',
                'answer' => 'S2U is designed for the UPSI ecosystem. It can be used by UPSI students (as service providers or buyers), UPSI staff, and the surrounding local community members who need ad-hoc services.',
                'display_order' => 1,
            ],
            [
                'category' => 'General & Accounts',
                'question' => 'Is S2U free to use?',
                'answer' => 'Yes. S2U is completely free to join and browse. There are no hidden platform fees or commissions charged by S2U. You pay the student directly for the service agreed upon.',
                'display_order' => 2,
            ],
            [
                'category' => 'General & Accounts',
                'question' => 'How do I create an account?',
                'answer' => 'Simply click the "Register" button, enter your email and create a password. If you are a student wanting to offer services, you will need to complete your profile and verify your UPSI student status in the dashboard.',
                'display_order' => 3,
            ],
            [
                'category' => 'Services & Requests',
                'question' => 'What types of services can students offer?',
                'answer' => 'The sky is the limit! Common services include academic tutoring, graphic design, photography, videography, laptop repair/formatting, translation, running errands, and cleaning. As long as it adheres to university guidelines, it can be offered.',
                'display_order' => 1,
            ],
            [
                'category' => 'Services & Requests',
                'question' => 'How do I request a service?',
                'answer' => 'Browse the service listings using the search bar or categories. Once you find a provider you like, click "View Details" and use the "Contact" or "Request Service" button to discuss your needs directly.',
                'display_order' => 2,
            ],
            [
                'category' => 'Safety & Support',
                'question' => 'Why was my service banned?',
                'answer' => 'We prioritize safety. Services are banned if they violate UPSI rules, contain inappropriate content, receive repeated reports from users, or involve unsafe illegal activities. Please review our Community Guidelines.',
                'display_order' => 1,
            ],
            [
                'category' => 'Safety & Support',
                'question' => 'What should I do if I face a problem?',
                'answer' => 'If you encounter issues with a user or technical problems, please contact our support team immediately via the email below or use the "Report" function on the users profile.',
                'display_order' => 2,
            ],
        ]);
    }
}
