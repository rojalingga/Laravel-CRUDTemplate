<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Biodata;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'foto' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];

        $messages = [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'foto.file' => 'Foto harus berupa file.',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $ext = $foto->getClientOriginalExtension();

                $filename = Str::random(25) . '.' . $ext;
                $foto->move(public_path('assets/foto/'), $filename);
            }

            $db = [
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'foto' => $filename ?? ''
            ];

            Biodata::create($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Biodata::findOrFail($id);

        $rules = [
            'nama' => 'required|unique:biodata,nama,' . $data->id . ',id',
            'jenis_kelamin' => 'required',
            'tgl_lahir' => 'required|date',
            'foto' => 'nullable|file|mimes:jpeg,png,jpg|max:2048',
        ];

        $messages = [
            'nama.required' => 'Nama wajib diisi.',
            'nama.unique' => 'Nama sudah digunakan.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tgl_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'foto.file' => 'Foto harus berupa file.',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        DB::beginTransaction();
        try {

            if ($request->hasFile('foto')) {
                if (!empty($data->foto) && file_exists(public_path('assets/foto/' . $data->foto))) {
                    unlink(public_path('assets/foto/' . $data->foto));
                }

                $foto = $request->file('foto');
                $ext = $foto->getClientOriginalExtension();
                $filename = Str::random(25) . '.' . $ext;
                $foto->move(public_path('assets/foto/'), $filename);
            }

            $db = [
                'nama' => $request->nama,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'foto' => $filename ?? $data->foto
            ];

            $data->update($db);

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Biodata::findOrFail($id);

            if (!empty($data->foto) && file_exists(public_path('assets/foto/' . $data->foto))) {
                unlink(public_path('assets/foto/' . $data->foto));
            }

            $data->delete();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
