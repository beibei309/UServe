$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")

Write-Host "Running non-admin regression gates..."

$phpPattern = '@php'
$scriptPattern = '<script(?![^>]*\bsrc=)[^>]*>'
$inlineHandlerPattern = '\son[a-zA-Z]+\s*='
$forbiddenDataPattern = 'auth\(\)->|Auth::'
$presentationHelperPattern = 'optional\(|number_format\(|round\(|substr\(|(\\Illuminate\\Support\\)?Str::limit'
$nonAdminBladeFiles = Get-ChildItem -Path "resources/views" -Recurse -Filter "*.blade.php" | Where-Object { $_.FullName -notmatch '\\admin\\' }
$presentationAllowlist = @(
    "resources/views/about.blade.php",
    "resources/views/emails/service_request.blade.php",
    "resources/views/favorites/index.blade.php",
    "resources/views/onboarding/students_verification.blade.php",
    "resources/views/profile/show-public.blade.php",
    "resources/views/service-requests/helper.blade.php",
    "resources/views/service-requests/index.blade.php",
    "resources/views/service-requests/show.blade.php",
    "resources/views/services/index.blade.php",
    "resources/views/services/manage.blade.php",
    "resources/views/services/show.blade.php",
    "resources/views/students/index.blade.php"
)

$phpMatches = Select-String -Path $nonAdminBladeFiles.FullName -Pattern $phpPattern
if ($phpMatches) {
    $phpMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: @php found in non-admin Blade views."
}

$scriptMatches = Select-String -Path $nonAdminBladeFiles.FullName -Pattern $scriptPattern
if ($scriptMatches) {
    $scriptMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: inline <script> found in non-admin Blade views."
}

$inlineHandlerMatches = Select-String -Path $nonAdminBladeFiles.FullName -Pattern $inlineHandlerPattern
if ($inlineHandlerMatches) {
    $inlineHandlerMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: inline on* handler found in non-admin Blade views."
}

$forbiddenDataMatches = Select-String -Path $nonAdminBladeFiles.FullName -Pattern $forbiddenDataPattern
if ($forbiddenDataMatches) {
    $forbiddenDataMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: forbidden data-access helper found in non-admin Blade views."
}

$presentationHelperMatches = Select-String -Path $nonAdminBladeFiles.FullName -Pattern $presentationHelperPattern
$disallowedPresentationMatches = @($presentationHelperMatches | Where-Object {
    $relativePath = $_.Path.Replace((Get-Location).Path + '\', '').Replace('\', '/')
    $presentationAllowlist -notcontains $relativePath
})
if ($disallowedPresentationMatches.Count -gt 0) {
    $disallowedPresentationMatches | ForEach-Object { Write-Host "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
    throw "Gate failed: presentation helper usage outside non-admin allowlist."
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

$touchedControllers = $touchedFiles | Where-Object {
    (
        (($_ -match '^app/Http/Controllers/.*\.php$') -and ($_ -notmatch '^app/Http/Controllers/Admin/')) -or
        ($_ -match '^app/Providers/.*\.php$') -or
        ($_ -match '^app/Mail/.*\.php$')
    )
}
foreach ($file in $touchedControllers) {
    if (-not (Test-Path $file)) { continue }
    php -l $file
    if ($LASTEXITCODE -ne 0) {
        throw "php -l failed for $file"
    }
}

$touchedModules = $touchedFiles | Where-Object { $_ -match '^public/js/.*\.js$' -and $_ -notmatch '^public/js/admin-' }
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

Write-Host "Non-admin regression gates passed."
