1. Registrasi User Baru
Ini adalah endpoint publik, jadi kita tidak memerlukan token apa pun.

Method: POST

URL: http://127.0.0.1:8000/api/register

Headers:

Key: Accept

Value: application/json

Body (form-data):

nama_lengkap: John Doe

email: john.doe@example.com

password: password123

Klik Send. Jika berhasil, Anda akan mendapatkan respons JSON seperti ini, yang berisi access_token. Simpan token ini, karena ini adalah kunci Anda untuk mengakses endpoint lain.

{
    "message": "Registrasi berhasil",
    "access_token": "1|abcdefghijklmnopqrstuvwxyz123456",
    "token_type": "Bearer",
    "user": {
        "nama_lengkap": "John Doe",
        "email": "john.doe@example.com",
        "role": "pembaca",
        "updated_at": "2025-07-18T13:00:00.000000Z",
        "created_at": "2025-07-18T13:00:00.000000Z",
        "id": 1
    }
}

2. Login User
Method: POST

URL: http://127.0.0.1:8000/api/login

Headers: Accept: application/json

Body (form-data):

email: john.doe@example.com

password: password123

Klik Send. Anda akan mendapatkan respons yang mirip dengan registrasi, lengkap dengan access_token baru.

Langkah C: Mengetes Endpoint yang Dilindungi
Sekarang kita akan menggunakan token yang didapat dari login/registrasi.

Cara Menggunakan Token di Postman
Salin access_token yang Anda dapatkan.

Di request baru, buka tab Authorization.

Pilih Type: Bearer Token.

Di kolom Token di sebelah kanan, tempelkan token Anda.

3. Melihat Semua Buku (Sebagai Pembaca)
Method: GET

URL: http://127.0.0.1:8000/api/books

Authorization: Set Bearer Token seperti di atas.

Klik Send. Anda akan mendapatkan daftar semua buku dalam format JSON (atau array kosong jika belum ada buku).

Langkah D: Mengetes Endpoint Khusus Admin
Endpoint untuk membuat, mengubah, dan menghapus buku hanya bisa diakses oleh admin.

PENTING: Menjadikan User sebagai Admin
Gunakan seeder yang sudah kita buat atau ubah role user secara manual di database menjadi admin. Setelah itu, login kembali dengan user tersebut di Postman untuk mendapatkan token baru yang memiliki hak akses admin.

4. Membuat Buku Baru (Sebagai Admin)
Ini adalah bagian yang paling penting karena melibatkan upload file.

Method: POST

URL: http://127.0.0.1:8000/api/books

Authorization: Gunakan token admin yang baru Anda dapatkan.

Body:

Pilih tab Body, lalu WAJIB form-data.

Masukkan key-value berikut:

judul: Belajar Laravel 8 API

penulis: Budi Santoso

kategori: Pemrograman

deskripsi: Buku panduan lengkap untuk pemula.

penerbit: Penerbit Informatika

isbn: 978-602-03-8433-2

bahasa: Indonesia

tanggal_terbit: 2025-01-15

jumlah_halaman: 350

ukuran: 15 MB

cover: (Tipe: File) Pilih file gambar (jpg, png) dari komputer Anda.

pdfPath: (Tipe: File) Pilih file PDF dari komputer Anda.

Klik Send. Jika berhasil, Anda akan mendapatkan JSON dari buku yang baru dibuat.

5. Mengubah dan Menghapus Buku (Sebagai Admin)
Update Buku:

Method: POST (ingat, kita pakai POST untuk update agar mudah)

URL: http://127.0.0.1:8000/api/books/1 (ganti 1 dengan ID buku yang ingin diubah)

Authorization: Token admin.

Body (form-data): Masukkan field yang ingin diubah. Anda juga bisa menyertakan file cover atau pdfPath baru jika ingin menggantinya.

Delete Buku:

Method: DELETE

URL: http://127.0.0.1:8000/api/books/1 (ganti 1 dengan ID buku)

Authorization: Token admin.

6. Logout
Method: POST

URL: http://127.0.0.1:8000/api/logout

Authorization: Gunakan token apa saja (admin atau pembaca).

Setelah logout, token tersebut tidak akan bisa digunakan lagi.

Tips Troubleshooting
401 Unauthorized: Anda lupa menyertakan token, atau tokennya salah/sudah logout. Periksa tab Authorization.

403 Forbidden: Token Anda valid, tapi role Anda tidak sesuai (misal: pembaca mencoba membuat buku).

422 Unprocessable Entity: Validasi gagal. Periksa body respons, Laravel akan memberitahu field mana yang salah.

500 Internal Server Error: Ada error di kode Laravel Anda. Cek file log di storage/logs/laravel.log.
