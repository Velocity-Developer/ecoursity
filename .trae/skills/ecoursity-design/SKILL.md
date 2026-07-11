---
name: "ecoursity-design"
description: "Shopifi-inspired design system for Ecoursity UI — light/cream canvas, thin-weight Neue Haas Grotesk display type, pill-shaped buttons, and aloe green accents."
---

# Ecoursity Design System

Shopifi-inspired design language — a clean, editorial commerce UI on a light canvas with cream/warm surfaces, pill-shaped buttons, aloe green accents, and thin-weight display type.

All values use Tailwind classes (`tw:` prefix).

## Colors

| Token | Tailwind | Hex | Usage |
|-------|----------|-----|-------|
| `primary` | `tw:bg-black` / `tw:text-black` | `#000000` | Pill fill, ink |
| `on-primary` | `tw:text-white` | `#ffffff` | Text on dark fills |
| `canvas-light` | `tw:bg-white` | `#ffffff` | Default surface |
| `canvas-cream` | `tw:bg-[#fbfbf5]` | `#fbfbf5` | Page background, sections |
| `aloe-10` | `tw:bg-[#c1fbd4]` / `tw:text-[#c1fbd4]` | `#c1fbd4` | Featured tier, mint accent |
| `pistachio-10` | `tw:bg-[#d4f9e0]` | `#d4f9e0` | Section band fill |
| `shade-30` | `tw:bg-zinc-200` | `#d4d4d8` | Tag/chip BG |
| `shade-40` | `tw:text-zinc-400` | `#a1a1aa` | Tertiary text |
| `shade-50` | `tw:text-zinc-500` | `#71717a` | Secondary text |
| `shade-60` | `tw:text-zinc-600` | `#52525b` | Tertiary text |
| `shade-70` | `tw:bg-zinc-700` | `#3f3f46` | Pressed pill state |
| `hairline-light` | `tw:border-zinc-200` | `#e4e4e7` | Card borders, dividers |
| `ink` | `tw:text-black` | `#000000` | Primary text |

Opacity: `tw:bg-black/10` `tw:text-black/60` `tw:border-zinc-200/50`

## Typography

Font stack:
- **Display**: `"Neue Haas Grotesk Display", "Helvetica", "Arial", sans-serif` (weight 330 — thin is the brand signature)
- **Body/UI**: `"Inter Variable", "Inter", "Helvetica", "Arial", sans-serif` (weight 420–550)
- **Code**: `"ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", monospace`

Global: enable `font-feature-settings: "ss03"` — the brand's character-level signature.

| Token | Tailwind | Size | Wt | Use |
|-------|----------|------|----|-----|
| `display-lg` | `tw:text-[55px] tw:font-[330] tw:leading-[1.16]` | 55px | 330 | Page title |
| `display-md` | `tw:text-[48px] tw:font-[330] tw:leading-[1.14]` | 48px | 330 | Section headline |
| `heading-xl` | `tw:text-[28px] tw:font-medium tw:leading-[1.28] tw:tracking-[0.42px]` | 28px | 500 | Card / tier name |
| `heading-lg` | `tw:text-[24px] tw:font-normal tw:leading-[1.14] tw:tracking-[0.36px]` | 24px | 400 | Compact card title |
| `heading-md` | `tw:text-[20px] tw:font-medium tw:leading-[1.4] tw:tracking-[0.3px]` | 20px | 500 | Sub-heading |
| `heading-sm` | `tw:text-[18px] tw:font-medium tw:leading-[1.25] tw:tracking-[0.72px]` | 18px | 500 | Eyebrow label |
| `body-lg` | `tw:text-lg tw:font-semibold tw:leading-[1.56]` | 18px | 550 | Marketing lead |
| `body-md` | `tw:text-base` | 16px | 420 | Default body, buttons |
| `body-strong` | `tw:text-base tw:font-semibold` | 16px | 550 | Emphasized body |
| `caption` | `tw:text-sm tw:font-medium tw:leading-[1.49] tw:tracking-[0.28px]` | 14px | 500 | Helpers, footnotes |
| `micro` | `tw:text-[13px] tw:font-medium tw:leading-[1.5] tw:tracking-[-0.13px]` | 13px | 500 | Pricing fine print |
| `eyebrow-cap` | `tw:text-xs tw:font-normal tw:leading-[1.2] tw:tracking-[0.72px] tw:uppercase` | 12px | 400 | All-caps eyebrow |
| `code` | `tw:text-base tw:font-mono` | 16px | 400 | Code blocks |

## Border Radius

| Token | Tailwind | Value | Use |
|-------|----------|-------|-----|
| `xs` | `tw:rounded-[4px]` | 4px | Inputs, hairline tags |
| `sm` | `tw:rounded-[5px]` | 5px | Small image containers |
| `md` | `tw:rounded-[8px]` | 8px | Form inputs, video frames |
| `lg` | `tw:rounded-[12px]` | 12px | Pricing cards, feature cards |
| `xl` | `tw:rounded-[20px]` | 20px | Hero photo frames |
| `pill` | `tw:rounded-full` | 9999px | **All** buttons, tags, chips |

