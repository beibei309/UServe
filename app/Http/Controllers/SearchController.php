<?php

namespace App\Http\Controllers;

use App\Models\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function services(Request $request): JsonResponse
    {
        $q = $request->string('q')->toString();
        $categoryId = $request->integer('category_id');
        $minRating = $request->integer('min_rating'); // 1-5
        $availableOnly = $request->boolean('available_only', false);

        $query = StudentService::query()
            ->where('hss_is_active', true)
            ->with(['category', 'student' => function ($q) {
                $q->select(['hu_id', 'hu_name', 'hu_role', 'hu_is_available', 'hu_trust_badge', 'hu_average_rating']);
            }]);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('hss_title', 'like', "%$q%")
                    ->orWhere('hss_description', 'like', "%$q%");
            });
        }

        if ($categoryId) {
            $query->where('hss_category_id', $categoryId);
        }

        if ($availableOnly) {
            $query->whereHas('student', function ($sub) {
                $sub->where('hu_is_available', true);
            });
        }

        if ($minRating) {
            // Filter by average rating of the student
            $query->whereRaw('(
                select avg(hr_rating)
                from h2u_reviews r
                where r.hr_reviewee_id = h2u_student_services.hss_user_id
            ) >= ?', [$minRating]);
        }

        $query->orderByDesc('hss_id');

        $services = $query->get();

        $result = $services->map(function ($svc) {
            $student = $svc->student;
            return [
                'id' => $svc->hss_id,
                'title' => $svc->hss_title,
                'description' => $svc->hss_description,
                'suggested_price' => $svc->hss_suggested_price,
                'category' => $svc->category,
                'student' => [
                    'id' => $student->hu_id,
                    'name' => $student->hu_name,
                    'badge' => $student->hu_trust_badge,
                    'is_available' => (bool) $student->hu_is_available,
                    'average_rating' => $student->hu_average_rating,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'services' => $result,
        ], 200);
    }
}