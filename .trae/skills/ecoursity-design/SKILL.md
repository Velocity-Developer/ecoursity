---
name: "ecoursity-design"
description: "Starbucks-inspired design system for Ecoursity UI — warm cream canvas, SoDoSans/Inter type, pill-shaped buttons, four-tier green system, and gold rewards accents."
---

# Ecoursity Design System

Starbucks-inspired design language — a warm, confident retail UI on a cream canvas with four calibrated green surfaces, pill buttons with `scale(0.95)` active micro-interaction, tight `-0.01em` tracking, and gold reserved for rewards-status ceremony.

**CSS file rules:**
- **Class umum / design tokens** → `../assets/css/ecoursity-main.css`
- **Khusus dashboard wp-admin** → `../assets/css/ecoursity-admin.css`
- **Khusus halaman public** → `../assets/css/ecoursity-public.css`
- **CSS variables (`--ecoursity-*`)** sudah didefinisikan di `ecoursity-main.css` — tambah component/utility CSS baru di bawah block `:root`.

**Gunakan Bahasa Indonesia untuk seluruh teks antarmuka — label, tombol, heading, pesan, dll. Hanya kode dan token desain yang tetap dalam bahasa Inggris.**

## Colors

### Primary Greens — Four-Tier System

| Token | Value | Usage |
|-------|-------|-------|
| `--ecoursity-green-starbucks` | `#006241` | h1 headings, primary section headers, main brand signal |
| `--ecoursity-green-accent` | `#00754A` | Filled CTAs, floating Frap button, primary action |
| `--ecoursity-green-house` | `#1E3932` | Footer surface, feature bands, dark sections |
| `--ecoursity-green-uplift` | `#2b5148` | Decorative accents, dark-gradient moments |
| `--ecoursity-green-light` | `#d4e9e2` | Form valid-state tint, light utility surfaces |

### Rewards Accent

| Token | Value | Usage |
|-------|-------|-------|
| `--ecoursity-gold` | `#cba258` | Rewards-status ceremony — Gold tier, partnership badges |
| `--ecoursity-gold-light` | `#dfc49d` | Gold background washes on gold-tier sections |
| `--ecoursity-gold-lightest` | `#faf6ee` | Cream-gold page wash under partnership sections |

### Surface & Background

| Token | Value | Usage |
|-------|-------|-------|
| `--ecoursity-canvas-white` | `#ffffff` | Cards, modal surface, gift-card tiles |
| `--ecoursity-canvas-cream` | `#f2f0eb` | Primary page canvas — warm cream signature |
| `--ecoursity-canvas-ceramic` | `#edebe9` | Zone separators, soft page-section washes |
| `--ecoursity-canvas-neutral` | `#f9f9f9` | Dropdown menus, form-card wraps, utility containers |

### Neutrals & Text

| Token | Value | Usage |
|-------|-------|-------|
| `--ecoursity-text-black` | `rgba(0,0,0,0.87)` | Primary heading/body on light surfaces |
| `--ecoursity-text-soft` | `rgba(0,0,0,0.58)` | Secondary/metadata text on light surfaces |
| `--ecoursity-text-white` | `rgba(255,255,255,1)` | Heading/body on dark green surfaces |
| `--ecoursity-text-white-soft` | `rgba(255,255,255,0.70)` | Secondary text on dark-green |
| `--ecoursity-rewards-green` | `#33433d` | Rewards-page text blocks |
| `--ecoursity-ink` | `#000000` | High-contrast CTA strip, top-nav sign-in |

### Semantic

| Token | Value | Usage |
|-------|-------|-------|
| `--ecoursity-red` | `#c82014` | Error, destructive state |
| `--ecoursity-yellow` | `#fbbc05` | Warning state |
| `--ecoursity-hairline` | `#e4e4e7` | Card borders, dividers |
| `--ecoursity-input-border` | `#d6dbde` | Form input borders |

## Typography

Font stack:
- **Primary**: `"Inter Variable", "Inter", "Helvetica Neue", "Helvetica", "Arial", sans-serif` (SoDoSans substitute — Inter)
- **Rewards Serif**: `"Lora", "Iowan Old Style", Georgia, serif` — for Rewards editorial headline moments
- **Code**: `"ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", monospace`

Global: `letter-spacing: -0.01em` — the brand's tight, confident tracking signature.

