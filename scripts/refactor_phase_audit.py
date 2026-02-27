from __future__ import annotations

import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

TARGETS = {
    "categories": {
        "table_old": "categories",
        "table_new": "h2u_categories",
        "columns": {
            "id": "hc_id",
            "name": "hc_name",
            "slug": "hc_slug",
            "description": "hc_description",
            "image_path": "hc_image_path",
            "icon": "hc_icon",
            "color": "hc_color",
            "is_active": "hc_is_active",
        },
    },
    "student_services": {
        "table_old": "student_services",
        "table_new": "h2u_student_services",
        "columns": {
            "id": "hss_id",
            "user_id": "hss_user_id",
            "category_id": "hss_category_id",
            "booking_mode": "hss_booking_mode",
            "title": "hss_title",
            "image_path": "hss_image_path",
            "description": "hss_description",
            "suggested_price": "hss_suggested_price",
            "status": "hss_status",
            "price_range": "hss_price_range",
            "is_active": "hss_is_active",
            "approval_status": "hss_approval_status",
            "warning_count": "hss_warning_count",
            "warning_reason": "hss_warning_reason",
            "basic_duration": "hss_basic_duration",
            "basic_frequency": "hss_basic_frequency",
            "basic_price": "hss_basic_price",
            "basic_description": "hss_basic_description",
            "standard_duration": "hss_standard_duration",
            "standard_frequency": "hss_standard_frequency",
            "standard_price": "hss_standard_price",
            "standard_description": "hss_standard_description",
            "premium_duration": "hss_premium_duration",
            "premium_frequency": "hss_premium_frequency",
            "premium_price": "hss_premium_price",
            "premium_description": "hss_premium_description",
            "unavailable_dates": "hss_unavailable_dates",
            "operating_hours": "hss_operating_hours",
            "session_duration": "hss_session_duration",
            "blocked_slots": "hss_blocked_slots",
        },
    },
}

INCLUDE_SUFFIX = {".php", ".blade.php", ".js", ".ts"}
IGNORE_DIRS = {"vendor", "node_modules", "storage", "bootstrap/cache", "public/build"}


def should_scan(path: Path) -> bool:
    rel = path.relative_to(ROOT).as_posix()
    for d in IGNORE_DIRS:
        if rel.startswith(d + "/"):
            return False
    return any(rel.endswith(s) for s in INCLUDE_SUFFIX)


def find_hits(text: str, needle: str) -> list[int]:
    # whole-word-ish: avoid partial token matches
    pat = re.compile(rf"(?<![A-Za-z0-9_]){re.escape(needle)}(?![A-Za-z0-9_])")
    return [m.start() for m in pat.finditer(text)]


def line_number_from_offset(text: str, offset: int) -> int:
    return text.count("\n", 0, offset) + 1


def main() -> None:
    report: dict[str, dict] = {}
    files = [p for p in ROOT.rglob("*") if p.is_file() and should_scan(p)]

    for module, cfg in TARGETS.items():
        keys = [cfg["table_old"], *cfg["columns"].keys()]
        module_hits: dict[str, list[dict]] = {k: [] for k in keys}

        for f in files:
            try:
                text = f.read_text(encoding="utf-8", errors="ignore")
            except Exception:
                continue

            rel = f.relative_to(ROOT).as_posix()
            for key in keys:
                offsets = find_hits(text, key)
                if not offsets:
                    continue
                lines = sorted({line_number_from_offset(text, o) for o in offsets})
                module_hits[key].append({"file": rel, "lines": lines[:20], "count": len(offsets)})

        report[module] = {
            "table_old": cfg["table_old"],
            "table_new": cfg["table_new"],
            "columns": cfg["columns"],
            "hits": module_hits,
        }

    out = ROOT / "storage" / "logs" / "refactor_audit_report.json"
    out.parent.mkdir(parents=True, exist_ok=True)
    out.write_text(json.dumps(report, indent=2), encoding="utf-8")
    print(f"Report written: {out}")


if __name__ == "__main__":
    main()
