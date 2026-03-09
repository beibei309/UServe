# UServe API Documentation

## Base URL
```
https://your-domain.com/api/v1
```

## Authentication
Currently, all endpoints are public and do not require authentication. If authentication is needed in the future, we can implement Laravel Sanctum tokens.

## Content Type
All API endpoints return JSON responses with the following content type:
```
Content-Type: application/json
```

---

## Endpoints

### 1. Get Services List

**GET** `/services`

Retrieve a paginated list of all available services with filtering and search capabilities.

#### Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `search` | string | - | Search in service title and description |
| `category_id` | integer | - | Filter by category ID |
| `sort` | string | `newest` | Sort order: `newest`, `oldest`, `price_low`, `price_high`, `rating` |
| `available_only` | boolean | - | Filter by availability: `true` (available only), `false` (unavailable only) |
| `per_page` | integer | 15 | Items per page (max 50) |
| `page` | integer | 1 | Page number |

#### Example Request
```bash
curl -X GET "https://your-domain.com/api/v1/services?search=tutoring&category_id=1&sort=rating&per_page=20&page=1"
```

#### Example Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Mathematics Tutoring",
            "description": "Expert math tutoring for all levels...",
            "status": "available",
            "is_active": true,
            "created_at": "2026-03-01T10:00:00.000000Z",
            "updated_at": "2026-03-05T15:30:00.000000Z",
            "pricing": {
                "basic_price": 25.00,
                "standard_price": 40.00,
                "premium_price": 60.00,
                "suggested_price": 35.00,
                "price_range": "25-60"
            },
            "packages": {
                "basic": {
                    "price": 25.00,
                    "duration": "1 hour",
                    "description": "Basic tutoring session"
                },
                "standard": {
                    "price": 40.00,
                    "duration": "1.5 hours", 
                    "description": "Extended session with materials"
                },
                "premium": {
                    "price": 60.00,
                    "duration": "2 hours",
                    "description": "Premium session with homework help"
                }
            },
            "image": {
                "url": "https://your-domain.com/storage/services/math-tutoring.jpg",
                "fallback": "https://ui-avatars.com/api/?name=Mathematics%20Tutoring"
            },
            "provider": {
                "id": 123,
                "name": "John Doe",
                "role": "helper",
                "avatar_url": "https://your-domain.com/storage/avatars/john.jpg",
                "is_available": true,
                "faculty": "Engineering"
            },
            "category": {
                "id": 1,
                "name": "Academic Tutoring",
                "description": "Educational tutoring services"
            },
            "stats": {
                "reviews_count": 15,
                "average_rating": 4.8,
                "warning_count": 0
            },
            "availability": {
                "status": "available",
                "booking_mode": "scheduled",
                "session_duration": "60",
                "has_operating_hours": true
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 20,
        "total": 45,
        "from": 1,
        "to": 20,
        "has_more_pages": true
    },
    "filters": {
        "search": "tutoring",
        "category_id": 1,
        "sort": "rating",
        "available_only": null
    }
}
```

---

### 2. Get Service Details

**GET** `/services/{id}`

Retrieve detailed information about a specific service.

#### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | Service ID |

#### Example Request
```bash
curl -X GET "https://your-domain.com/api/v1/services/1"
```

#### Example Response
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Mathematics Tutoring",
        "description": "Comprehensive mathematics tutoring covering algebra, calculus, statistics...",
        "status": "available",
        "is_active": true,
        "approval_status": "approved",
        "created_at": "2026-03-01T10:00:00.000000Z",
        "updated_at": "2026-03-05T15:30:00.000000Z",
        "pricing": {
            "basic_price": 25.00,
            "standard_price": 40.00,
            "premium_price": 60.00,
            "suggested_price": 35.00,
            "price_range": "25-60",
            "min_price": 20.00,
            "max_price": 70.00
        },
        "packages": {
            "basic": {
                "price": 25.00,
                "duration": "1 hour",
                "frequency": "weekly",
                "description": "Basic one-on-one tutoring session"
            },
            "standard": {
                "price": 40.00,
                "duration": "1.5 hours",
                "frequency": "bi-weekly", 
                "description": "Extended session with practice materials"
            },
            "premium": {
                "price": 60.00,
                "duration": "2 hours",
                "frequency": "weekly",
                "description": "Premium package with homework assistance"
            }
        },
        "provider": {
            "id": 123,
            "name": "John Doe",
            "role": "helper", 
            "email": "john@example.com",
            "phone": "+60123456789",
            "student_id": "S12345",
            "bio": "Experienced math tutor with 3 years of teaching experience...",
            "faculty": "Engineering",
            "course": "Mechanical Engineering",
            "skills": ["Mathematics", "Physics", "Problem Solving"],
            "avatar_url": "https://your-domain.com/storage/avatars/john.jpg",
            "is_available": true,
            "verification_status": "verified",
            "public_verified_at": "2025-12-01T12:00:00.000000Z",
            "staff_verified_at": "2025-12-01T12:00:00.000000Z",
            "helper_verified_at": "2025-12-01T12:00:00.000000Z",
            "trust_badge": "verified_helper",
            "average_rating": 4.8
        },
        "category": {
            "id": 1,
            "name": "Academic Tutoring",
            "description": "Educational tutoring services"
        },
        "stats": {
            "reviews_count": 15,
            "average_rating": 4.8,
            "completed_orders": 25,
            "warning_count": 0,
            "average_delivery_days": 0
        },
        "availability": {
            "status": "available",
            "booking_mode": "scheduled",
            "session_duration": "60",
            "operating_hours": {
                "mon": {"enabled": true, "start": "09:00", "end": "17:00"},
                "tue": {"enabled": true, "start": "09:00", "end": "17:00"},
                "wed": {"enabled": true, "start": "09:00", "end": "17:00"},
                "thu": {"enabled": true, "start": "09:00", "end": "17:00"},
                "fri": {"enabled": true, "start": "09:00", "end": "17:00"},
                "sat": {"enabled": false, "start": "10:00", "end": "14:00"},
                "sun": {"enabled": false, "start": "10:00", "end": "14:00"}
            },
            "unavailable_dates": ["2026-03-15", "2026-03-20"],
            "blocked_slots": [],
            "booked_appointments": [
                {
                    "date": "2026-03-10",
                    "start_time": "14:00", 
                    "end_time": "15:00",
                    "status": "accepted"
                }
            ]
        },
        "reviews": [
            {
                "id": 1,
                "rating": 5,
                "comment": "Excellent tutor! Very patient and explains concepts clearly.",
                "reply": "Thank you for the feedback!",
                "created_at": "2026-03-01T10:00:00.000000Z",
                "replied_at": "2026-03-01T11:00:00.000000Z",
                "reviewer": {
                    "id": 456,
                    "name": "Jane Smith",
                    "role": "student", 
                    "avatar_url": "https://ui-avatars.com/api/?name=Jane%20Smith"
                }
            }
        ],
        "metadata": {
            "warning_reason": null,
            "work_experience_message": "3 years of tutoring experience in university",
            "has_work_experience_file": true
        }
    }
}
```

