# Developer Guide

## Architecture Overview

This application is built with:
- **Laravel 12** - PHP framework
- **Livewire 3** - Full-stack framework for dynamic interfaces
- **MySQL/MariaDB** - Database
- **Vite** - Asset bundling

## Directory Structure

```
postcrossing-tracker/
├── app/
│   ├── Livewire/          # Livewire components (main logic)
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services (GeocodingService, CurrencyService)
│   └── Providers/         # Service providers
├── resources/
│   ├── views/
│   │   ├── livewire/      # Livewire component views
│   │   └── components/    # Blade components
│   ├── css/               # Stylesheets
│   └── js/                # JavaScript
├── routes/
│   └── web.php            # Web routes
├── public/
│   ├── uploads/           # User-uploaded images
│   └── build/             # Compiled assets
└── docs/                  # Documentation
```

## Livewire Components

Each page is a Livewire component with its own state and methods:

### Component Lifecycle
1. `mount()` - Initialize component state
2. `render()` - Return the view
3. Action methods - Handle user interactions

### Example: RegisterPostcard
```php
class RegisterPostcard extends Component
{
    public $type = 'sent';
    public $postcardId = '';
    // ... properties

    public function mount()
    {
        // Initialize state
    }

    public function submit()
    {
        // Handle form submission
    }

    public function render()
    {
        return view('livewire.register-postcard');
    }
}
```

## Services

### GeocodingService
Handles address-to-coordinates conversion using:
- **Google Maps Geocoding API** - For non-China addresses
- **AMap Web Service API** - For China addresses

```php
$service = new GeocodingService();
$coords = $service->getCoordinates($address, $country);
// Returns ['lat' => 35.6762, 'lng' => 139.6503]
```

## Models

### Postcard
```php
// Relationships
$postcard->user      // belongsTo User
$postcard->contact   // belongsTo Contact
$postcard->country   // belongsTo Country
$postcard->stamps    // hasMany PostcardStamp

// Casts
'tanggal_kirim' => 'date'
'tanggal_terima' => 'date'
'biaya_prangko' => 'decimal:2'
```

### PostcardStamp
```php
$stamp->postcard  // belongsTo Postcard
```

## Adding New Features

### 1. Create a Livewire Component
```bash
php artisan make:livewire NewFeature
```

### 2. Add Route
```php
// routes/web.php
Route::get('/new-feature', NewFeature::class)
    ->middleware('auth')
    ->name('new-feature');
```

### 3. Create View
Edit `resources/views/livewire/new-feature.blade.php`

## Security & Privacy

- **Data Privacy**: We encrypt sensitive fields like names, addresses, and phone numbers using AES-256. This means you can't search them directly in SQL (using `LIKE`), so the app handles those filters in PHP.
- **Location Security**: GPS coordinates are also encrypted and stored in a central `contacts` table.
- **Access Control**: We check the `user_id` on every query to make sure you only see your own cards.
- **Bot Protection**: Login is protected by ReCaptcha.

## Testing Locally

```bash
# Start development server
php artisan serve

# Watch for asset changes
npm run dev

# Run with specific port
php artisan serve --port=8080
```

## Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Run `composer install --no-dev`
- [ ] Run `npm run build`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan storage:link`
- [ ] Set proper file permissions

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

Please follow PSR-12 coding standards for PHP.
