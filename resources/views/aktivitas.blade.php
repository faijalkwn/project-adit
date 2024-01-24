@extends('layouts.layout')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Form Aktivitas</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aktivitas</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Aktivitas</h4>
                        <form class="forms-sample" id="form-addaktivitas" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="id_jadwal">Jadwal</label>
                                <select name="id_jadwal" id="id_jadwal" class="form-control">
                                    <option value="">Pilih Jadwal</option>
                                    @foreach($jadwal->chunk(10) as $row)
                                        @foreach ($row as $data)
                                            <option value="{{ $data->id }}">{{ $data->tanggal }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <span class="text-danger error-text id_jadwal_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="aktivitas">Aktivitas</label>
                                <input type="text" class="form-control" name="aktivitas" id="aktivitas"
                                    placeholder="Aktivitas">
                                <span class="text-danger error-text aktivitas_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="10"></textarea>
                                <span class="text-danger error-text keterangan_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">File</label>
                                <input type="file" class="form-control" name="file" id="file"
                                    placeholder="File">
                                <span class="text-danger error-text file_error"></span>
                            </div>
                            @role('Admin|Atasan')
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="status">Done</label>
                            </div>
                            @endrole
                            <button type="submit" class="btn btn-primary btn_submit mr-2">Submit</button>
                            <button type="button" class="btn btn-light btn_cancelaktivitas">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Table Role</h4>
                    </p>
                    <table class="table table-hover" id="table-aktivitas">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jadwal Aktivitas</th>
                                <th>Aktivitas</th>
                                <th>Keterangan</th>
                                <th>File</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Update</th>
                                <th>Status</th>
                                @role('Admin|Atasan')
                                <th>Aksi</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @role('Admin|Atasan')
    <script>
        $(function () {
            var table = $('#table-aktivitas').DataTable({
                processing: false,
                serverSide: false,
                ajax: "/aktivitas",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'jadwal.tanggal',
                        name: 'jadwal.tanggal'
                    },
                    {
                        data: 'aktivitas',
                        name: 'aktivitas'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'file',
                        name: 'file'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
    @endrole

    @role('Pejabat')
    <script>
        $(function () {
            var table = $('#table-aktivitas').DataTable({
                processing: false,
                serverSide: false,
                ajax: "/aktivitas",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'jadwal.tanggal',
                        name: 'jadwal.tanggal'
                    },
                    {
                        data: 'aktivitas',
                        name: 'aktivitas'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'file',
                        name: 'file'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ]
            });
        });
    </script>
    @endrole
    <script>
        $('.btn_cancelaktivitas').on('click', function () {
            $("#form-editaktivitas").prop('id', 'form-addaktivitas');
            $('#form-addaktivitas').trigger("reset");
            $('#form-addaktivitas').find('input[name="id"]').remove();
        });
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-addaktivitas', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/aktivitas',
                    method: 'POST',
                    data: new FormData(form),
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(form).find('span.error-text').text('');
                    },

                    success: function(data) {
                        if (data.code == 0) {
                            $.each(data.error, function(prefix, val) {
                                $(form).find('span.' + prefix + '_error').text(val[0]);
                                $('.btn_submit').prop('disabled', false);
                            });
                        } else {
                            $('.btn_cancelaktivitas').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil ditambahkan!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-aktivitas').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>

    <script>
        $('#table-aktivitas').on('click','#btn_edit',function(){
            var id = $(this).data('id');
            var url = '/aktivitas';
            $("#form-addaktivitas").prop('id','form-editaktivitas');
            $("#form-editaktivitas :input").prop("disabled", true);
            $('#form-editaktivitas').find('input[name="id"]').remove();
            $('.btn_submit').prop('disabled', true);
            $.get(url, {
                id: id
            }, function (data) {
                $('#form-editaktivitas').append('<input name="id" hidden readonly>');
                $('body').find('form').find('input[name="id"]').val(data.result.id);
                $('select[name="id_jadwal"]').val(data.result.id_jadwal).trigger('change');
                $('body').find('form').find('input[name="aktivitas"]').val(data.result.aktivitas);
                $('body').find('form').find('textarea[name="keterangan"]').val(data.result.keterangan);
                if (data.result.status == "Done") {
                    $('body').find('form').find('input[name="status"]').prop('checked', true);
                } else {
                    $('body').find('form').find('input[name="status"]').prop('checked', false);
                }
            }, 'json').done(function(){
                $("#form-editaktivitas :input").prop("disabled", false);
                $('.btn_submit').prop('disabled', false);
            })
        })
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-editaktivitas', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                let formData = new FormData(form);
                formData.append('_method', 'PATCH');
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/aktivitas',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(form).find('span.error-text').text('');
                    },

                    success: function(data) {
                        if (data.code == 0) {
                            $.each(data.error, function(prefix, val) {
                                $(form).find('span.' + prefix + '_error').text(val[0]);
                                $('.btn_submit').prop('disabled', false);
                            });
                        } else {
                            $('.btn_cancelaktivitas').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil diubah!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-aktivitas').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>
    
    <script>
        $('#table-aktivitas').on('click', '#btn_delete', function() {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak akan bisa kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = $(this).data('id');
                    $.ajax({
                        headers: {
                            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "DELETE",
                        url: '/aktivitas',
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function(data) {
                            if (data.code == 1) {
                                Swal.fire(
                                    'Deleted!',
                                    'Data berhasil dihapus!',
                                    'success'
                                )
                                $('.btn_cancelaktivitas').trigger('click');
                                $('#table-aktivitas').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan!',
                                    'error'
                                )
                            }
                        }
                    });
                }
            })
        });
    </script>
@endpush
