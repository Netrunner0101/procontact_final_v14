# Pro Contact Design System v2 — The Sophisticated Architect

## Overview

**Version:** 2.0
**Codename:** The Sophisticated Architect
**Previous:** Ocean Breeze (v1)
**Date:** 2026-03-20

This document describes the complete redesign of Pro Contact's visual identity from the "Ocean Breeze" theme to **"The Sophisticated Architect"** — an editorial, high-end CRM experience that bridges startup agility with heritage corporate authority.

> The experience should feel like walking through a modern, glass-walled office in autumn: warm, transparent, and structurally sound.

---

## What Changed

### Color System

The entire color palette was migrated from cool digital tones to a warm, earth-inspired palette:

| Before (Ocean Breeze) | After (Sophisticated Architect) |
|------------------------|--------------------------------|
| `#0f172a` Dark Navy | `#1b1c1a` Warm Charcoal |
| `#1e293b` Slate Dark | `#2c2c2a` Deep Charcoal |
| `#334155` Slate Medium | `#44483e` Olive Gray |
| `#06b6d4` Cyan Accent | `#843728` Terracotta |
| `#8b5cf6` Violet | `#8a6e2e` Refined Gold |
| `#3b82f6` Digital Blue | `#843728` Terracotta Primary |
| `#10b981` Neon Green | `#3a6a3a` Forest Green |
| `#f8fafc` Cool Gray BG | `#fbf9f6` Warm Linen BG |
| `#f1f5f9` Cool Card BG | `#f5f3f0` Warm Stone BG |

### Surface Token System (Material Design 3 Inspired)

| Token | Hex | Usage |
|-------|-----|-------|
| `--surface` | `#fbf9f6` | Main canvas background |
| `--surface-container-low` | `#f5f3f0` | Secondary content blocks |
| `--surface-container` | `#efecea` | Mid-level containers |
| `--surface-container-high` | `#e9e6e3` | Card headers, elevated sections |
| `--surface-container-highest` | `#e3e0dd` | Highest elevation surfaces |
| `--surface-container-lowest` | `#ffffff` | Primary cards, "lifted" feel |

### The "No-Line" Rule

**All `1px solid` borders have been removed.** Visual boundaries are now defined through:

1. **Tonal Shifts** — adjacent surfaces use different surface tokens
2. **Ambient Shadows** — soft, warm-toned shadows derived from `#1b1c1a`
3. **Ghost Borders** — `outline-variant` at max 15% opacity (fallback only)

### Typography

| Role | Font | Notes |
|------|------|-------|
| Display & Headlines | `Manrope` | Geometric precision, modern corporate-cool |
| Body & Labels | `Inter` | High readability workhorse |

Replaced `Plus Jakarta Sans` entirely.

---

## Files Modified

### Core Layout & Navigation
- `resources/views/layouts/app.blade.php` — Glass navigation bar, warm surface backgrounds
- `resources/views/navigation-menu.blade.php` — Glassmorphism nav, terracotta accents

### Dashboard & Statistics
- `resources/views/dashboard.blade.php` — Full color migration, tonal card system
- `resources/views/livewire/statistics-dashboard.blade.php` — Charts, stat cards, controls restyled
- `resources/views/livewire/activity-view.blade.php` — Activity timeline, stat panels

### CRM Views
- `resources/views/livewire/contact-index.blade.php` — Contact list, filters, action buttons
- `resources/views/livewire/contact-show.blade.php` — Contact detail view
- `resources/views/livewire/company-index.blade.php` — Company list view
- `resources/views/livewire/company-show.blade.php` — Company detail view
- `resources/views/livewire/deal-index.blade.php` — Deal pipeline
- `resources/views/livewire/deal-show.blade.php` — Deal detail view

### Components
- `resources/views/livewire/quick-contact-form.blade.php` — Quick-add form styling

### Design Documentation
- `DESIGN_SYSTEM.md` — Full design system specification (root)
- `MD/design_v2.md` — This file

---

## Component Patterns

### Primary CTA (Terracotta Button)
```css
background: linear-gradient(135deg, #843728, #ffdbd1);
color: #ffffff;
border-radius: 0.375rem;
font-weight: 600;
letter-spacing: 0.02em;
```

### Glass Navigation
```css
background: rgba(255, 255, 255, 0.7);
backdrop-filter: blur(12px);
border-bottom: 1px solid rgba(197, 200, 185, 0.10);
```

### CRM Data Cards
- Border radius: `0.75rem` (xl)
- No divider lines between data rows
- Header uses `#e9e6e3` (surface-container-high) for natural visual break
- Spacing: `1.4rem` between data sets

### Chips & Status Tags
- **Action Chips:** `#f5dfd0` bg, `#281810` text, pill radius
- **Golden Leads:** `#ffdfa0` bg (tertiary-container)
- **At Risk:** `#ffdad6` bg (error-container)

---

## Elevation Strategy

Depth through **tonal layering**, not drop shadows:

```
#fbf9f6 (surface) → #f5f3f0 (low) → #efecea (mid) → #e9e6e3 (high)
```

Shadows reserved for floating elements only:
```css
/* Modal */     box-shadow: 0 20px 40px rgba(27, 28, 26, 0.05);
/* Hover lift */ box-shadow: 0 12px 24px rgba(27, 28, 26, 0.04);
/* Subtle card */ box-shadow: 0 4px 12px rgba(27, 28, 26, 0.03);
```

---

## Semantic Color Mapping

| Purpose | Color | Hex |
|---------|-------|-----|
| Success | Forest Green | `#3a6a3a` |
| Warning | Refined Gold | `#8a6e2e` |
| Error/Danger | Deep Red | `#ba1a1a` |
| Info/Primary | Terracotta | `#843728` |

---

## Design Principles (Do's & Don'ts)

### Do
- Use asymmetrical layouts for modern energy
- Rely on generous spacing (`3rem`+) to let the design breathe
- Use tonal shifts between nested containers
- Use `#1b1c1a` (Warm Charcoal) for text — never pure black

### Don't
- Use `1px solid` borders for sectioning
- Use `#000000` text anywhere
- Use card-in-card layouts with borders
- Use digital blue (`#3b82f6`) or cyan (`#06b6d4`)
- Use heavy Material Design shadows

---

*Design System v2.0 — The Sophisticated Architect*
*Pro Contact CRM — 2026*
