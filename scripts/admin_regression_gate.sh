#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "Running admin regression gates..."

mapfile -t ADMIN_BLADE_FILES < <(find resources/views/admin -type f -name '*.blade.php')

if grep -En '@php' "${ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: @php found in admin Blade views."
  exit 1
fi

if grep -EnP '<script(?![^>]*\bsrc=)[^>]*>' "${ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: inline <script> found in admin Blade views."
  exit 1
fi

if grep -En '\son[a-zA-Z]+\s*=' "${ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: inline on* handler found in admin Blade views."
  exit 1
fi

if grep -En 'auth\(\)->|Auth::' "${ADMIN_BLADE_FILES[@]}"; then
  echo "Gate failed: forbidden data-access helper found in admin Blade views."
  exit 1
fi

PRESENTATION_ALLOWLIST=(
  "resources/views/admin/community/index.blade.php"
  "resources/views/admin/faqs/index.blade.php"
  "resources/views/admin/feedback/index.blade.php"
  "resources/views/admin/reports/index.blade.php"
  "resources/views/admin/requests/index.blade.php"
  "resources/views/admin/services/index.blade.php"
  "resources/views/admin/services/reviews.blade.php"
  "resources/views/admin/services/show.blade.php"
  "resources/views/admin/students/view.blade.php"
)
for file in "${ADMIN_BLADE_FILES[@]}"; do
  if grep -En 'optional\(|number_format\(|round\(|substr\(|(\\Illuminate\\Support\\)?Str::limit' "$file" > /dev/null; then
    if [[ ! " ${PRESENTATION_ALLOWLIST[*]} " =~ " ${file} " ]]; then
      grep -En 'optional\(|number_format\(|round\(|substr\(|(\\Illuminate\\Support\\)?Str::limit' "$file"
      echo "Gate failed: presentation helper usage outside admin allowlist."
      exit 1
    fi
  fi
done
for file in "${PRESENTATION_ALLOWLIST[@]}"; do
  if [[ ! -f "$file" ]]; then
    echo "Gate failed: admin allowlist file not found: $file"
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

TOUCHED_CONTROLLERS="$(printf '%s\n' "$TOUCHED_FILES" | grep -E '^app/Http/Controllers/Admin/.*\.php$' || true)"
if [[ -n "$TOUCHED_CONTROLLERS" ]]; then
  while IFS= read -r file; do
    [[ -z "$file" ]] && continue
    [[ ! -f "$file" ]] && continue
    php -l "$file"
  done <<< "$TOUCHED_CONTROLLERS"
fi

TOUCHED_MODULES="$(printf '%s\n' "$TOUCHED_FILES" | grep -E '^public/js/admin-.*\.js$' || true)"
if [[ -n "$TOUCHED_MODULES" ]]; then
  while IFS= read -r file; do
    [[ -z "$file" ]] && continue
    [[ ! -f "$file" ]] && continue
    node --check "$file"
  done <<< "$TOUCHED_MODULES"
fi

php artisan view:clear
php artisan view:cache

echo "Admin regression gates passed."
