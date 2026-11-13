<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('cari', ''));
        $query = Buku::with('kategori')->orderBy('id');

        if ($cari !== '') {
            $query->where('judul', 'like', "%{$cari}%");
        }

        return view('buku.index', [
            'bukus' => $query->get(),
            'cari' => $cari,
        ]);
    }

    public function show(string $kode): View
    {
        return view('buku.show', [
            'buku' => Buku::with('kategori')->where('kode', $kode)->firstOrFail(),
        ]);
    }

    public function create(): View
    {
        return view('buku.form', [
            'buku' => new Buku,
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'max:10', 'unique:bukus,kode'],
            'judul' => ['required', 'max:255'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ]);

        Buku::create($data);

        return redirect()->route('buku.index')->with('status', 'Buku ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('buku.form', [
            'buku' => $buku,
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, Buku $buku): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'max:10', Rule::unique('bukus', 'kode')->ignore($buku->id)],
            'judul' => ['required', 'max:255'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ]);

        $buku->update($data);

        return redirect()->route('buku.index')->with('status', 'Buku diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()->route('buku.index')->with('status', 'Buku dihapus.');
    }
}
