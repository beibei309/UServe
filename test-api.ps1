# API Testing Script
# Save as test-api.ps1 and run with: .\test-api.ps1

Write-Host "🚀 UServe API Testing Script" -ForegroundColor Magenta
Write-Host "================================" -ForegroundColor Magenta

$baseUrl = "http://localhost:8000/api/v1"

function Test-Endpoint {
    param($name, $url)
    try {
        Write-Host "Testing: $name" -ForegroundColor Cyan
        $response = Invoke-RestMethod -Uri $url -Headers @{"Accept"="application/json"}
        Write-Host "✅ SUCCESS" -ForegroundColor Green
        return $response
    } catch {
        Write-Host "❌ FAILED: $($_.Exception.Message)" -ForegroundColor Red
        return $null
    }
}

# Test 1: Health Check
Write-Host "`n1. Health Check Test" -ForegroundColor Yellow
$health = Test-Endpoint "Health" "$baseUrl/health"
if ($health) {
    Write-Host "   Status: $($health.status)" -ForegroundColor Green
    Write-Host "   Version: $($health.version)" -ForegroundColor Green
}

# Test 2: Get Services
Write-Host "`n2. Services List Test" -ForegroundColor Yellow
$services = Test-Endpoint "Services List" "$baseUrl/services?per_page=3"
if ($services -and $services.success) {
    Write-Host "   Found: $($services.data.Count) services" -ForegroundColor Green
    Write-Host "   Total: $($services.pagination.total)" -ForegroundColor Green
    $firstService = $services.data[0]
    if ($firstService) {
        Write-Host "   First Service: $($firstService.title)" -ForegroundColor Blue
        $serviceId = $firstService.id

        # Test 3: Service Details
        Write-Host "`n3. Service Details Test" -ForegroundColor Yellow
        $details = Test-Endpoint "Service Details" "$baseUrl/services/$serviceId"
        if ($details -and $details.success) {
            Write-Host "   Service: $($details.data.title)" -ForegroundColor Green
            Write-Host "   Provider: $($details.data.provider.name)" -ForegroundColor Green
            Write-Host "   Rating: $($details.data.stats.average_rating)" -ForegroundColor Green
            Write-Host "   Reviews: $($details.data.stats.reviews_count)" -ForegroundColor Green
        }
    }
}

# Test 4: Categories
Write-Host "`n4. Categories Test" -ForegroundColor Yellow
$categories = Test-Endpoint "Categories" "$baseUrl/categories"
if ($categories -and $categories.success) {
    Write-Host "   Categories found: $($categories.data.Count)" -ForegroundColor Green
    foreach ($cat in $categories.data) {
        Write-Host "   - $($cat.name)" -ForegroundColor Blue
    }
}

# Test 5: Search & Filter
Write-Host "`n5. Search & Filter Tests" -ForegroundColor Yellow

$searchTests = @(
    @{name="Search 'math'"; url="$baseUrl/services?search=math&per_page=2"},
    @{name="Sort by price"; url="$baseUrl/services?sort=price_low&per_page=2"},
    @{name="Available only"; url="$baseUrl/services?available_only=true&per_page=2"}
)

foreach ($test in $searchTests) {
    $result = Test-Endpoint $test.name $test.url
    if ($result -and $result.success) {
        Write-Host "   $($test.name): $($result.data.Count) results" -ForegroundColor Green
    }
}

# Test 6: Error Handling
Write-Host "`n6. Error Handling Test" -ForegroundColor Yellow
$error = Test-Endpoint "Non-existent Service" "$baseUrl/services/99999"
if (-not $error) {
    Write-Host "   ✅ Properly handles 404 errors" -ForegroundColor Green
}

Write-Host "`n🎉 Testing Complete!" -ForegroundColor Magenta
