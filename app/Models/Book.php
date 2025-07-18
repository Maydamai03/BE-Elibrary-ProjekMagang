<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'penulis',
        'kategori',
        'deskripsi',
        'penerbit',
        'isbn',
        'bahasa',
        'tanggal_terbit',
        'jumlah_halaman',
        'ukuran',
        'pdfPath',
        'coverPath', // Tambahkan field baru di sini
    ];

    protected $casts = [
        'tanggal_terbit' => 'date:Y-m-d',
    ];

    // Tambahkan 'cover_url' ke $appends
    protected $appends = ['pdf_url', 'cover_url'];

    public function getPdfUrlAttribute()
    {
        return $this->pdfPath ? Storage::url($this->pdfPath) : null;
    }

    /**
     * Get the full URL for the book's cover image.
     *
     * @return string|null
     */
    public function getCoverUrlAttribute()
    {
        // Membuat URL lengkap untuk gambar cover
        return $this->coverPath ? Storage::url($this->coverPath) : null;
    }
}