| Token | Size | Weight | Line H | Letter Sp | Use |
|-------|------|--------|--------|-----------|-----|
| `display-xl` | 80px | 500 | 1.2 | -0.01em | Largest hero display |
| `display-lg` | 58px | 500 | 1.2 | -0.01em | Secondary hero heading |
| `display-md` | 45px | 500 | 1.2 | -0.01em | Landing section headline |
| `heading-xl` | 28px | 600 | 1.5 | -0.01em | Page title / Starbucks-green h1 |
| `heading-lg` | 24px | 400 | 1.5 | -0.01em | Section title in Text Black |
| `heading-md` | 20px | 500 | 1.4 | -0.01em | Sub-heading |
| `heading-sm` | 18px | 500 | 1.25 | -0.01em | Eyebrow label |
| `body-lg` | 19px | 400 | 1.75 | -0.01em | Hero intro copy, feature-band body |
| `body-md` | 16px | 400 | 1.5 | -0.01em | Default body copy |
| `body-strong` | 16px | 600 | 1.5 | -0.01em | Emphasized body |
| `caption` | 14px | 400 | 1.5 | -0.01em | Button label, metadata, form labels |
| `micro` | 13px | 400 | 1.5 | -0.01em | Fine print, active float-label |
| `eyebrow-cap` | 14px | 700 | 1.2 | 0.1em | All-caps label, uppercase |
| `code` | 16px | 400 | 1.5 | 0 | Code blocks, monospace |

## Border Radius

| Token | Value | Use |
|-------|-------|-----|
| `--ecoursity-radius-xs` | 4px | Inputs, hairline tags |
| `--ecoursity-radius-sm` | 5px | Small image containers |
| `--ecoursity-radius-md` | 8px | Form inputs, video frames |
| `--ecoursity-radius-lg` | 12px | Pricing cards, feature cards, modals |
| `--ecoursity-radius-xl` | 20px | Hero photo frames |
| `--ecoursity-radius-pill` | 50px | **All** buttons |
| `--ecoursity-radius-circle` | 50% | Circular icons, Frap button, avatars |

**Rule**: Buttons are **always** `border-radius: 50px`. Active state: `transform: scale(0.95)` with `0.2s ease` transition. Never use rounded-rectangle for buttons.

## Spacing

Base unit: 1rem = 10px (via `62.5%` root trick). Semantic rem-based scale:

| Token | Rem | Pixels |
|-------|-----|--------|
| `--ecoursity-space-xxs` | 0.4rem | 4px |
| `--ecoursity-space-xs` | 0.8rem | 8px |
| `--ecoursity-space-sm` | 1.6rem | 16px |
| `--ecoursity-space-md` | 2.4rem | 24px |
| `--ecoursity-space-lg` | 3.2rem | 32px |
| `--ecoursity-space-xl` | 4.0rem | 40px |
| `--ecoursity-space-xxl` | 4.8rem | 48px |
| `--ecoursity-space-huge` | 6.4rem | 64px |

**Gutter scale:** 1.6rem (mobile) → 2.4rem (tablet) → 4.0rem (desktop).

## Elevation

| Level | Shadow | Use |
|-------|--------|-----|
| 0 | None | Default surface |
| 1 (Card) | `0 0 0.5px rgba(0,0,0,0.14), 0 1px 1px rgba(0,0,0,0.24)` | Content cards — whisper-soft dual-shadow |
| 2 (Nav) | `0 1px 3px rgba(0,0,0,0.1), 0 2px 2px rgba(0,0,0,0.06), 0 0 2px rgba(0,0,0,0.07)` | Fixed global nav — triple-layer soft lift |
| 3 (Frap Base) | `0 0 6px rgba(0,0,0,0.24)` | Floating Frap CTA base halo |
| 4 (Frap Ambient) | `0 8px 12px rgba(0,0,0,0.14)` | Floating Frap CTA directional shadow |
| 5 (Modal) | `0 25px 50px -12px rgba(0,0,0,0.25)` | Modal / floating panel |

**Shadow philosophy:** Always layer 2–3 low-alpha shadows with different offsets — never a single heavy drop shadow.

## Components

### Buttons — always `border-radius: 50px`, active `scale(0.95)`

