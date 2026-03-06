#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "Running non-admin regression gates..."

mapfile -t NON_ADMIN_BLADE_FILES < <(find resources/views -type f -name '*.blade.php' ! -path 'resources/views/admin/*')

if grep -En '@php' "${NON_ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: @php found in non-admin Blade views."
  exit 1
fi

if grep -EnP '<script(?![^>]*\bsrc=)[^>]*>' "${NON_ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: inline <script> found in non-admin Blade views."
  exit 1
fi

if grep -En '\son[a-zA-Z]+\s*=' "${NON_ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: inline on* handler found in non-admin Blade views."
  exit 1
fi

if grep -En 'auth\(\)->|Auth::' "${NON_ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: forbidden data-access helper found in non-admin Blade views."
  exit 1
fi

PRESENTATION_ALLOWLIST=(
  "resources/views/about.blade.php"
  "resources/views/emails/service_request.blade.php"
  "resources/views/favorites/index.blade.php"
  "resources/views/onboarding/students_verification.blade.php"
  "resources/views/profile/show-public.blade.php"
  "resources/views/service-requests/helper.blade.php"
  "resources/views/service-requests/index.blade.php"
  "resources/views/service-requests/show.blade.php"
  "resources/views/services/index.blade.php"
  "resources/views/services/manage.blade.php"
  "resources/views/services/show.blade.php"
  "resources/views/students/index.blade.php"
)
for file in "${NON_ADMIN_BLADE_FILES[@]}"; do
  if grep -En 'optional\(|number_format\(|round\(|substr\(|(\\Illuminate\\Support\\)?Str::limit' "$file" > /dev/null; then
    if [[ ! " ${PRESENTATION_ALLOWLIST[*]} " =~ " ${file} " ]]; then
      grep -En 'optional\(|number_format\(|round\(|substr\(|(\\Illuminate\\Support\\)?Str::limit' "$file"
      echo "Gate failed: presentation helper usage outside non-admin allowlist."
      exit 1
    fi
  fi
done
for file in "${PRESENTATION_ALLOWLIST[@]}"; do
  if [[ ! -f "$file" ]]; then
    echo "Gate failed: non-admin allowlist file not found: $file"
    exit 1
  fi
done

TOUCHED_FILES="${TOUCHED_FILES:-}"
if [[ -z "$TOUCHED_FILES" ]]; then
  if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    if git show-ref --verify --quiet refs/remotes/origin/main; then
      BASE_SHA="$(git merge-base HEAD origin/main)"
      TOUCHED_FILES="$(git diff --name-only "$BASE_SHA"...HEAD)"
    elif git rev-parse --verify HEAD~1 >/dev/null 2>&1; then
      TOUCHED_FILES="$(git diff --name-only HEAD~1...HEAD)"
    else
      TOUCHED_FILES="$(git diff --name-only)"
    fi
  fi
fi

TOUCHED_CONTROLLERS="$(printf '%s\n' "$TOUCHED_FILES" | grep -E '^app/Http/Controllers/.*\.php$|^app/Providers/.*\.php$|^app/Mail/.*\.php$' | grep -Ev '^app/Http/Controllers/Admin/' || true)"
if [[ -n "$TOUCHED_CONTROLLERS" ]]; then
  while IFS= read -r file; do
    [[ -z "$file" ]] && continue
    [[ ! -f "$file" ]] && continue
    php -l "$file"
  done <<< "$TOUCHED_CONTROLLERS"
fi

TOUCHED_MODULES="$(printf '%s\n' "$TOUCHED_FILES" | grep -E '^public/js/.*\.js$' | grep -Ev '^public/js/admin-' || true)"
if [[ -n "$TOUCHED_MODULES" ]]; then
  while IFS= read -r file; do
    [[ -z "$file" ]] && continue
    [[ ! -f "$file" ]] && continue
    node --check "$file"
  done <<< "$TOUCHED_MODULES"
fi

php artisan view:clear
php artisan view:cache

echo "Non-admin regression gates passed."
