<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\ServiceRequest;

class ServiceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Calculate additional statistics
        $orders = ServiceRequest::where('hsr_student_service_id', $this->hss_id)
            ->whereIn('hsr_status', ['completed', 'accepted'])
            ->get();

        $completedOrders = $this->orders()
            ->whereIn('hsr_status', ['completed', 'accepted'])
            ->count();

        return [
            'id' => $this->hss_id,
            'title' => $this->hss_title,
            'description' => $this->hss_description,
            'status' => $this->hss_status,
            'is_active' => $this->hss_is_active,
            'approval_status' => $this->hss_approval_status,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Complete pricing information
            'pricing' => [
                'basic_price' => (float) $this->hss_basic_price,
                'standard_price' => (float) $this->hss_standard_price,
                'premium_price' => (float) $this->hss_premium_price,
                'suggested_price' => (float) $this->hss_suggested_price,
                'price_range' => $this->hss_price_range,
                'min_price' => $orders->min('hsr_offered_price') ?? 0,
                'max_price' => $orders->max('hsr_offered_price') ?? 0,
            ],

            // Detailed package information
            'packages' => [
                'basic' => [
                    'price' => (float) $this->hss_basic_price,
                    'duration' => $this->hss_basic_duration,
                    'frequency' => $this->hss_basic_frequency,
                    'description' => $this->hss_basic_description,
                ],
                'standard' => $this->hss_standard_price ? [
                    'price' => (float) $this->hss_standard_price,
                    'duration' => $this->hss_standard_duration,
                    'frequency' => $this->hss_standard_frequency,
                    'description' => $this->hss_standard_description,
                ] : null,
                'premium' => $this->hss_premium_price ? [
                    'price' => (float) $this->hss_premium_price,
                    'duration' => $this->hss_premium_duration,
                    'frequency' => $this->hss_premium_frequency,
                    'description' => $this->hss_premium_description,
                ] : null,
            ],

            // Image information
            'image' => [
                'url' => $this->resolveImageUrl($this->hss_image_path),
                'fallback' => 'https://ui-avatars.com/api/?name=' . urlencode($this->hss_title ?? 'Service'),
            ],

            // Complete provider information
            'provider' => [
                'id' => $this->user->hu_id,
                'name' => $this->user->hu_name,
                'role' => $this->user->hu_role,
                'email' => $this->user->hu_email,
                'phone' => $this->user->hu_phone,
                'student_id' => $this->user->hu_student_id,
                'bio' => $this->user->hu_bio,
                'faculty' => $this->user->hu_faculty,
                'course' => $this->user->hu_course,
                'skills' => $this->user->skills,
                'avatar_url' => $this->user->hu_profile_photo_path
                    ? asset($this->user->hu_profile_photo_path)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($this->user->hu_name),
                'is_available' => $this->user->hu_is_available,
                'verification_status' => $this->user->hu_verification_status,
                'public_verified_at' => $this->user->hu_public_verified_at,
                'staff_verified_at' => $this->user->hu_staff_verified_at,
                'helper_verified_at' => $this->user->hu_helper_verified_at,
                'trust_badge' => $this->user->trust_badge,
                'average_rating' => $this->user->average_rating,
            ],

            // Category information
            'category' => [
                'id' => $this->category->hc_id,
                'name' => $this->category->hc_name,
                'description' => $this->category->hc_description,
            ],

            // Detailed statistics
            'stats' => [
                'reviews_count' => $this->reviews_count ?? 0,
                'average_rating' => $this->average_rating ? round($this->average_rating, 1) : 0,
                'completed_orders' => $completedOrders,
                'warning_count' => $this->hss_warning_count ?? 0,
                'average_delivery_days' => $this->calculateAverageDeliveryDays($orders),
            ],

            // Complete availability information
            'availability' => [
                'status' => $this->hss_status,
                'booking_mode' => $this->hss_booking_mode,
                'session_duration' => $this->hss_session_duration,
                'operating_hours' => $this->hss_operating_hours,
                'unavailable_dates' => $this->hss_unavailable_dates,
                'blocked_slots' => $this->hss_blocked_slots,
                'booked_appointments' => $this->getBookedAppointments(),
            ],

            // Reviews
            'reviews' => $this->reviews->map(function($review) {
                return [
                    'id' => $review->hr_id,
                    'rating' => $review->hr_rating,
                    'comment' => $review->hr_comment,
                    'reply' => $review->hr_reply,
                    'created_at' => $review->hr_created_at ? Carbon::parse($review->hr_created_at)->toISOString() : null,
                    'replied_at' => $review->hr_replied_at ? Carbon::parse($review->hr_replied_at)->toISOString() : null,
                    'reviewer' => [
                        'id' => $review->reviewer->hu_id,
                        'name' => $review->reviewer->hu_name,
                        'role' => $review->reviewer->hu_role,
                        'avatar_url' => $review->reviewer->hu_profile_photo_path
                            ? asset($review->reviewer->hu_profile_photo_path)
                            : 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewer->hu_name),
                    ],
                ];
            }),

            // Additional metadata
            'metadata' => [
                'warning_reason' => $this->hss_warning_reason,
                'work_experience_message' => $this->user->hu_work_experience_message,
                'has_work_experience_file' => !empty($this->user->hu_work_experience_file),
            ],
        ];
    }

    /**
     * Resolve the image URL with proper fallbacks
     */
    private function resolveImageUrl(?string $path): ?string
    {
        if (!$path) return null;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Calculate average delivery time in days
     */
    private function calculateAverageDeliveryDays($orders): int
    {
        if ($orders->isEmpty()) return 0;

        return (int) $orders->avg(function($order) {
            $rawDate = $order->hsr_selected_dates;
            $date = is_array($rawDate) ? ($rawDate[0] ?? now()->toDateString()) : $rawDate;
            return Carbon::parse($date)->diffInDays(now());
        });
    }

    /**
     * Get currently booked appointment slots
     */
    private function getBookedAppointments(): array
    {
        return ServiceRequest::where('hsr_student_service_id', $this->hss_id)
            ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress', 'approved'])
            ->get()
            ->map(function ($appointment) {
                $rawDate = $appointment->hsr_selected_dates;
                return [
                    'date' => is_array($rawDate) ? ($rawDate[0] ?? null) : $rawDate,
                    'start_time' => substr((string) $appointment->hsr_start_time, 0, 5),
                    'end_time' => substr((string) $appointment->hsr_end_time, 0, 5),
                    'status' => $appointment->hsr_status,
                ];
            })
            ->toArray();
    }
}
