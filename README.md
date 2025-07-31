## System Requirements
- PHP 8.2+
- Composer
- Node.js 18.0.0+
- MySQL

## Langkah Setup

1. Clone repository
```sh
git clone https://github.com/delvifitri/order-management-api
```

2. Install dependensi
```sh
composer install
npm install
```

3. Copy file .env dan buat database
```sh
cp .env.example .env
```

4. Generate secret key
```sh
php artisan key:generate
php artisan jwt:secret
```

5. Jalankan migrasi
```sh
php artisan migrate
```

6. Jalankan seeder untuk populasi awal database
```sh
php artisan db:seed
```

7. Jalankan server
```sh
php artisan serve
```

## Asumsi Yang Dibuat

- Hanya terdapat 2 role: admin dan customer
- Customer dapat melakukan registrasi sendiri
- Akun admin dibuat melalui database seeder
- Sistem tidak menangani pembayaran
- Stok produk berkurang langsung saat order dibuat
- Admin dapat mengubah status order
- Order status: pending → completed → cancelled