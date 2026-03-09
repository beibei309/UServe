<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->hss_id,
            'title' => $this->hss_title,
            'description' => Str::limit($this->hss_description, 150),
            'status' => $this->hss_status,
            'is_active' => $this->hss_is_active,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Pricing information
            'pricing' => [
                'basic_price' => (float) $this->hss_basic_price,
                'standard_price' => (float) $this->hss_standard_price,
                'premium_price' => (float) $this->hss_premium_price,
                'suggested_price' => (float) $this->hss_suggested_price,
                'price_range' => $this->hss_price_range,
            ],

            // Package information (basic summary)
            'packages' => [
                'basic' => [
                    'price' => (float) $this->hss_basic_price,
                    'duration' => $this->hss_basic_duration,
                    'description' => Str::limit($this->hss_basic_description, 100),
                ],
                'standard' => $this->hss_standard_price ? [
                    'price' => (float) $this->hss_standard_price,
                    'duration' => $this->hss_standard_duration,
                    'description' => Str::limit($this->hss_standard_description, 100),
                ] : null,
                'premium' => $this->hss_premium_price ? [
                    'price' => (float) $this->hss_premium_price,
                    'duration' => $this->hss_premium_duration,
                    'description' => Str::limit($this->hss_premium_description, 100),
                ] : null,
            ],

            // Image information
            'image' => [
                'url' => $this->resolveImageUrl($this->hss_image_path),
                'fallback' => 'https://ui-avatars.com/api/?name=' . urlencode($this->hss_title ?? 'Service'),
            ],

            // Provider information (limited for privacy)
            'provider' => [
                'id' => $this->user->hu_id,
                'name' => $this->user->hu_name,
                'role' => $this->user->hu_role,
                'avatar_url' => $this->user->hu_profile_photo_path
                    ? asset($this->user->hu_profile_photo_path)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($this->user->hu_name),
                'is_available' => $this->user->hu_is_available,
                'faculty' => $this->user->hu_faculty,
            ],

            // Category information
            'category' => [
                'id' => $this->category->hc_id,
                'name' => $this->category->hc_name,
                'description' => $this->category->hc_description,
            ],

            // Statistics
            'stats' => [
                'reviews_count' => $this->reviews_count ?? 0,
                'average_rating' => $this->average_rating ? round($this->average_rating, 1) : 0,
                'warning_count' => $this->hss_warning_count ?? 0,
            ],

            // Availability summary
            'availability' => [
                'status' => $this->hss_status,
                'booking_mode' => $this->hss_booking_mode,
                'session_duration' => $this->hss_session_duration,
                'has_operating_hours' => !empty($this->hss_operating_hours),
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
}