**Rule**: Buttons are **always** `tw:rounded-full`. Never use rounded-rectangle for buttons.

## Spacing

Base unit: 8px

| Token | Tailwind | Value |
|-------|----------|-------|
| `xxs` | `tw:p-[2px]` | 2px |
| `xs` | `tw:p-1` | 4px |
| `sm` | `tw:p-2` | 8px |
| `md` | `tw:p-3` | 12px |
| `lg` | `tw:p-4` | 16px |
| `xl` | `tw:p-6` | 24px |
| `xxl` | `tw:p-8` | 32px |
| `huge` | `tw:p-16` | 64px |

Section padding: ~48px on transactional pages (density for scannability).

## Elevation

| Level | Tailwind | Use |
|-------|----------|-----|
| 0 | None | Default surface |
| 1 | `tw:shadow-[0_8px_8px_rgba(0,0,0,0.1),0_4px_4px_rgba(0,0,0,0.1),0_2px_2px_rgba(0,0,0,0.1),0_0_0_1px_rgba(0,0,0,0.1)]` | Pricing cards — stacked soft shadows |
| 2 | `tw:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)]` | Modal / floating panel |

## Components

### Buttons — **always** `tw:rounded-full`

| Component | Classes | Notes |
|-----------|---------|-------|
| `button-primary-pill` | `tw:rounded-full tw:bg-black tw:text-white tw:px-6 tw:py-3 tw:text-base` | Dominant CTA |
| `button-primary-pill-pressed` | `tw:rounded-full tw:bg-zinc-700 tw:text-white tw:px-6 tw:py-3 tw:text-base` | Pressed state |
| `button-outline` | `tw:rounded-full tw:bg-white tw:text-black tw:px-6 tw:py-3 tw:text-base tw:border tw:border-black` | Outline CTA |
| `button-aloe-pill` | `tw:rounded-full tw:bg-[#c1fbd4] tw:text-black tw:px-6 tw:py-3 tw:text-base` | Featured / mint CTA |

### Cards & Containers

| Component | Classes | Notes |
|-----------|---------|-------|
| `card-pricing` | `tw:rounded-[12px] tw:bg-white tw:p-8 tw:border tw:border-zinc-200` | Standard pricing |
| `card-pricing-featured` | `tw:rounded-[12px] tw:bg-[#c1fbd4] tw:p-8` | Featured tier (aloe) |
| `card-default` | `tw:rounded-[12px] tw:bg-white tw:p-8 tw:border tw:border-zinc-200` | Default content card |
| `card-pistachio-band` | `tw:rounded-[12px] tw:bg-[#d4f9e0] tw:p-8` | Section band |

### Navigation

| Component | Classes |
|-----------|---------|
| `nav-bar` | `tw:bg-white tw:text-black tw:px-6 tw:py-4 tw:text-base tw:border-b tw:border-zinc-200` |
| `footer` | `tw:bg-white tw:text-black tw:px-6 tw:p-16 tw:text-sm tw:border-t tw:border-zinc-200` |

### Inputs

| Component | Classes |
|-----------|---------|
| `text-input` | `tw:rounded-[8px] tw:bg-white tw:text-black tw:px-3 tw:py-[10px] tw:text-base tw:border tw:border-zinc-200` |

### Pills & Tags — **always** `tw:rounded-full`

| Component | Classes |
|-----------|---------|
| `pill-tag-mint` | `tw:rounded-full tw:bg-[#c1fbd4] tw:text-black tw:px-3 tw:py-1 tw:text-xs tw:uppercase tw:tracking-[0.72px]` |
| `pill-tag-shade` | `tw:rounded-full tw:bg-zinc-200 tw:text-black tw:px-3 tw:py-1 tw:text-xs tw:uppercase tw:tracking-[0.72px]` |

## Do's

- Use **white** / **cream** canvas (`tw:bg-white` / `tw:bg-[#fbfbf5]`) as the primary background
- **Pill shape only** for all buttons — `tw:rounded-full`, never rounded-rect
- Display type at thin weight 330 — `tw:font-[330]`
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
```html
<div class="tw:rounded-[12px] tw:bg-white tw:p-8 tw:border tw:border-zinc-200
  tw:shadow-[0_8px_8px_rgba(0,0,0,0.1),0_4px_4px_rgba(0,0,0,0.1),0_2px_2px_rgba(0,0,0,0.1),0_0_0_1px_rgba(0,0,0,0.1)]">
```

## Tailwind `@theme` Setup (main.css)

```css
@import "tailwindcss";

@theme {
  --color-canvas-cream: #fbfbf5;
  --color-aloe-10: #c1fbd4;
  --color-pistachio-10: #d4f9e0;

  --radius-xs: 4px;
  --radius-sm: 5px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 20px;
}
```

Then use: `tw:bg-aloe-10` / `tw:bg-canvas-cream` / `tw:rounded-xl`

## References

Full specs: [DESIGN.md](file:///.trae/skills/ecoursity-design/DESIGN.md)
