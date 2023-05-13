@extends('layout.backend.base')
@section('content')
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>Tabel Data Instansi</h1>
      </div>

      <div class="section-body">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex justify-content-between w-100">
                    <h4>
                        Data Instansi
                    </h4>

                    <a href="{{ route('instansi.create') }}"
                        class="btn btn-outline-success btn-lg d-flex align-items-center ">
                        <i class="fa fa-plus pr-2"></i>
                        Tambah
                    </a>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="myTable">
                    <thead>
                      <tr>
                        <th class="text-center">
                            #
                        </th>
                        <th class="text-center">
                            Nama Perusahaan
                        </th>
                        <th class="text-center">
                            Alamat
                        </th>
                        <th class="text-center">
                            Aksi
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach ($instansi as $item)
                        <tr class="text-center">
                            <td>
                                {{$loop->iteration}}
                            </td>
                            <td>{{ $item->nama_perusahaan}}</td>
                            <td>{{ $item->alamat}}</td>
                            <td>
                                <a href="{{ route('instansi.show', Crypt::encryptString($item->id)) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                        width="16" height="16" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>
                                <a href="{{ route('instansi.edit', $item->id) }}" title="Edit" 
                                    class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button value="{{ route('instansi.destroy', $item->id) }}"
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