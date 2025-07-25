<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Biodata;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CRUDModalController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        if ($request->ajax()) {
            $data = Biodata::orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('jenis_kelamin', function ($row) {
                    return $row->jenis_kelamin == 1 ? 'Laki - laki' : 'Perempuan';
                })
                ->editColumn('tgl_lahir', function ($row) {
                    return Carbon::parse($row->tgl_lahir)->translatedFormat('d F Y');
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('crud-modal.show', $row->id);
                    $deleteUrl = route('crud-modal.destroy', $row->id);

                    $btn = '<div class="d-flex justify-content-center">';
                    $btn .= '<button class="btn btn-primary btn-sm edit-button" data-id="' . e($row->id) . '" data-url="' . e($showUrl) . '">Edit</button>';

                    $btn .= '<form action="' . e($deleteUrl) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="delete-button btn btn-danger btn-sm ml-2">Hapus</button></form>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.modal.index');
    }

    public function show($id)
    {
        $data = Biodata::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|unique:biodata,nama',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required|date',
        ];

        $messages = [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
        ];

        $request->validate($rules, $messages);

        $db = [
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tgl_lahir' => $request->tgl_lahir,
        ];

        Biodata::create($db);

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $data = Biodata::findOrFail($id);

        $rules = [
            'nama' => 'required|unique:biodata,nama,' . $data->id . ',id',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required|date',
        ];

        $messages = [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
        ];

        $request->validate($rules, $messages);

        $db = [
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tgl_lahir' => $request->tgl_lahir,
        ];

        $data->update($db);

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        $data = Biodata::findOrFail($id);

        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
