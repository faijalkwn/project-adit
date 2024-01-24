@extends('layouts.layout')
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Form User Management</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">User Management</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Management</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-6 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Assign Role</h4>
                        <form class="forms-sample" id="form-assignrole">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" name="email" id="email"
                                    placeholder="Email">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">Pilih Role</option>
                                    @foreach($role->chunk(10) as $row)
                                        @foreach ($row as $data)
                                            <option value="{{ $data->name }}">{{ $data->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <span class="text-danger error-text role_error"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn_submit mr-2">Submit</button>
                            <button type="button" class="btn btn-light btn_cancelassign">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Tambah User</h4>
                        <form class="forms-sample" id="form-adduser">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" name="email" id="email"
                                    placeholder="Email">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Nama">
                                <span class="text-danger error-text name_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password"
                                    placeholder="Password">
                                <span class="text-danger error-text password_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Password Confirmation</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                                    placeholder="Password Confirmation">
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">Pilih Role</option>
                                    @foreach($role->chunk(10) as $row)
                                        @foreach ($row as $data)
                                            <option value="{{ $data->name }}">{{ $data->name }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <span class="text-danger error-text role_error"></span>
                            </div>
                            <button type="submit" class="btn btn-primary btn_submit mr-2">Submit</button>
                            <button type="button" class="btn btn-light btn_canceluser">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Table User</h4>
                    </p>
                    <table class="table table-hover" id="table-user">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Email</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Tanggal Dibuat</th>
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
            var table = $('#table-user').DataTable({
                processing: false,
                serverSide: false,
                ajax: "/user",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'roles[0].name',
                        name: 'roles[0].name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
        $('.btn_cancelassign').on('click', function () {
            $('#form-assignrole').trigger("reset");
            $('#form-assignrole').find('input[name="id"]').remove();
        });
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-assignrole', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/user/assign',
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
                        } else if(data.code == 2){
                            $(form).find('span.' + 'email_error').text("Email tidak terdaftar!");
                            $('.btn_submit').prop('disabled', false);
                        } else {
                            $('.btn_cancelassign').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil ditambahkan!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-user').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>

    <script>
        $('#table-user').on('click','#btn_assign',function(){
            var id = $(this).data('id');
            var url = '/user';
            $("#form-assignrole :input").prop("disabled", true);
            $('#form-assignrole').find('input[name="id"]').remove();
            $('.btn_submit').prop('disabled', true);
            $.get(url, {
                id: id
            }, function (data) {
                $('#form-assignrole').append('<input name="id" hidden readonly>');
                $('body').find('form').find('input[name="id"]').val(data.result.id);
                $('body').find('form').find('input[name="email"]').val(data.result.email);
                $('select[name="role"]').val(data.result.roles[0].name).trigger('change');
            }, 'json').done(function(){
                $("#form-assignrole :input").prop("disabled", false);
                $('.btn_submit').prop('disabled', false);
            })
        })
    </script>

    <script>
        $('.btn_canceluser').on('click', function () {
            $('#form-adduser').trigger("reset");
        });
    </script>

    <script>
        $(function() {
            $('body').on('submit', '#form-adduser', function(e) {
                e.preventDefault();
                $('.btn_submit').prop('disabled', true);

                var form = this;
                $.ajax({
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/user',
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
                            $('.btn_canceluser').trigger("click");
                            Swal.fire(
                                'Success!',
                                'Data berhasil ditambahkan!',
                                'success'
                            )
                            $('.btn_submit').prop('disabled', false);
                            $('#table-user').DataTable().ajax.reload(null, false);
                        }
                    }
                });
            });
        })
    </script>
@endpush
