@extends('layouts.layout')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Form Jadwal</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Jadwal</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Jadwal</h4>
                        <form class="forms-sample" id="form-addjadwal">
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" id="tanggal"
                                    placeholder="Tanggal">
                                <span class="text-danger error-text tanggal_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="10"></textarea>
                            </div>
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="status">Aktif</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn_submit mr-2">Submit</button>
                            <button type="button" class="btn btn-light btn_canceljadwal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Table Jadwal</h4>
                    </p>
                    <table class="table table-hover" id="table-jadwal">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Jadwal</th>
                                <th>Keterangan</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Update</th>
                                <th>Status</th>
                                <th>Aksi</th>
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
    <script>
        $(function () {
            var table = $('#table-jadwal').DataTable({
                processing: false,
                serverSide: false,
                ajax: "/jadwal",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
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

    <script>
        $('.btn_canceljadwal').on('click', function () {
            $("#form-editjadwal").prop('id', 'form-addjadwal');
            $('#form-addjadwal').trigger("reset");
            $('#form-addjadwal').find('input[name="id"]').remove();
        });
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-addjadwal', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/jadwal',
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
                            $('.btn_canceljadwal').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil ditambahkan!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-jadwal').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>

    <script>
        $('#table-jadwal').on('click','#btn_edit',function(){
            var id = $(this).data('id');
            var url = '/jadwal';
            $("#form-addjadwal").prop('id','form-editjadwal');
            $("#form-editjadwal :input").prop("disabled", true);
            $('#form-editjadwal').find('input[name="id"]').remove();
            $('.btn_submit').prop('disabled', true);
            $.get(url, {
                id: id
            }, function (data) {
                $('#form-editjadwal').append('<input name="id" hidden readonly>');
                $('body').find('form').find('input[name="id"]').val(data.result.id);
                $('body').find('form').find('input[name="tanggal"]').val(data.result.tanggal);
                $('body').find('form').find('textarea[name="keterangan"]').val(data.result.keterangan);
                if (data.result.status == "Aktif") {
                    $('body').find('form').find('input[name="status"]').prop('checked', true);
                } else {
                    $('body').find('form').find('input[name="status"]').prop('checked', false);
                }
            }, 'json').done(function(){
                $("#form-editjadwal :input").prop("disabled", false);
                $('.btn_submit').prop('disabled', false);
            })
        })
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-editjadwal', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                let formData = new FormData(form);
                formData.append('_method', 'PATCH');
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/jadwal',
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
                            $('.btn_canceljadwal').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil diubah!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-jadwal').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>
    
    <script>
        $('#table-jadwal').on('click', '#btn_delete', function() {
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
                        url: '/jadwal',
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
                                $('.btn_canceljadwal').trigger('click');
                                $('#table-jadwal').DataTable().ajax.reload(null, false);
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
