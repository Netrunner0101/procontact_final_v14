# Pro Contact Design System - The Sophisticated Architect

## 1. Overview & Creative North Star

The creative North Star for this design system is **"The Sophisticated Architect."** This concept bridges the agility of a modern startup with the unwavering authority of a heritage corporate firm.

We moved away from the "SaaS-in-a-box" aesthetic (Ocean Breeze theme) — characterized by harsh borders and generic blue/cyan hues — toward an **editorial, high-end CRM experience**.

The visual signature is defined by **Intentional Asymmetry** and **Tonal Depth**. Rather than rigid, centered grids, we utilize generous white space and overlapping elements to create a sense of bespoke craftsmanship.

> The experience should feel like walking through a modern, glass-walled office in autumn: warm, transparent, and structurally sound.

---

## 2. Color Palette

### Surface Tokens (Material Design 3 Inspired)

| Token | Hex | Usage |
|-------|-----|-------|
| `--surface` | `#fbf9f6` | Main canvas background |
| `--surface-container-low` | `#f5f3f0` | Secondary content blocks |
| `--surface-container` | `#efecea` | Mid-level containers |
| `--surface-container-high` | `#e9e6e3` | Card headers, elevated sections |
| `--surface-container-highest` | `#e3e0dd` | Highest elevation surfaces |
| `--surface-container-lowest` | `#ffffff` | Primary cards, "lifted" feel |
| `--on-surface` | `#1b1c1a` | Primary text (Warm Charcoal, never 100% black) |
| `--on-surface-variant` | `#44483e` | Secondary text |

### Primary (Terracotta)

| Token | Hex | Usage |
|-------|-----|-------|
| `--primary` | `#843728` | Primary buttons, CTAs |
| `--on-primary` | `#ffffff` | Text on primary |
| `--primary-container` | `#ffdbd1` | Primary container backgrounds |
| `--on-primary-container` | `#341100` | Text on primary container |
| `--primary-fixed-dim` | `#c4816e` | Hover states on dark elements |

### Secondary (Warm Stone)

| Token | Hex | Usage |
|-------|-----|-------|
| `--secondary` | `#6f5d4e` | Secondary elements |
| `--on-secondary` | `#ffffff` | Text on secondary |
| `--secondary-container` | `#f5dfd0` | Chip/tag backgrounds |
| `--on-secondary-container` | `#281810` | Text on chips/tags |

### Tertiary (Refined Gold)

| Token | Hex | Usage |
|-------|-----|-------|
| `--tertiary` | `#8a6e2e` | Golden Leads, premium micro-interactions |
| `--on-tertiary` | `#ffffff` | Text on tertiary |
| `--tertiary-container` | `#ffdfa0` | Golden status tags |
| `--on-tertiary-container` | `#2d2000` | Text on tertiary container |

### Semantic Colors

| Token | Hex | Usage |
|-------|-----|-------|
| `--success` | `#3a6a3a` | Success states |
| `--success-container` | `#c0f0b8` | Success backgrounds |
| `--warning` | `#8a6e2e` | Warning states |
| `--warning-container` | `#ffdfa0` | Warning backgrounds |
| `--error` | `#ba1a1a` | Error/danger states |
| `--error-container` | `#ffdad6` | Error backgrounds, "At Risk" tags |
| `--on-error-container` | `#410002` | Text on error container |

### Outline

| Token | Hex | Usage |
|-------|-----|-------|
| `--outline` | `#75786c` | Standard outlines (use sparingly) |
| `--outline-variant` | `#c5c8b9` | Ghost borders at 10-15% opacity |

---

## 3. The "No-Line" Rule

**1px solid borders are strictly prohibited** for sectioning or container definition.

Boundaries must be defined through:

1. **Tonal Shifts:** Placing a `surface-container-low` section against a `surface` background
2. **Organic Shadows:** Using ambient light rather than structural lines
3. **Ghost Borders (fallback):** `outline-variant` at **15% opacity** maximum

```css
/* DO */
.card { background: var(--surface-container-lowest); }
.card-header { background: var(--surface-container-high); }

/* DON'T */
.card { border: 1px solid #e2e8f0; }
.card-header { border-bottom: 1px solid #ccc; }
```

---

## 4. Typography

### Font Stack

- **Display & Headlines:** `Manrope` — geometric precision, modern "corporate-cool"
- **Body & Labels:** `Inter` — high readability workhorse

### Scale

| Token | Size | Weight | Letter-Spacing | Usage |
|-------|------|--------|-----------------|-------|
| `display-lg` | 3.5rem | 800 | -0.02em | Hero headlines |
| `display-md` | 2.5rem | 700 | -0.02em | Page titles |
| `headline-lg` | 2rem | 700 | -0.01em | Section headers |
| `headline-md` | 1.5rem | 600 | -0.01em | Card titles |
| `title-lg` | 1.25rem | 600 | 0 | Sub-headers |
| `title-md` | 1rem | 600 | 0 | Component titles |
| `body-lg` | 1rem | 400 | 0.01em | Lead paragraphs |
| `body-md` | 0.875rem | 400 | 0.01em | Standard body text |
| `body-sm` | 0.8125rem | 400 | 0.02em | Secondary text |
| `label-lg` | 0.875rem | 600 | 0.02em | Button labels |
| `label-md` | 0.75rem | 600 | 0.05em | Data labels (uppercase) |
| `label-sm` | 0.6875rem | 500 | 0.05em | Captions |

