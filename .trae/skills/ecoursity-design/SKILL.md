---
name: "ecoursity-design"
description: "Shopifi-inspired design system for Ecoursity UI — light/cream canvas, thin-weight Neue Haas Grotesk display type, pill-shaped buttons, and aloe green accents."
---

# Ecoursity Design System

Shopifi-inspired design language — a clean, editorial commerce UI on a light canvas with cream/warm surfaces, pill-shaped buttons, aloe green accents, and thin-weight display type.

**All CSS must be written to `../assets\css\ecoursity-main.css`.**
**CSS variables (`--ecoursity-*`) are already defined there — add new component/utility CSS below the `:root` block.**

## Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--ecoursity-primary` | `#000000` | Pill fill, ink |
| `--ecoursity-on-primary` | `#ffffff` | Text on dark fills |
| `--ecoursity-canvas-light` | `#ffffff` | Default surface |
| `--ecoursity-canvas-cream` | `#fbfbf5` | Page background, sections |
| `--ecoursity-aloe-10` | `#c1fbd4` | Featured tier, mint accent |
| `--ecoursity-pistachio-10` | `#d4f9e0` | Section band fill |
| `--ecoursity-shade-30` | `#d4d4d8` | Tag/chip BG |
| `--ecoursity-shade-40` | `#a1a1aa` | Tertiary text |
| `--ecoursity-shade-50` | `#71717a` | Secondary text |
| `--ecoursity-shade-60` | `#52525b` | Tertiary text |
| `--ecoursity-shade-70` | `#3f3f46` | Pressed pill state |
| `--ecoursity-hairline-light` | `#e4e4e7` | Card borders, dividers |
| `--ecoursity-ink` | `#000000` | Primary text |

## Typography

Font stack:
- **Display**: `"Neue Haas Grotesk Display", "Helvetica", "Arial", sans-serif` (weight 330 — thin is the brand signature)
- **Body/UI**: `"Inter Variable", "Inter", "Helvetica", "Arial", sans-serif` (weight 420–550)
- **Code**: `"ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", monospace`

Global: `font-feature-settings: "ss03"` — the brand's character-level signature.

| Token | Size | Weight | Line H | Letter Sp | Use |
|-------|------|--------|--------|-----------|-----|
| `display-lg` | 55px | 330 | 1.16 | 0 | Page title |
| `display-md` | 48px | 330 | 1.14 | 0 | Section headline |
| `heading-xl` | 28px | 500 | 1.28 | 0.42px | Card / tier name |
| `heading-lg` | 24px | 400 | 1.14 | 0.36px | Compact card title |
| `heading-md` | 20px | 500 | 1.4 | 0.3px | Sub-heading |
| `heading-sm` | 18px | 500 | 1.25 | 0.72px | Eyebrow label |
| `body-lg` | 18px | 550 | 1.56 | 0 | Marketing lead |
| `body-md` | 16px | 420 | 1.5 | 0 | Default body, buttons |
| `body-strong` | 16px | 550 | 1.5 | 0 | Emphasized body |
| `caption` | 14px | 500 | 1.49 | 0.28px | Helpers, footnotes |
| `micro` | 13px | 500 | 1.5 | -0.13px | Pricing fine print |
| `eyebrow-cap` | 12px | 400 | 1.2 | 0.72px | All-caps eyebrow, uppercase |
| `code` | 16px | 400 | 1.5 | 0 | Code blocks, monospace |

## Border Radius

| Token | Value | Use |
|-------|-------|-----|
| `--ecoursity-radius-xs` | 4px | Inputs, hairline tags |
| `--ecoursity-radius-sm` | 5px | Small image containers |
| `--ecoursity-radius-md` | 8px | Form inputs, video frames |
| `--ecoursity-radius-lg` | 12px | Pricing cards, feature cards |
| `--ecoursity-radius-xl` | 20px | Hero photo frames |
| `--ecoursity-radius-pill` | 9999px | **All** buttons, tags, chips |

**Rule**: Buttons are **always** `border-radius: 9999px`. Never use rounded-rectangle for buttons.

## Spacing

Base unit: 8px

| Token | Value |
|-------|-------|
| `--ecoursity-space-xxs` | 2px |
| `--ecoursity-space-xs` | 4px |
| `--ecoursity-space-sm` | 8px |
| `--ecoursity-space-md` | 12px |
| `--ecoursity-space-lg` | 16px |
| `--ecoursity-space-xl` | 24px |
| `--ecoursity-space-xxl` | 32px |
| `--ecoursity-space-huge` | 64px |

Section padding: ~48px on transactional pages (density for scannability).

## Elevation

| Level | Shadow | Use |
|-------|--------|-----|
| 0 | None | Default surface |
| 1 | `0 8px 8px rgba(0,0,0,0.1), 0 4px 4px rgba(0,0,0,0.1), 0 2px 2px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.1)` | Pricing cards — stacked soft shadows |
| 2 | `0 25px 50px -12px rgba(0,0,0,0.25)` | Modal / floating panel |

## Components

### Buttons — always `border-radius: 9999px`

