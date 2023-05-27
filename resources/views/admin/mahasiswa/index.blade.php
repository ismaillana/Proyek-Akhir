@extends('layout.backend.base')
@section('content')
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>Tabel Data Mahasiswa</h1>
      </div>

      <div class="section-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between w-100">
                    <h4>
                        Data Mahasiswa
                    </h4>
                    <div class="d-flex justify-content-between">
                        <a href="{{route('import-excel')}}"
                            class="btn btn-outline-info btn-lg d-flex align-items-center mr-1 ">
                            <i class="fa fa-plus pr-2"></i>
                            Upload Excel
                        </a>
    
                        <a href="{{ route('mahasiswa.create') }}"
                            class="btn btn-outline-success btn-lg d-flex align-items-center ">
                            <i class="fa fa-plus pr-2"></i>
                            Tambah
                        </a>
                    </div>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="myTable">
                    <thead>
                      <tr>
                        <th style="width: 10%">
                            #
                        </th>

                        <th>
                            Nama Mahasiswa
                        </th>

                        <th>
                            NIM
                        </th>

                        <th>
                            Angkatan
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
                        @foreach ($mahasiswa as $item)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>

                                <td>
                                    {{ $item->user->name}}
                                </td>

                                <td>
                                    {{ $item->nim}}
                                </td>

                                <td>
                                    {{ $item->angkatan}}
                                </td>

                                <td class="text-center">
                                    @if ($item->status == 'Mahasiswa Aktif')
                                        <span class="badge badge-success">Mahasiswa Aktif</span>
                                    @else
                                        <span class="badge badge-warning">Alumni</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('mahasiswa.show', Crypt::encryptString($item->id)) }}"
                                        class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            width="16" height="16" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('mahasiswa.edit', Crypt::encryptString($item->id)) }}" title="Edit" 
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    <button value="{{ route('mahasiswa.destroy', $item->id) }}"
                                        class="btn btn-sm btn-outline-danger delete" title="Hapus"> 
                                        <i class="fas fa-trash"></i>
                                    </button>
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