| Component | BG | Text | Border | Padding | Size |
|-----------|----|------|--------|---------|------|
| `button-primary-pill` | `#00754A` | `#ffffff` | 1px solid `#00754A` | 7px 16px | 16px / 600 |
| `button-outline` | transparent | `#00754A` | 1px solid `#00754A` | 7px 16px | 16px / 600 |
| `button-black-fill` | `#000000` | `#ffffff` | 1px solid `#000000` | 7px 16px | 14px / 600 |
| `button-dark-outline` | transparent | `rgba(0,0,0,0.87)` | 1px solid `rgba(0,0,0,0.87)` | 7px 16px | 14px / 600 |
| `button-inverted-green` | `#ffffff` | `#00754A` | 1px solid `#ffffff` | 7px 16px | 16px / 600 |
| `button-outline-on-dark` | transparent | `#ffffff` | 1px solid `#ffffff` | 7px 16px | 16px / 600 |
| `button-customize` | `#ffffff` | `#00754A` | 1.5px solid `#00754A` | 14px 40px | 16px / 600 |
| `button-add-to-order` | `#00754A` | `#ffffff` | none | 14px 32px | 16px / 600 |

### Frap — Floating Circular Order Button

| Component | BG | Icon | Size | Radius | Shadow |
|-----------|----|------|------|--------|--------|
| `frap-cta` | `#00754A` | `#ffffff` | 56px | 50% | `0 0 6px rgba(0,0,0,0.24)` + `0 8px 12px rgba(0,0,0,0.14)` |

Fixed bottom-right. Active: ambient shadow fades, `scale(0.95)`. Touch offset: `-0.8rem`.

### Cards & Containers

| Component | BG | Radius | Padding | Border / Shadow |
|-----------|----|--------|---------|-----------------|
| `card-default` | `#ffffff` | 12px | 16–24px | `0 0 0.5px rgba(0,0,0,0.14), 0 1px 1px rgba(0,0,0,0.24)` |
| `card-pricing` | `#ffffff` | 12px | 32px | 1px solid `#e4e4e7` |
| `card-featured` | `#1E3932` | 12px | 32px | none |
| `card-dark-band` | `#1E3932` | 12px | 32px | none |
| `card-partnership` | `#faf6ee` | 12px | 32px | default shadow |
| `card-pistachio-band` | `#d4f9e0` | 12px | 32px | none |

### Navigation

| Component | BG | Text | Padding |
|-----------|----|------|---------|
| `nav-bar` | `#ffffff` | `#000000` | Height: 64–99px (progressive), border-bottom none, shadow stack |
| `footer` | `#1E3932` | `#ffffff` | 64px 24px, border-top none |

### Inputs

| Component | BG | Text | Radius | Padding | Border |
|-----------|----|------|--------|---------|--------|
| `text-input` | `#ffffff` | `rgba(0,0,0,0.87)` | 4px | 12px | 1px solid `#d6dbde` |
| Focus state | — | — | — | — | 1px solid `#00754A` |

### Pills & Tags — always `border-radius: 50px`

| Component | BG | Text | Padding | Type |
|-----------|----|------|---------|------|
| `pill-tag-mint` | `#d4e9e2` | `#000000` | 4px 12px | 13px / 700 uppercase |
| `pill-tag-shade` | `#d4d4d8` | `#000000` | 4px 12px | 13px / 700 uppercase |
| `pill-rewards-gold` | transparent | `#cba258` | 4px 12px | 13px / 700, 1px solid `#cba258` |

## Do's

- Use **warm cream** (`#f2f0eb`) as the primary page canvas instead of pure white — it's the brand signature
- Map green tiers to their intended role: Starbucks Green for headings, Green Accent for CTAs, House Green for deep bands
- Keep tracking tight at `-0.01em` across the whole system
- **Pill shape only** for all buttons — `border-radius: 50px`
- Apply `transform: scale(0.95)` as the universal button active state with `0.2s ease`
- Reserve Gold for Rewards-status ceremony moments only
- Layer 2–3 low-alpha shadows instead of one heavy drop shadow
- Let the cream canvas breathe between cards — use whitespace, not dividers
- Black text on light surfaces, white text on dark fills
- Enable `font-feature-settings: "ss03"` globally

## Don'ts

- Don't use pure white as the page canvas — warm cream is load-bearing
- Don't pick "one brand green" — the four-green system is intentional
- Don't use Gold as a general-purpose accent — it's Rewards only
- Don't square corners on buttons — the 50px pill is universal
- Don't introduce gradient fills — the system is color-block throughout
- Don't weight-contrast headings by size alone — use weight + color
- Don't use pure black for body text — `rgba(0,0,0,0.87)` matches warm canvas
- Don't skip the `scale(0.95)` active feedback on buttons
- Don't stack single heavy shadows — always layer 2–3 low-alpha ones

