<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Jurusan;
use App\Models\ProgramStudi;
use Spatie\Permission\Models\Role;

use App\Http\Requests\MahasiswaRequest;
use App\Http\Requests\MahasiswaUpdateRequest;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;


class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mahasiswa = Mahasiswa::latest()
        ->where('status', 'Mahasiswa Aktif' || 'Keluar')
        ->get();

        return view ('admin.mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'title'     => 'Mahasiswa'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createImport()
    {
        $jurusan    =   Jurusan::get();
        $prodi      =   ProgramStudi::get();
        
        return view ('admin.mahasiswa.formImport', [
            'jurusan'   =>  $jurusan,
            'prodi'     =>  $prodi,
            'title'     => 'Form Import Mahasiswa'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jurusan    =   Jurusan::get();
        $prodi      =   ProgramStudi::get();
        $roles      =   Role::all();
        
        return view ('admin.mahasiswa.form', [
            'jurusan'   =>  $jurusan,
            'prodi'     =>  $prodi,
            'roles'     =>  $roles,
            'title'     => 'Form Tambah Mahasiswa'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MahasiswaRequest $request)
    {
        DB::beginTransaction();

        try {
            $roles = Role::findOrFail($request->roles);

            $user = User::create([
                'name'        => $request->name,
                'nomor_induk' => $request->nomor_induk,
                'email'       => $request->email,
                'wa'          => 62 . $request->wa,
                'password'    => Hash::make($request->nomor_induk)
            ]);

            $data = [
                'user_id'           => $user->id,
                'nim'               => $user->nomor_induk,
                'angkatan'          => $request->angkatan,
                'jurusan_id'        => $request->jurusan_id,
                'program_studi_id'  => $request->program_studi_id,
                'status'            => $request->status
            ];

            $image = Mahasiswa::saveImage($request);

            $data['image'] = $image;

            $mahasiswa = Mahasiswa::create($data);

            $user->assignRole($roles);

            DB::commit();

            return redirect()->route('mahasiswa.index')->with('success', 'Data Berhasil Ditambah');
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->withError('Mahasiswa Gagal Ditambah');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $mahasiswa = Mahasiswa::findOrFail($id);
        $jurusan = Jurusan::oldest('name')->get();
        $prodi = ProgramStudi::oldest('name')->get();
        $roles     =   Role::oldest('name')->get();

        return view ('admin.mahasiswa.detail', [
            'mahasiswa' => $mahasiswa,
            'jurusan'   => $jurusan,
            'prodi'     => $prodi,
            'roles'     => $roles,
            'title'     => 'Mahasiswa'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $mahasiswa = Mahasiswa::findOrFail($id);
        $jurusan   = Jurusan::oldest('name')->get();
        $prodi     = ProgramStudi::oldest('name')->get();
        $roles     = Role::oldest('name')->get();

        return view ('admin.mahasiswa.form', [
            'mahasiswa' => $mahasiswa,
            'jurusan'   => $jurusan,
            'prodi'     => $prodi,
            'roles'     => $roles,
            'title'     => 'Mahasiswa'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MahasiswaUpdateRequest $request, Mahasiswa $mahasiswa)
    {
        $data = [
            'nim'               => $request->nomor_induk,
            'angkatan'          => $request->angkatan,
            'jurusan_id'        => $request->jurusan_id,
            'program_studi_id'  => $request->program_studi_id,
            'status'            => $request->status,
        ];
        
        $image = Mahasiswa::saveImage($request);
        
        if ($image) {
            $data['image'] = $image;
            
            $param = (object) [
                'type'  => 'image',
                'id'    => $mahasiswa->id
            ];
            
            Mahasiswa::deleteImage($param);
        }
        
        Mahasiswa::where('id', $mahasiswa->id)->update($data);
        
        $roles = Role::findOrFail($request->roles);

       $user = User::whereId($mahasiswa->user_id)->update([
            'name'        => $request->name,
            'nomor_induk' => $request->nomor_induk,
            'email'       => $request->email,
            'wa'          => 62 . $request->wa,
            'password'    => Hash::make($request->nomor_induk)
        ]);

        $user = User::where('id',$mahasiswa->user_id)->first();
        
        $user->syncRoles($roles);

        return redirect()->route('mahasiswa.index')->with('success', 'Data Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        // $resellerOrder =  ResellerOrder::where('reseller_id', $id)
        //     ->whereNotIn('order_status_id', [8, 9])
        //     ->first();

        // if ($resellerOrder) {
        //     return response()->json(['message' => 'Gagal hapus, masih ada transaksi yang berjalan', 'status' => 'error', 'code' => '500']);
        // }

        $param = (object) [
            'type'  => 'image',
            'id'    => $mahasiswa->id
        ];

        Mahasiswa::deleteImage($param);

        // $this->deleteMahasiswa($id);

        $mahasiswa->delete();

        User::where('id', $mahasiswa->user_id)->update(['status' => '0']);

        return response()->json(['status' => 'Data Berhasil Dihapus']);
    }
}