| Component | BG | Text | Border | Padding | Size |
|-----------|----|------|--------|---------|------|
| `button-primary-pill` | `#000000` | `#ffffff` | none | 12px 24px | 16px / 420 |
| `button-primary-pill-pressed` | `#3f3f46` | `#ffffff` | none | 12px 24px | 16px / 420 |
| `button-outline` | `#ffffff` | `#000000` | 1px solid `#000000` | 12px 24px | 16px / 420 |
| `button-aloe-pill` | `#c1fbd4` | `#000000` | none | 12px 24px | 16px / 420 |

### Cards & Containers

| Component | BG | Radius | Padding | Border |
|-----------|----|--------|---------|--------|
| `card-pricing` | `#ffffff` | 12px | 32px | 1px solid `#e4e4e7` |
| `card-pricing-featured` | `#c1fbd4` | 12px | 32px | none |
| `card-default` | `#ffffff` | 12px | 32px | 1px solid `#e4e4e7` |
| `card-pistachio-band` | `#d4f9e0` | 12px | 32px | none |

### Navigation

| Component | BG | Text | Padding |
|-----------|----|------|---------|
| `nav-bar` | `#ffffff` | `#000000` | 16px 24px, border-bottom 1px `#e4e4e7` |
| `footer` | `#ffffff` | `#000000` | 64px 24px, border-top 1px `#e4e4e7` |

### Inputs

| Component | BG | Text | Radius | Padding | Border |
|-----------|----|------|--------|---------|--------|
| `text-input` | `#ffffff` | `#000000` | 8px | 10px 12px | 1px solid `#e4e4e7` |

### Pills & Tags — always `border-radius: 9999px`

| Component | BG | Text | Padding | Type |
|-----------|----|------|---------|------|
| `pill-tag-mint` | `#c1fbd4` | `#000000` | 4px 12px | 12px / 400 uppercase, tracking 0.72px |
| `pill-tag-shade` | `#d4d4d8` | `#000000` | 4px 12px | 12px / 400 uppercase, tracking 0.72px |

## Do's

- Use **white** / **cream** canvas (`#ffffff` / `#fbfbf5`) as the primary background
- **Pill shape only** for all buttons — `border-radius: 9999px`, never rounded-rect
- Display type at thin weight 330
- Aloe (`#c1fbd4`) for featured tiers and mint accent tags
- Pistachio (`#d4f9e0`) for wide section band fills
- Stacked tiny shadows for pricing card depth
- Enable `font-feature-settings: "ss03"` globally
- Black text on light surfaces, white text on dark fills

## Don'ts

- Don't use dark/black backgrounds — keep the UI light
- Don't display at weight 400+ — thin (330) is the brand signature
- Don't put aloe/pistachio greens behind type — surface fills only
- Don't replace pill shape with rounded-rectangle buttons
- Don't use drop shadows on cards — use stacked tiny shadows or borders

## Responsive Breakpoints

| Name | Width | Changes |
|------|-------|---------|
| Wide | ≥ 1440px | Pricing 4-up |
| Desktop | 1024–1440px | Default width, pricing tightens |
| Tablet | 768–1023px | Pricing 2-up |
| Mobile | < 768px | Pricing 1-up, hamburger, display scales down |

## Brand Gradient

No gradient system — depth comes from **stacked tiny shadows** (Level 1 elevation) and **aloe/pistachio band fills**.

Pricing card elevation:
```
box-shadow: 0 8px 8px rgba(0,0,0,0.1),
            0 4px 4px rgba(0,0,0,0.1),
            0 2px 2px rgba(0,0,0,0.1),
            0 0 0 1px rgba(0,0,0,0.1);
```

## CSS Setup

```css
:root {
  --ecoursity-primary: #000000;
  --ecoursity-on-primary: #ffffff;
  --ecoursity-canvas-light: #ffffff;
  --ecoursity-canvas-cream: #fbfbf5;
  --ecoursity-aloe-10: #c1fbd4;
  --ecoursity-pistachio-10: #d4f9e0;
  --ecoursity-shade-30: #d4d4d8;
  --ecoursity-shade-40: #a1a1aa;
  --ecoursity-shade-50: #71717a;
  --ecoursity-shade-60: #52525b;
  --ecoursity-shade-70: #3f3f46;
  --ecoursity-hairline-light: #e4e4e7;
  --ecoursity-ink: #000000;

  --ecoursity-radius-xs: 4px;
  --ecoursity-radius-sm: 5px;
  --ecoursity-radius-md: 8px;
  --ecoursity-radius-lg: 12px;
  --ecoursity-radius-xl: 20px;
  --ecoursity-radius-pill: 9999px;

  --ecoursity-space-xxs: 2px;
  --ecoursity-space-xs: 4px;
  --ecoursity-space-sm: 8px;
  --ecoursity-space-md: 12px;
  --ecoursity-space-lg: 16px;
  --ecoursity-space-xl: 24px;
  --ecoursity-space-xxl: 32px;
  --ecoursity-space-huge: 64px;

  --ecoursity-shadow-stacked: 0 8px 8px rgba(0,0,0,0.1),
                    0 4px 4px rgba(0,0,0,0.1),
                    0 2px 2px rgba(0,0,0,0.1),
                    0 0 0 1px rgba(0,0,0,0.1);
  --ecoursity-shadow-modal: 0 25px 50px -12px rgba(0,0,0,0.25);
}
```

## References

Full specs: [DESIGN.md](file:///.trae/skills/ecoursity-design/DESIGN.md)
