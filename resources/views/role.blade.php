@extends('layouts.layout')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Form Role</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">User Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role Management</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Role</h4>
                        <form class="forms-sample" id="form-addrole">
                            <div class="form-group">
                                <label for="name">Nama Role</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Nama Role">
                                <span class="text-danger error-text name_error"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn_submit mr-2">Submit</button>
                            <button type="button" class="btn btn-light btn_cancelrole">Cancel</button>
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
                    <table class="table table-hover" id="table-role">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Role</th>
                                <th>Tanggal Dibuat</th>
                                <th>Tanggal Update</th>
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
            var table = $('#table-role').DataTable({
                processing: false,
                serverSide: false,
                ajax: "/role",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
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
        $('.btn_cancelrole').on('click', function () {
            $("#form-editrole").prop('id', 'form-addrole');
            $('#form-addrole').trigger("reset");
            $('#form-addrole').find('input[name="id"]').remove();
        });
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-addrole', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/role',
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
                            $('.btn_cancelrole').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil ditambahkan!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-role').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>

    <script>
        $('#table-role').on('click','#btn_edit',function(){
            var id = $(this).data('id');
            var url = '/role';
            $("#form-addrole").prop('id','form-editrole');
            $("#form-editrole :input").prop("disabled", true);
            $('#form-editrole').find('input[name="id"]').remove();
            $('.btn_submit').prop('disabled', true);
            $.get(url, {
                id: id
            }, function (data) {
                $('#form-editrole').append('<input name="id" hidden readonly>');
                $('body').find('form').find('input[name="id"]').val(data.result.id);
                $('body').find('form').find('input[name="name"]').val(data.result.name);
            }, 'json').done(function(){
                $("#form-editrole :input").prop("disabled", false);
                $('.btn_submit').prop('disabled', false);
            })
        })
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-editrole', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                let formData = new FormData(form);
                formData.append('_method', 'PATCH');
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/role',
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
                            $('.btn_cancelrole').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil diubah!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-role').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>
    
    <script>
        $('#table-role').on('click', '#btn_delete', function() {
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
                        url: '/role',
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
                                $('.btn_cancelrole').trigger('click');
                                $('#table-role').DataTable().ajax.reload(null, false);
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
