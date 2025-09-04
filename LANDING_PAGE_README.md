# Landing Page WebKhanza

Landing page profesional dan responsif yang terintegrasi dengan sistem identitas website dinamis.

## 🎨 Fitur Utama

### ✅ Design & UX
- **Modern & Profesional**: Design clean dengan gradient dan animasi smooth
- **Fully Responsive**: Optimal di semua device (mobile, tablet, desktop)
- **Dark Mode Support**: Otomatis menyesuaikan preferensi sistem
- **Loading Screen**: Professional loading dengan spinner animation
- **Smooth Scrolling**: Navigation yang halus dengan offset navbar
- **SEO Optimized**: Meta tags lengkap dan structured data

### ✅ Integrasi Database
- **Dynamic Content**: Semua konten diambil dari tabel `website_identities`
- **Theme Colors**: Warna dinamis sesuai konfigurasi admin
- **Logo & Branding**: Logo dan favicon otomatis dari database
- **Contact Info**: Email, telepon, dan alamat dari database

### ✅ Sections
1. **Hero Section**: Header dengan CTA dan hero image
2. **About Section**: Penjelasan tentang sistem dengan stats
3. **Features Section**: 6 fitur utama dengan icons dan deskripsi
4. **Contact Section**: Informasi kontak dan contact form
5. **Footer**: Links, contact info, dan copyright

### ✅ Technical Features
- **Performance**: Lazy loading, code splitting, optimized assets
- **Accessibility**: ARIA labels, keyboard navigation, screen reader support
- **Cross-browser**: Compatible dengan semua browser modern
- **Print Friendly**: Optimized untuk printing
- **Touch Optimized**: Enhanced experience untuk touch devices

## 📁 File Structure

```
resources/views/
├── layouts/
│   └── app.blade.php          # Master layout
├── components/
│   ├── theme-styles.blade.php # Dynamic CSS variables
│   └── landing/
│       ├── navbar.blade.php   # Navigation component
│       ├── hero.blade.php     # Hero section
│       ├── about.blade.php    # About section
│       ├── features.blade.php # Features section
│       ├── contact.blade.php  # Contact section
│       └── footer.blade.php   # Footer component
└── landing/
    └── index.blade.php        # Main landing page

public/
├── css/
│   ├── landing.css           # Main styles
│   └── responsive.css        # Responsive design
└── js/
    └── landing.js           # JavaScript functionality

app/Http/Controllers/
└── LandingPageController.php # Controller
```

## 🎯 Routes

- `GET /` - Landing page (menggunakan `LandingPageController@index`)

## 🔧 Components Usage

### Navbar Component
```blade
<x-landing.navbar :website-identity="$websiteIdentity" />
```

### Hero Section
```blade
<x-landing.hero :website-identity="$websiteIdentity" />
```

### About Section
```blade
<x-landing.about :website-identity="$websiteIdentity" />
```

### Features Section
```blade
<x-landing.features />
```

### Contact Section
```blade
<x-landing.contact :website-identity="$websiteIdentity" />
```

### Footer
```blade
<x-landing.footer :website-identity="$websiteIdentity" />
```

## 🎨 CSS Variables

Warna tema otomatis diambil dari database melalui `WebsiteThemeService`:

```css
:root {
  --color-primary: #3B82F6;      /* Warna utama */
  --color-secondary: #1E40AF;    /* Warna sekunder */
  --color-accent: #EF4444;       /* Warna aksen */
  --color-primary-rgb: 59, 130, 246;
  --color-secondary-rgb: 30, 64, 175;
  --color-accent-rgb: 239, 68, 68;
}
```

## 📱 Responsive Breakpoints

- **Mobile**: < 576px
- **Small**: 576px - 767px
- **Tablet**: 768px - 991px
- **Desktop**: 992px - 1199px
- **Large Desktop**: ≥ 1200px

## ⚡ Performance Features

1. **Optimized Images**: Lazy loading dan compression
2. **Minified Assets**: CSS dan JS terkompresi
3. **Caching**: Service cache untuk website identity
4. **CDN Ready**: Assets siap untuk CDN
5. **Critical CSS**: Inline critical styles

## 🔒 Security Features

1. **CSRF Protection**: Form protection
2. **XSS Prevention**: Input sanitization
3. **Safe Links**: Target blank dengan noopener
4. **Content Security**: Proper headers

## 🛠 Customization

### Mengubah Warna
1. Login ke admin panel (`/admin`)
2. Buka "Identitas Website"
3. Edit bagian "Tema & Warna"
4. Simpan perubahan

### Mengubah Konten
Edit komponen di `resources/views/components/landing/`

### Menambah Section
1. Buat component baru di `resources/views/components/landing/`
2. Tambahkan di `resources/views/landing/index.blade.php`
3. Update CSS jika diperlukan

## 📊 Browser Support

- ✅ Chrome 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+
- ✅ Mobile browsers

## 🚀 Deployment Notes

1. Jalankan `php artisan migrate` untuk memastikan database ter-update
2. Pastikan storage link aktif: `php artisan storage:link`
3. Optimize assets: `php artisan optimize`
4. Set proper cache headers untuk static assets

## 🐛 Troubleshooting

### Landing page tidak muncul
- Cek route: `php artisan route:list`
- Cek database: pastikan tabel `website_identities` ada data

### Warna tidak berubah
- Clear cache: `php artisan cache:clear`
- Cek WebsiteThemeService

### Images tidak muncul
- Jalankan: `php artisan storage:link`
- Cek permission folder storage

Created with ❤️ for WebKhanza System