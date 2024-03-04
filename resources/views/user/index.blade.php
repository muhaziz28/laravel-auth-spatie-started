@extends('layouts.app')

@section('content')
<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Users</h3>
                            <div class="card-tools">
                                @can('create-users')
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-user">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add New User
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="user-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th width="40%">Role</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('create-users')
<div class="modal fade" id="modal-add-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('user.store') }}" method="POST" id="form-add-user">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="title">Add New User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control role-select" name="role" id="role" style="width: 100%;">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(function() {
        function defineColumns() {
            return [{
                    data: 'DT_RowIndex',
                    class: 'table-td'
                },
                {
                    data: 'name',
                },
                {
                    data: 'roles',
                    render: function(data, type, row) {
                        if (data.length > 0) {

                            return data[0].name;
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="flex items-center justify-end space-x-2">
                        @can('update-users')
                            <button class="btn btn-sm btn-outline-primary edit" data-id="${data.id}">Edit</button>
                        @endcan
                        @can('delete-users')
                            <button class="btn btn-sm btn-outline-danger delete" data-id="${data.id}">Delete</button>
                        @endcan
                        </div>`;
                    }
                }
            ]
        }

        var table = $('#user-table');
        var config = {
            processing: true,
            serverSide: true,
            ajax: "{{ route('user.data') }}",
            paging: true,
            ordering: true,
            info: false,
            searching: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],

            columns: defineColumns()
        };

        initializeDataTable(table, config);

        $('#form-add-user').on('submit', function(e) {
            e.preventDefault();
            var form = new FormData(this)
            $.ajax({
                url: $(this).attr('action'),
                method: "POST",
                data: form,
                processData: false,
                dataType: 'json',
                contentType: false,
                beforeSend: function() {
                    $('#form-add-user button[type="submit"]').attr('disabled', true);
                    $('#form-add-user button[type="submit"]').html('<iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" icon="line-md:loading-twotone-loop"></iconify-icon><span>Loading</span>');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-add-user').modal('hide');
                        $('#form-add-user')[0].reset();
                        toastr.success(response.message);
                        table.DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                    $('#form-add-user button[type="submit"]').attr('disabled', false);
                    $('#form-add-user button[type="submit"]').html('Submit');
                }

            })
        })

        $(document).on('click', '.delete', function() {
            var id = $(this).data('id')
            console.log(id);
            confirm('Are you sure you want to delete this user?');

            $.ajax({
                url: '{{ route("user.destroy") }}',
                method: "DELETE",
                data: {
                    id: id
                },
                success: function(response) {
                    table.DataTable().ajax.reload();
                }
            })
        })

        $(document).on('click', '.edit', function(e) {
            e.preventDefault()
            var data = table.DataTable().row($(this).closest('tr')).data();

            $('#modal-add-user').modal('show');
            $('#modal-add-user').find('#title').text('Edit User');
            $('#form-add-user').attr('action', '{{ route("user.update") }}');
            $('#form-add-user').append('<input type="hidden" name="_method" value="PUT">');
            $('#form-add-user').append('<input type="hidden" name="id" value="' + data.id + '">');

            $('#form-add-user input[name="name"]').val(data.name);
            $('#form-add-user input[name="email"]').val(data.email);
            // disable input password
            $('#form-add-user input[name="password"]').attr('disabled', true);
            var role = new Option(data.roles[0].name, data.roles[0].id, true, true);
            $('#form-add-user .role-select').append(role).trigger('change');


        })

        $('#modal-add-user').on('hidden.bs.modal', function() {
            $('#modal-add-user').find('#title').text('Add User');
            $('#form-add-user input[name="_method"]').remove();
            $('#form-add-user input[name="id"]').remove();
            $('#form-add-user').attr('action', '{{ route("user.store") }}');
            $('#form-add-user')[0].reset();
            $('#form-add-user .role-select').val(null).trigger('change');
            $('#form-add-user input[name="password"]').attr('disabled', false);
        })

        $('.role-select').select2({
            placeholder: 'Select a role',
            ajax: {
                url: '{{ route("role.data") }}',
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data.data.map(function(role) {
                            return {
                                id: role.id,
                                text: role.name
                            }
                        })
                    }
                }
            }
        })
    })
</script>
@endpush