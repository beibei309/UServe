<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('h2u_reviews') || !Schema::hasTable('h2u_student_services') || !Schema::hasTable('h2u_service_requests') || !Schema::hasTable('h2u_users')) {
            return;
        }

        $completedRequests = DB::table('h2u_service_requests')
            ->where('hsr_status', 'completed')
            ->whereNotNull('hsr_student_service_id')
            ->whereNotNull('hsr_requester_id')
            ->whereNotNull('hsr_provider_id')
            ->orderByDesc('hsr_id')
            ->limit(5)
            ->get();

        if ($completedRequests->isEmpty()) {
            return;
        }

        foreach ($completedRequests as $request) {
            $serviceId = (int) $request->hsr_student_service_id;
            $requestId = (int) $request->hsr_id;
            $requesterId = (int) $request->hsr_requester_id;
            $providerId = (int) $request->hsr_provider_id;

            if ($requesterId === $providerId) {
                continue;
            }

            $this->upsertReview(
                $requestId,
                $serviceId,
                $requesterId,
                $providerId,
                5,
                'Great service and smooth communication.'
            );

            $this->upsertReview(
                $requestId,
                $serviceId,
                $providerId,
                $requesterId,
                4,
                'Pleasant client and clear requirements.'
            );
        }
    }

    private function upsertReview(int $requestId, int $serviceId, int $reviewerId, int $revieweeId, int $rating, string $comment): void
    {
        $existing = DB::table('h2u_reviews')
            ->where('hr_service_request_id', $requestId)
            ->where('hr_reviewer_id', $reviewerId)
            ->where('hr_reviewee_id', $revieweeId)
            ->first();

        $payload = [
            'hr_student_service_id' => $serviceId,
            'hr_rating' => $rating,
            'hr_comment' => $comment,
            'hr_reply' => null,
            'hr_replied_at' => null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('h2u_reviews')
                ->where('hr_id', $existing->hr_id)
                ->update($payload);
            return;
        }

        DB::table('h2u_reviews')->insert(array_merge($payload, [
            'hr_service_request_id' => $requestId,
            'hr_reviewer_id' => $reviewerId,
            'hr_reviewee_id' => $revieweeId,
            'created_at' => now(),
        ]));
    }
}