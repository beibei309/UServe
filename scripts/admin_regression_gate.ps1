$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")

Write-Host "Running admin regression gates..."

$phpPattern = '@php'
$scriptPattern = '<script(?![^>]*\bsrc=)[^>]*>'
$inlineHandlerPattern = '\son[a-zA-Z]+\s*='
$forbiddenDataPattern = 'auth\(\)->|Auth::'
$presentationHelperPattern = 'optional\(|number_format\(|round\(|substr\(|(\\Illuminate\\Support\\)?Str::limit'
$adminBladeFiles = Get-ChildItem -Path "resources/views/admin" -Recurse -Filter "*.blade.php"
$presentationAllowlist = @(
    "resources/views/admin/community/index.blade.php",
    "resources/views/admin/faqs/index.blade.php",
    "resources/views/admin/feedback/index.blade.php",
    "resources/views/admin/reports/index.blade.php",
    "resources/views/admin/requests/index.blade.php",
    "resources/views/admin/services/index.blade.php",
    "resources/views/admin/services/reviews.blade.php",
    "resources/views/admin/services/show.blade.php",
    "resources/views/admin/students/view.blade.php"
)

$phpMatches = Select-String -Path $adminBladeFiles.FullName -Pattern $phpPattern
if ($phpMatches) {
    $phpMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: @php found in admin Blade views."
}

$scriptMatches = Select-String -Path $adminBladeFiles.FullName -Pattern $scriptPattern
if ($scriptMatches) {
    $scriptMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: inline <script> found in admin Blade views."
}

$inlineHandlerMatches = Select-String -Path $adminBladeFiles.FullName -Pattern $inlineHandlerPattern
if ($inlineHandlerMatches) {
    $inlineHandlerMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: inline on* handler found in admin Blade views."
}

$forbiddenDataMatches = Select-String -Path $adminBladeFiles.FullName -Pattern $forbiddenDataPattern
if ($forbiddenDataMatches) {
    $forbiddenDataMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: forbidden data-access helper found in admin Blade views."
}

$presentationHelperMatches = Select-String -Path $adminBladeFiles.FullName -Pattern $presentationHelperPattern
$disallowedPresentationMatches = @($presentationHelperMatches | Where-Object {
    $relativePath = $_.Path.Replace((Get-Location).Path + '\', '').Replace('\', '/')
    $presentationAllowlist -notcontains $relativePath
})
if ($disallowedPresentationMatches.Count -gt 0) {
    $disallowedPresentationMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: presentation helper usage outside admin allowlist."
}

foreach ($allowlistPath in $presentationAllowlist) {
    if (-not (Test-Path $allowlistPath)) {
        throw "Gate failed: admin allowlist file not found: $allowlistPath"
    }
}

$touchedFiles = @()
if ($env:TOUCHED_FILES) {
    $touchedFiles = $env:TOUCHED_FILES -split "`n" | ForEach-Object { $_.Trim() } | Where-Object { $_ }
} else {
    try {
        git rev-parse --is-inside-work-tree *> $null
        if ($LASTEXITCODE -eq 0) {
            git show-ref --verify --quiet refs/remotes/origin/main
            if ($LASTEXITCODE -eq 0) {
                $baseSha = (git merge-base HEAD origin/main).Trim()
                $touchedFiles = git diff --name-only "$baseSha...HEAD"
            } else {
                git rev-parse --verify HEAD~1 *> $null
                if ($LASTEXITCODE -eq 0) {
                    $touchedFiles = git diff --name-only "HEAD~1...HEAD"
                } else {
                    $touchedFiles = git diff --name-only
                }
            }
        }
    } catch {
    }
}

$touchedControllers = $touchedFiles | Where-Object { $_ -match '^app/Http/Controllers/Admin/.*\.php$' }
foreach ($file in $touchedControllers) {
    if (-not (Test-Path $file)) { continue }
    php -l $file
    if ($LASTEXITCODE -ne 0) {
        throw "php -l failed for $file"
    }
}

$touchedModules = $touchedFiles | Where-Object { $_ -match '^public/js/admin-.*\.js$' }
foreach ($file in $touchedModules) {
    if (-not (Test-Path $file)) { continue }
    node --check $file
    if ($LASTEXITCODE -ne 0) {
        throw "node --check failed for $file"
    }
}

php artisan view:clear
if ($LASTEXITCODE -ne 0) { throw "view:clear failed" }
php artisan view:cache
if ($LASTEXITCODE -ne 0) { throw "view:cache failed" }

Write-Host "Admin regression gates passed."
