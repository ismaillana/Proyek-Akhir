<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Pengajuan;
use App\Models\Riwayat;
use App\Models\TempatPkl;
use App\Models\JenisPengajuan;

use App\Http\Requests\PengantarPklRequest;
use App\Http\Requests\KonfirmasiRequest;
use App\Exports\PengantarPklExport;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class PengantarPklController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin-jurusan')) {
            $pengantarPkl = Pengajuan::where('jenis_pengajuan_id', 2)
                ->whereNot('status', 'Selesai')
                ->whereNot('status', 'Tolak')
                ->whereNot('status', 'Diterima Perusahaan')
                ->whereNot('status', 'Ditolak Perusahaan')
                ->get();

            return view ('admin.pengajuan.pengantar-pkl.index-admin-jurusan',[
                'pengantarPkl' => $pengantarPkl,
                'user' => $user,
                'title'     => 'Pengantar PKL'
            ]);

        } elseif ($user->hasRole('koor-pkl')) {
            $pengantarPkl = Pengajuan::where('jenis_pengajuan_id', 2)
                ->where('status', 'Review')
                ->get();

            return view ('admin.pengajuan.pengantar-pkl.index-koor-pkl',[
                'pengantarPkl' => $pengantarPkl,
                'user'      => $user,
                'title'        => 'Pengantar PKL'
            ]);
        } else {
            $pengantarPkl = Pengajuan::where('jenis_pengajuan_id', 2)
            ->whereNot('status', 'Selesai')
            ->whereNot('status', 'Tolak')
            ->whereNot('status', 'Diterima Perusahaan')
            ->whereNot('status', 'Ditolak Perusahaan')
            ->get();

            return view ('admin.pengajuan.pengantar-pkl.index',[
                'pengantarPkl' => $pengantarPkl,
                'title'     => 'Pengantar PKL'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pengaju = auth()->user();

        $mahasiswa       = Mahasiswa::with(['pengajuan'])->whereUserId($pengaju->id)->first();

        $pengajuan = Pengajuan::where('mahasiswa_id', $mahasiswa->id)
            ->where('jenis_pengajuan_id', 2)
            ->latest()
            ->first();

        $tempatPkl = TempatPkl::get();

        $user = User::whereHas('roles', function ($q)
        {
            $q->whereIn('name', ['mahasiswa']);
        })
        ->get();
        
        return view ('user.pengajuan.pengantar-pkl.form', [
            'user'      => $user,
            'pengajuan' => $pengajuan,
            'tempatPkl' => $tempatPkl,
            'title'     => 'Pengantar PKL'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PengantarPklRequest $request)
    {
        $user = auth()->user();

        $alumni       = Mahasiswa::whereUserId($user->id)->first();

        $tempat_pkl_id = $request->tempat_pkl_id;
        if ($request->tempat_pkl_id == 'perusahaan_lainnya') {
            $tempatPkl = TempatPkl::create([
                'name'      => $request->name,
                'alamat'    => $request->alamat,
                'telepon'   => $request->telepon,
                'web'       => $request->web
            ]);

            $tempat_pkl_id = $tempatPkl->id;
        }
        
        $data = ([
            'jenis_pengajuan_id' => '2',
            'mahasiswa_id'     => $alumni->id,
            'tempat_pkl_id'    => $tempat_pkl_id,
            'tgl_mulai'        => $request->tgl_mulai,
            'tgl_selesai'      => $request->tgl_selesai,
            'tujuan_surat'     => $request->tujuan_surat,
            'nama_mahasiswa'   => $request->nama_mahasiswa
        ]);

        $pengajuan = Pengajuan::create($data);

        Riwayat::create([
            'pengajuan_id'  => $pengajuan->id,
            'status'        => 'Menunggu Konfirmasi',
            'catatan'       => 'Pengajuan Berhasil Dibuat. Tunggu pemberitahuan selanjutnya'
        ]);

        return redirect()->back()->with('success', 'Pengajuan Berhasil');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $pengantarPkl = Pengajuan::find($id);
        $data = Mahasiswa::whereIn('user_id', $pengantarPkl['nama_mahasiswa'])->get();
        
        return view ('admin.pengajuan.pengantar-pkl.detail', [
            'pengantarPkl'    =>  $pengantarPkl,
            'data'          => $data,
            'user'          => $user,
            'title'         =>  'Detail Pengajuan Pengantar PKL'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function konfirmasi(Request $request, string $id)
    {
        $request->validate([
            'dokumen_permohonan' => 'required',
        ], [
            'dokumen_permohonan.required' => 'Masukkan Dokumen Permohonan',
        ]);

        $dokumen = Pengajuan::saveDokumenPermohonan($request);

        $data = [
            'status'  =>  'Konfirmasi',
            'dokumen_permohonan' => $dokumen
        ];

        Pengajuan::where('id', $id)->update($data);

        Riwayat::create([
            'pengajuan_id'  => $id,
            'status'        => 'Dikonfirmasi',
            'catatan'       => 'Pengajuan Anda Telah Dikonfirmasi. Tunggu pemberitahuan selanjutnya'
        ]);

        return redirect()->back()->with('success', 'Status Berhasil Diubah');
    }

    /**
     * Update the specified resource in storage.
     */
    public function review(Request $request, string $id)
    {
        $data = [
            'status'  =>  'Review'
        ];

        Pengajuan::where('id', $id)->update($data);

        Riwayat::create([
            'pengajuan_id'  => $id,
            'status'        => 'Direview',
            'catatan'       => 'Pengajuan Anda Sedang di review oleh Koordinator Pkl. Tunggu pemberitahuan selanjutnya'
        ]);

        return redirect()->back()->with('success', 'Status Berhasil Diubah');
    }

    /**
     * Update the specified resource in storage.
     */
    public function setuju(Request $request, string $id)
    {
        $data = [
            'status'  =>  'Setuju'
        ];

        Pengajuan::where('id', $id)->update($data);

        Riwayat::create([
            'pengajuan_id'  => $id,
            'status'        => 'Disetujui Koor.Pkl',
            'catatan'       => 'Pengajuan Anda Telah Disetujui oleh koordinator Pkls. Tunggu pemberitahuan selanjutnya'
        ]);

        return redirect()->back()->with('success', 'Status Berhasil Diubah');
    }

    /**
     * Update the specified resource in storage.
     */
    public function tolak(KonfirmasiRequest $request, string $id)
    {
        $data = [
            'status'  =>  'Tolak',
            'catatan' =>  $request->catatan
        ];

        Riwayat::create([
            'pengajuan_id'  => $id,
            'status'        => 'Ditolak',
            'catatan'       => $request->catatan
        ]);

        Pengajuan::where('id', $id)->update($data);

        return redirect()->back()->with('success', 'Status Berhasil Diubah');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, string $id)
    {
        $data = [
            'status'  =>  $request->status
        ];

        Pengajuan::where('id', $id)->update($data);

        if ($request->status == 'Proses' ) {
            Riwayat::create([
                'pengajuan_id'  => $id,
                'status'        => 'Diproses',
                'catatan'       => 'Pengajuan Anda Sedang Diproses. Tunggu pemberitahuan selanjutnya'
            ]);
        }elseif ($request->status == 'Kendala' ) {
            Riwayat::create([
                'pengajuan_id'  => $id,
                'status'        => 'Ada Kendala',
                'catatan'       => 'Pengajuan Anda Sedang Dalam Kendala. Tunggu pemberitahuan selanjutnya'
            ]);
        }elseif ($request->status == 'Selesai' ) {
            Riwayat::create([
                'pengajuan_id'  => $id,
                'status'        => 'Selesai',
                'catatan'       => 'Pengajuan Anda Sudah Selesai. Ambil Dokumen Di Ruangan AKademik'
            ]);
        }
        return redirect()->back()->with('success', 'Status Berhasil Diubah');
    }

    /**
     * Display a listing of the resource.
     */
    public function riwayat()
    {
        $user = auth()->user();

        $pengantarPkl = Pengajuan::where('jenis_pengajuan_id',2)
            ->where('status', 'Tolak')
            ->orWhere('jenis_pengajuan_id',2)
            ->where('status', 'Selesai')
            ->orWhere('jenis_pengajuan_id',2)
            ->where('status', 'Diterima Perusahaan')
            ->orWhere('jenis_pengajuan_id',2)
            ->where('status', 'Ditolak Perusahaan')
            ->get();

        if ($user->hasRole('admin-jurusan')) {
            return view ('admin.riwayat.pengantar-pkl.index-admin-jurusan', [
                'pengantarPkl'  => $pengantarPkl,
                'user'          => $user,
                'title'         => 'Surat Pengantar PKL'
            ]);
        } elseif ($user->hasRole('koor-pkl')) {
            return view ('admin.riwayat.pengantar-pkl.index-koor-pkl', [
                'pengantarPkl'  => $pengantarPkl,
                'user'          => $user,
                'title'         => 'Surat Pengantar PKL'
            ]);
        } else {
            return view ('admin.riwayat.pengantar-pkl.index', [
                'pengantarPkl'   => $pengantarPkl,
                'title'         => 'Surat Pengantar PKL'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function showRiwayat(string $id)
    {
        try {
            $id = Crypt::decryptString($id);
        } catch (DecryptException $e) {
            abort(404);
        }

        $pengantarPkl = Pengajuan::find($id);

        $data = Mahasiswa::whereIn('user_id', $pengantarPkl['nama_mahasiswa'])->get();

        return view ('admin.riwayat.pengantar-pkl.detail', [
            'pengantarPkl'  =>  $pengantarPkl,
            'data'          =>  $data,
            'title'         =>  'Detail Pengajuan Pengantar Pkl'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date'   => 'required',
        ], [
            'start_date.required' => 'Masukkan Tanggal Mulai',
            'end_date.required'   => 'Masukkan Tanggal Selesai',
        ]);
        
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

        $data = Pengajuan::with(['mahasiswa'])
            ->where('jenis_pengajuan_id', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return Excel::download(new PengantarPklExport($data), 'Pengantar-Pkl-Export.xlsx');
    }
}