---

### 3. Get Categories

**GET** `/categories`

Retrieve all service categories for filtering.

#### Example Request
```bash
curl -X GET "https://your-domain.com/api/v1/categories"
```

#### Example Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Academic Tutoring",
            "description": "Educational tutoring services",
            "created_at": "2026-01-01T00:00:00.000000Z",
            "updated_at": "2026-01-01T00:00:00.000000Z"
        },
        {
            "id": 2, 
            "name": "Tech Support",
            "description": "Computer and technology assistance",
            "created_at": "2026-01-01T00:00:00.000000Z",
            "updated_at": "2026-01-01T00:00:00.000000Z"
        }
    ]
}
```

---

### 4. Health Check

**GET** `/health`

Check API status and connectivity.

#### Example Request
```bash
curl -X GET "https://your-domain.com/api/v1/health"
```

#### Example Response
```json
{
    "status": "ok",
    "timestamp": "2026-03-09T12:00:00.000000Z",
    "version": "1.0.0"
}
```

---

## Error Responses

All endpoints return standardized error responses:

### 404 Not Found
```json
{
    "success": false,
    "message": "Service not found"
}
```

### 500 Internal Server Error
```json
{
    "success": false,
    "message": "Failed to fetch services",
    "error": "Detailed error message"
}
```

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "per_page": ["The per page may not be greater than 50."]
    }
}
```

---

## Rate Limiting

Currently no rate limiting is implemented. If needed, we can add rate limiting per API key or IP address.

---

## Changelog

- **v1.0.0** (2026-03-09): Initial API release with services and categories endpoints
