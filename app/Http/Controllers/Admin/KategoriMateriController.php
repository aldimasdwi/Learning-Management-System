<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

use App\Models\KategoriMateri;
use App\Models\Materi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class KategoriMateriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategoriMateri = KategoriMateri::with(['jurusan'])->get();
        return view('admin.kategori-materi.index', compact('kategoriMateri'));
    }

    public function publicIndex()
    {
        $kategoris = KategoriMateri::latest()->paginate(4);
        return view('kategori-materi.index', compact('kategoris'));
    }
    public function publicSearch(Request $request)
    {
        $kategoris = KategoriMateri::where(function ($query) use ($request) {
            $query->where('nama_kategori', 'like', '%' . $request->keyword . '%');
            // ->orWhere('deskripsi','like','%'.$request->keyword.'%');
        })->paginate(4);

        return view('kategori-materi.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jurusans = Jurusan::all();
        return view('admin.kategori-materi.create', compact('jurusans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        KategoriMateri::create([
            'nama_kategori' => $request->nama_kategori,
            'slug' => Str::slug($request->nama_kategori),
            'jurusan_id' => $request->jurusan_id
        ]);
        return redirect()->route('admin.kategori-materi.index')->with('success', 'Data berhasil ditambah');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $kategori = KategoriMateri::with('materi')->where(compact('slug'))->first();
        $materi_pertama = $kategori->materi()->orderBy('tersedia')->first();
        return view('admin.kategori-materi.show', [
            'kategori' => $kategori,
            'materi_pertama' => new Carbon(!$materi_pertama ? '' : $materi_pertama->tersedia)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $kategoriMateri = KategoriMateri::where(compact('slug'))->first();
        return view('admin.kategori-materi.edit', compact('kategoriMateri'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KategoriMateri $kategoriMateri)
    {
        $kategoriMateri->update($request->all());
        return redirect()->route('admin.kategori-materi.index')->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(KategoriMateri $kategoriMateri)
    {
        $kategoriMateri->delete();
        return redirect()->route('admin.kategori-materi.index')->with('success', 'Data berhasil dihapus');
    }
}