## Responsive Breakpoints

| Name | Width | Key Changes |
|------|-------|-------------|
| XS | < 480px | Nav 64px; hamburger; single-column; pills full-width |
| Mobile | 480–767px | Nav 72px; cards 2-up; padding tightens |
| Tablet | 768–1023px | Nav 83px; cards 3-up; hero split begins |
| Desktop | 1024–1439px | Nav 99px; cards 4-up; full hero 40/60 |
| XLarge | 1440px+ | Content caps; cards 5-up; extra cream margin |

## Brand Gradient

No gradient system — depth comes from **layered low-alpha shadow stacks** and **color-block banding** (dark-green feature bands between cream/white sections).

Pricing card elevation:
```
box-shadow: 0 0 0.5px rgba(0,0,0,0.14),
            0 1px 1px rgba(0,0,0,0.24);
```

Global nav elevation:
```
box-shadow: 0 1px 3px rgba(0,0,0,0.1),
            0 2px 2px rgba(0,0,0,0.06),
            0 0 2px rgba(0,0,0,0.07);
```

## CSS Setup

```css
:root {
  /* Greens — four-tier system */
  --ecoursity-green-starbucks: #006241;
  --ecoursity-green-accent: #00754A;
  --ecoursity-green-house: #1E3932;
  --ecoursity-green-uplift: #2b5148;
  --ecoursity-green-light: #d4e9e2;

  /* Rewards */
  --ecoursity-gold: #cba258;
  --ecoursity-gold-light: #dfc49d;
  --ecoursity-gold-lightest: #faf6ee;

  /* Canvas */
  --ecoursity-canvas-white: #ffffff;
  --ecoursity-canvas-cream: #f2f0eb;
  --ecoursity-canvas-ceramic: #edebe9;
  --ecoursity-canvas-neutral: #f9f9f9;

  /* Text */
  --ecoursity-text-black: rgba(0,0,0,0.87);
  --ecoursity-text-soft: rgba(0,0,0,0.58);
  --ecoursity-text-white: rgba(255,255,255,1);
  --ecoursity-text-white-soft: rgba(255,255,255,0.70);
  --ecoursity-rewards-green: #33433d;
  --ecoursity-ink: #000000;

  /* Semantic */
  --ecoursity-red: #c82014;
  --ecoursity-yellow: #fbbc05;
  --ecoursity-hairline: #e4e4e7;
  --ecoursity-input-border: #d6dbde;

  /* Border Radius */
  --ecoursity-radius-xs: 4px;
  --ecoursity-radius-sm: 5px;
  --ecoursity-radius-md: 8px;
  --ecoursity-radius-lg: 12px;
  --ecoursity-radius-xl: 20px;
  --ecoursity-radius-pill: 50px;
  --ecoursity-radius-circle: 50%;

  /* Spacing (1rem = 10px) */
  --ecoursity-space-xxs: 0.4rem;
  --ecoursity-space-xs: 0.8rem;
  --ecoursity-space-sm: 1.6rem;
  --ecoursity-space-md: 2.4rem;
  --ecoursity-space-lg: 3.2rem;
  --ecoursity-space-xl: 4.0rem;
  --ecoursity-space-xxl: 4.8rem;
  --ecoursity-space-huge: 6.4rem;

  /* Elevation */
  --ecoursity-shadow-card: 0 0 0.5px rgba(0,0,0,0.14),
                           0 1px 1px rgba(0,0,0,0.24);
  --ecoursity-shadow-nav: 0 1px 3px rgba(0,0,0,0.1),
                          0 2px 2px rgba(0,0,0,0.06),
                          0 0 2px rgba(0,0,0,0.07);
  --ecoursity-shadow-frap-base: 0 0 6px rgba(0,0,0,0.24);
  --ecoursity-shadow-frap-ambient: 0 8px 12px rgba(0,0,0,0.14);
  --ecoursity-shadow-modal: 0 25px 50px -12px rgba(0,0,0,0.25);
}
```

## Notes for Developers

- SoDoSans is proprietary to Starbucks. Use **Inter** (Google Fonts) as the open-source substitute — it has similar humanist geometric proportions and wide weight range.
- Lander Tall (Rewards serif) is custom. Substitute with **Lora** or **Source Serif Pro**.
- Kalam script (Careers cup-names) — use only for decorative contexts, not general UI.

## References

Full specs: [DESIGN.md](file:///.trae/skills/ecoursity-design/DESIGN.md)
