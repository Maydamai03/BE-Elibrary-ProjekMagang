<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        return Book::latest()->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'penerbit' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'bahasa' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'jumlah_halaman' => 'required|integer',
            'ukuran' => 'nullable|string',
            'pdfPath' => 'required|file|mimes:pdf|max:20480',
            'cover' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk cover
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pdfPath = $request->file('pdfPath')->store('public/pdfs');
        $coverPath = $request->file('cover')->store('public/covers'); // Simpan gambar cover

        $book = Book::create(array_merge($validator->validated(), [
            'pdfPath' => $pdfPath,
            'coverPath' => $coverPath, // Simpan path cover ke database
        ]));

        return response()->json($book, 201);
    }

    public function show(Book $book)
    {
        return $book;
    }

    public function update(Request $request, Book $book)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'penerbit' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $book->id,
            'bahasa' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'jumlah_halaman' => 'required|integer',
            'ukuran' => 'nullable|string',
            'pdfPath' => 'nullable|file|mimes:pdf|max:20480',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk cover baru
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('pdfPath')) {
            Storage::delete($book->pdfPath);
            $validatedData['pdfPath'] = $request->file('pdfPath')->store('public/pdfs');
        }

        if ($request->hasFile('cover')) { // Logika untuk update cover
            Storage::delete($book->coverPath); // Hapus cover lama
            $validatedData['coverPath'] = $request->file('cover')->store('public/covers'); // Simpan cover baru
        }

        $book->update($validatedData);

        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        Storage::delete($book->pdfPath);
        Storage::delete($book->coverPath); // Hapus juga file cover saat buku dihapus
        $book->delete();

        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }
}
