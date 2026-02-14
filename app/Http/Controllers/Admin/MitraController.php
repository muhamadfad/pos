<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
   public function index(Request $request)
{
    $query = Mitra::query();

    if ($request->search) {
        $query->where('nama_mitra', 'like', '%' . $request->search . '%')
              ->orWhere('alamat', 'like', '%' . $request->search . '%')
              ->orWhere('no_hp', 'like', '%' . $request->search . '%');
    }

    $mitras = $query->latest()->paginate(10);
    $total = Mitra::count();

    if ($request->ajax()) {
        return view('admin.mitra.partials.table', compact('mitras'))->render();
    }

    return view('admin.mitra.index', compact('mitras', 'total'));
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

        return redirect()->route('admin.mitra.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->delete();

        return redirect()->route('admin.mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }
}
