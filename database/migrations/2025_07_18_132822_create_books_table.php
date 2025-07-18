<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('penulis');
            $table->string('kategori');
            $table->text('deskripsi');
            $table->string('penerbit');
            $table->string('isbn')->nullable()->unique(); // ISBN bisa jadi unik dan opsional
            $table->string('bahasa');
            $table->date('tanggal_terbit');
            $table->integer('jumlah_halaman');
            $table->string('ukuran')->nullable(); // Misal: "14x20 cm" atau ukuran file "15 MB"
            $table->string('pdfPath'); // Path ke file PDF di storage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
