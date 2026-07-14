---
name: "design"
description: "Applies HP-style design system from DESIGN.md. Invoke when designing or updating UI to match this visual language, tokens, components, and layout rules."
---

# Design

Gunakan skill ini saat membuat atau mengubah UI agar konsisten dengan design system di [DESIGN.md](ecoursity/DESIGN.md).

## Kapan dipakai

Invoke saat:
- user minta bikin UI baru
- user minta redesign halaman, komponen, atau section
- user minta samakan tampilan dengan design language proyek
- perlu pilih warna, tipografi, spacing, radius, atau komponen sesuai token
- perlu review apakah implementasi UI sudah patuh design system

Jangan pakai saat:
- tugas murni backend
- bug non-UI
- perubahan kecil yang tidak menyentuh tampilan

## Sumber kebenaran

Selalu pakai [DESIGN.md](ecoursity/DESIGN.md) sebagai source of truth.

Prioritas:
1. Token frontmatter: warna, typography, rounded, spacing, components
2. Narasi body: overview, principles, layout, elevation, shapes, components
3. Jika konflik, ikuti token eksplisit dulu, lalu aturan naratif

## Inti style

- Canvas dominan putih
- Ink gelap untuk headline dan body
- Satu aksen utama: HP Electric Blue `#024ad8`
- Blue cuma untuk CTA utama, link, chevron decoration, indikator aktif
- Typography tunggal: Forma DJR Micro; fallback aman: Inter, Manrope, lalu Roboto
- Button tajam `4px`; cards/foto lembut `16px`
- Dark slab untuk testimonial, help band, footer
- Chevron biru angular jadi aksen brand, bukan ornamen random

## Guardrails wajib

- Jangan tambah warna brand baru
- Jangan pakai biru sebagai background section besar
- Jangan pakai hover-state invent sendiri bila belum diminta
- Jangan campur radius sembarang; pakai token rounded
- Jangan pakai font family lain untuk UI utama
- Jangan bikin visual terlalu ramai; sistem ini putih, rapi, komersial, bersih
- Jangan ubah jadi neumorphism, glassmorphism, atau shadow berat

## Token yang diprioritaskan

### Colors
- primary: `#024ad8`
- primary-bright: `#296ef9`
- primary-deep: `#0e3191`
- primary-soft: `#c9e0fc`
- ink: `#1a1a1a`
- canvas/paper: `#ffffff`
- cloud: `#f7f7f7`
- fog: `#e8e8e8`
- steel: `#c2c2c2`
- charcoal: `#3d3d3d`
- graphite: `#636363`
- error: `#b3262b`

### Typography
- display-xxl: 72/500
- display-xl: 56/500
- display-lg: 44/500
- display-md: 32/500
- display-sm: 24/500
- display-xs: 20/500
- body-lg: 18/400
- body-md: 16/400
- body-emphasis: 16/500
- caption-md: 14/400
- caption-sm: 12/400
- caption-bold: 14/700
- button-md: 14/600 uppercase
- button-sm: 12.6/700
- price-md: 24/500

### Radius
- md: `4px` untuk button/input
- lg: `8px` untuk badge/FAQ row
- xl: `16px` untuk cards/foto
- pill: `9999px` untuk chip/tab

### Spacing
- xxs `4px`
- xs `8px`
- sm `12px`
- md `16px`
- lg `20px`
- xl `24px`
- xxl `32px`
- section `80px`

## Mapping komponen

Pilih komponen paling dekat dari token berikut:
- `button-primary` untuk CTA utama
- `button-ink` untuk CTA gelap di atas imagery/dark slab
- `button-outline` untuk aksi sekunder biru
- `button-outline-ink` untuk aksi sekunder netral
- `button-text-link` untuk inline action
- `card-product` untuk tile produk
- `card-product-feature` untuk row promo/feature
- `card-pricing-tier` untuk pricing
- `promo-strip-dark` / `help-band-dark` / `footer-dark` untuk penutup section gelap
- `nav-bar-top`, `nav-link`, `category-tab` untuk navigation pattern
- `faq-row` untuk accordion/list FAQ
- `chevron-decoration` untuk aksen hero brand

## Cara pakai saat implementasi

1. Identifikasi jenis surface: white body, cloud/fog band, atau dark slab.
2. Pilih typography dari token, bukan angka acak.
3. Pilih spacing dari scale 4/8/12/16/20/24/32/80.
4. Pakai radius sesuai kategori elemen.
5. Batasi biru maksimal sebagai sinyal penting dalam viewport.
6. Jika butuh hero/feature, pertimbangkan chevron biru angular.
7. Untuk mobile, pertahankan bahasa visual; cukup rapatkan grid dan section spacing.

## Aturan review cepat

Checklist singkat:
- Apakah background utama tetap putih?
- Apakah CTA utama hanya satu fokus biru?
- Apakah headline/body pakai ink, bukan abu terlalu pucat?
- Apakah button 4px dan card 16px?
- Apakah section gelap dipakai hanya untuk band penutup/testimonial/footer?
- Apakah spacing ikut token?
- Apakah font konsisten satu keluarga?

## Output yang diharapkan

Saat user minta UI, hasilkan:
- struktur section yang sesuai sistem
- pilihan token spesifik untuk warna/typo/spacing/radius
- komponen yang dipakai
- jika coding, implementasi minimal yang langsung mengikuti token ini

## Fallback font

Jika Forma DJR Micro tidak tersedia:
1. Manrope
2. Inter
3. Roboto

Set explicit line-height untuk jaga ritme.

## Catatan

Jika user minta sesuatu yang bentrok dengan system, bilang jelas bagian yang bentrok lalu usulkan versi terdekat yang masih patuh design language.