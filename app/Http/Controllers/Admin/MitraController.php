<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::all();
        return view('admin.mitra.index', compact('mitras'));
    }

   public function create()
{
    return view('admin.mitra.form');
}

public function edit($id)
{
    $mitra = Mitra::findOrFail($id);
    return view('admin.mitra.form', compact('mitra'));
}


    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => 'required',
        ]);

        Mitra::create($request->all());

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

 

    public function update(Request $request, $id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->update($request->all());

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->delete();

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }
}
