<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BiodataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $opd = Biodata::orderBy('id', 'desc');

            return DataTables::of($opd)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('biodata.edit', $row->id);
                    $deleteUrl = route('biodata.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    $btn .= '<button class="btn btn-primary btn-sm mx-1" data-id="' . e($row->id) . '" 
                         data-url="' . e($editUrl) . '" data-toggle="modal" data-target="#modalForm" id="edit-button"> 
                         Edit
                     </button>';

                    $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="delete-button btn btn-danger btn-sm mx-1">
                                Hapus
                            </button>
                         </form>';

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('biodata.index');
    }

    public function edit($id)
    {
        $list = Biodata::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $list,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:data_diri,nama,',
            'jenis_kelamin' => 'required|in:Laki - laki,Perempuan',
            'tgl_lahir' => 'required|date',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $db = [
            'nama'          => $request->nama,
            'jenis_kelamin'          => $request->jenis_kelamin,
            'tgl_lahir'              => $request->tgl_lahir,
        ];

        Biodata::create($db);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $biodata = Biodata::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:data_diri,nama,' . $biodata->id . ',id',
            'jenis_kelamin' => 'required|in:Laki - laki,Perempuan',
            'tgl_lahir' => 'required|date',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $db = [
            'nama'          => $request->nama,
            'jenis_kelamin'          => $request->jenis_kelamin,
            'tgl_lahir'              => $request->tgl_lahir,
        ];
        
        $biodata->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $biodata = Biodata::findOrFail($id);
        $biodata->delete();

        return response()->json(['status' => 'success']);
    }
}