---

## 5. Elevation & Depth

### Tonal Layering (Primary Method)

Depth is a feeling, not a feature. Use background color differences to create hierarchy:

```
surface (#fbf9f6) < surface-container-low (#f5f3f0) < surface-container (#efecea)
```

A `surface-container-highest` card placed on a `surface` background creates a natural focal point **without any CSS shadow**.

### Ambient Shadows (Floating Elements Only)

```css
/* Modal / floating panel */
box-shadow: 0 20px 40px rgba(27, 28, 26, 0.05);

/* Hover state lift */
box-shadow: 0 12px 24px rgba(27, 28, 26, 0.04);

/* Subtle card */
box-shadow: 0 4px 12px rgba(27, 28, 26, 0.03);
```

Shadow color must be derived from `--on-surface` (#1b1c1a), **never pure black**.

---

## 6. Border Radius

| Token | Value | Usage |
|-------|-------|-------|
| `--radius-sm` | 0.25rem (4px) | Small elements, inputs |
| `--radius-md` | 0.375rem (6px) | Buttons (Primary CTA) |
| `--radius-lg` | 0.5rem (8px) | Secondary containers |
| `--radius-xl` | 0.75rem (12px) | Main dashboard cards |
| `--radius-2xl` | 1rem (16px) | Large panels |
| `--radius-full` | 9999px | Chips, pills, avatars |

---

## 7. Spacing Scale

| Token | Value | Usage |
|-------|-------|-------|
| `1` | 0.25rem | Micro spacing |
| `2` | 0.5rem | Tight spacing |
| `3` | 0.75rem | Compact spacing |
| `4` | 1.4rem | Between data sets in cards |
| `6` | 1.5rem | Standard padding |
| `8` | 2rem | Section spacing |
| `12` | 3rem | Major section gaps |
| `16` | 4rem | Page-level breathing room |
| `24` | 8.5rem | Generous white space |

---

## 8. Components

### Primary CTA (Terracotta Button)

```css
.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-container));
    color: var(--on-primary);
    border-radius: var(--radius-md);  /* 0.375rem */
    font-weight: 600;
    letter-spacing: 0.02em;
}
```

### Glass Navigation Bar

```css
nav {
    background: rgba(255, 255, 255, 0.7);  /* surface-container-lowest at 70% */
    backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(197, 200, 185, 0.10);  /* outline-variant at 10% */
}
```

### CRM Data Cards

- **Radius:** Always `xl` (0.75rem)
- **No divider lines** between data rows
- **Spacing:** `4` (1.4rem) between data sets
- **Header:** `surface-container-high` background for natural break

### Chips & Tags

- **Action Chips:** `secondary-container` bg, `on-secondary-container` text, `full` radius
- **Status: Golden Leads:** `tertiary-container` bg
- **Status: At Risk:** `error-container` bg

---

## 9. Glassmorphism (The "Glass & Gold" Rule)

For floating elements (nav bars, hover-state cards):

```css
.glass {
    background: rgba(251, 249, 246, 0.80);  /* surface at 80% */
    backdrop-filter: blur(20px);
}
```

Accents of `tertiary` (Refined Gold) should be used **sparingly** — only for high-value micro-interactions or premium status indicators.

---

## 10. Do's and Don'ts

### Do

- Use asymmetrical layouts for "Modern Startup" energy
- Use `primary-fixed-dim` for hover states on dark elements
- Rely on spacing `12` and `16` to let the design breathe
- Use tonal shifts between nested containers
- Use `on-surface` (#1b1c1a) for all text — never 100% black

### Don't

- Use 1px solid borders for sectioning (No-Line Rule)
- Use 100% black (#000) text anywhere
- Use "Card-in-Card" layouts with borders
- Use standard Material Design heavy shadows
- Use digital blue (#3b82f6) or cyan (#06b6d4) as primary colors

---

## 11. Migration from Ocean Breeze

| Ocean Breeze | Sophisticated Architect |
|-------------|------------------------|
| `#0f172a` (Dark Navy) | `#1b1c1a` (Warm Charcoal) |
| `#06b6d4` (Cyan accent) | `#843728` (Terracotta) |
| `#8b5cf6` (Violet) | `#8a6e2e` (Refined Gold) |
| `#f8fafc` (Cool gray bg) | `#fbf9f6` (Warm linen bg) |
| `1px solid #e2e8f0` | Tonal shift or ghost border |
| `Plus Jakarta Sans` | `Manrope` + `Inter` |
| Drop shadows | Tonal layering |
| Gradient mesh bg | Clean warm surface |

---

*Last updated: 2026-03-20*
*Theme: The Sophisticated Architect v1.0*
