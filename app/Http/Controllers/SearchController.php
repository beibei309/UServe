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
            ->where('is_active', true)
            ->with(['category', 'student' => function ($q) {
                $q->select(['id', 'name', 'role', 'is_available']);
            }]);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($availableOnly) {
            $query->whereHas('student', function ($sub) {
                $sub->where('is_available', true);
            });
        }

        if ($minRating) {
            // Filter by average rating of the student
            $query->whereRaw('(
                select avg(rating)
                from reviews r
                where r.reviewee_id = student_services.user_id
            ) >= ?', [$minRating]);
        }

        $query->orderByDesc('id');

        $services = $query->get();

        $result = $services->map(function ($svc) {
            $student = $svc->student;
            return [
                'id' => $svc->id,
                'title' => $svc->title,
                'description' => $svc->description,
                'suggested_price' => $svc->suggested_price,
                'category' => $svc->category,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'badge' => $student->trust_badge,
                    'is_available' => (bool) $student->is_available,
                    'average_rating' => $student->average_rating,
                ],
            ];
        });

        return response()->json(['services' => $result], 200);
    }
}