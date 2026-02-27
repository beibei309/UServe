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
                'hfq_category' => 'General & Accounts',
                'hfq_question' => 'Who can use S2U?',
                'hfq_answer' => 'S2U is designed for the UPSI ecosystem. It can be used by UPSI students (as service providers or buyers), UPSI staff, and the surrounding local community members who need ad-hoc services.',
                'hfq_display_order' => 1,
            ],
            [
                'hfq_category' => 'General & Accounts',
                'hfq_question' => 'Is S2U free to use?',
                'hfq_answer' => 'Yes. S2U is completely free to join and browse. There are no hidden platform fees or commissions charged by S2U. You pay the student directly for the service agreed upon.',
                'hfq_display_order' => 2,
            ],
            [
                'hfq_category' => 'General & Accounts',
                'hfq_question' => 'How do I create an account?',
                'hfq_answer' => 'Simply click the "Register" button, enter your email and create a password. If you are a student wanting to offer services, you will need to complete your profile and verify your UPSI student status in the dashboard.',
                'hfq_display_order' => 3,
            ],
            [
                'hfq_category' => 'Services & Requests',
                'hfq_question' => 'What types of services can students offer?',
                'hfq_answer' => 'The sky is the limit! Common services include academic tutoring, graphic design, photography, videography, laptop repair/formatting, translation, running errands, and cleaning. As long as it adheres to university guidelines, it can be offered.',
                'hfq_display_order' => 1,
            ],
            [
                'hfq_category' => 'Services & Requests',
                'hfq_question' => 'How do I request a service?',
                'hfq_answer' => 'Browse the service listings using the search bar or categories. Once you find a provider you like, click "View Details" and use the "Contact" or "Request Service" button to discuss your needs directly.',
                'hfq_display_order' => 2,
            ],
            [
                'hfq_category' => 'Safety & Support',
                'hfq_question' => 'Why was my service banned?',
                'hfq_answer' => 'We prioritize safety. Services are banned if they violate UPSI rules, contain inappropriate content, receive repeated reports from users, or involve unsafe illegal activities. Please review our Community Guidelines.',
                'hfq_display_order' => 1,
            ],
            [
                'hfq_category' => 'Safety & Support',
                'hfq_question' => 'What should I do if I face a problem?',
                'hfq_answer' => 'If you encounter issues with a user or technical problems, please contact our support team immediately via the email below or use the "Report" function on the users profile.',
                'hfq_display_order' => 2,
            ],
        ]);
    }
}
