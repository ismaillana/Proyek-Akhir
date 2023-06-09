@extends('layout.backend.base')
@section('content')
  <div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>Tabel Data Pengajuan Legalisir</h1>
      </div>

      <div class="section-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between w-100">
                    <h4>
                        Data Pengajuan Legalisir
                    </h4>
                </div>
              </div>
              <div class="card-body">
                <form id="myForm" class="forms-sample" enctype="multipart/form-data" 
                    action="{{route('export-legalisir')}}" method="GET">
                    <div class="row align-items-center" style="margin-bottom: 10px;">
                        <div class="col-md-3 col-sm-12">
                            <label for="start_date" class="label-control">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                placeholder="Tanggal Mulai" value="{{ old('start_date', request('start_date')) }}">
                            
                            @if ($errors->has('start_date'))
                                <span class="text-danger">
                                    {{ $errors->first('start_date') }}
                                </span>
                            @endif
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <label for="end_date" class="label-control">Tanggal Akhir</label>
                            <input type="date" id="end_date" name="end_date" class="form-control"
                                placeholder="Tanggal Akhir" value="{{ old('end_date', request('end_date')) }}">
                                
                            @if ($errors->has('end_date'))
                                <span class="text-danger">
                                    {{ $errors->first('end_date') }}
                                </span>
                            @endif
                        </div>

                        <div class="col-md-2 col-sm-12 d-flex mt-auto">
                            <button id="btn-submit" type="submit"
                                class="btn btn-success btn-block">Export Excel</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                  <table class="table table-striped" id="myTable">
                    <thead>
                      <tr>
                        <th style="width: 10%">
                            #
                        </th>

                        <th>
                            Pengaju
                        </th>

                        {{-- <th>
                            Nomor Ijazah
                        </th>

                        <th>
                            Tahun Lulus
                        </th> --}}

                        <th>
                            Jenis Dokumen
                        </th>

                        <th class="text-center">
                            Dokumen
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                        <th class="text-center">
                            Aksi
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($legalisir as $item)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>

                                <td>
                                    {{@$item->mahasiswa->user->name}}
                                </td>

                                {{-- <td>
                                    {{@$item->ijazah->no_ijazah}}
                                </td>

                                <td>
                                    {{$item->ijazah->tahun_lulus}}
                                </td> --}}

                                <td>
                                    @if ($item->jenis_legalisir)
                                        @foreach ( $item->jenis_legalisir as $dokumen)
                                            {{$dokumen}}
                                        @endforeach
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ asset('storage/public/dokumen/legalisir/'. $item->dokumen)}}" download="{{$item->dokumen}}">
                                        <p>
                                            File Pengajuan
                                        </p>
                                    </a>
                                </td>
                                
                                <td class="text-center">
                                    @if ($item->status == 'Tolak')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @else
                                        <span class="badge badge-success">Selesai</span>
                                    @endif
                                </td>
                                
                                <td class="text-center">
                                    <a href="{{ route('riwayat-pengajuan-legalisir-detail',  Crypt::encryptString($item->id)) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            width="16" height="16" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div> 
@endsection

@section('script')
    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.delete', function() {
                let url = $(this).val();
                console.log(url);
                swal({
                        title: "Apakah anda yakin?",
                        text: "Setelah dihapus, Anda tidak dapat memulihkan Tag ini lagi!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                type: "DELETE",
                                url: url,
                                dataType: 'json',
                                success: function(response) {
                                    swal(response.status, {
                                            icon: "success",
                                        })
                                        .then((result) => {
                                            location.reload();
                                        });
                                }
                            });
                        }
                    })
            });
        });
    </script>
@endsection