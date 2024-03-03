@extends('layouts.app')

@section('content')
<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Role</h1>
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
                            <h3 class="card-title">Roles</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-role">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add New Role
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="role-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Role</th>
                                        <th width="30%">Permissions</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Role</th>
                                        <th>Permissions</th>
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

<div class="modal fade" id="modal-add-role">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('role.store') }}" method="POST" id="form-add-role">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Add New Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role">Role Name</label>
                        <input type="text" class="form-control" id="role" name="role" placeholder="Role name">
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
                    class: 'table-td'
                },
                {
                    data: 'permissions',
                    class: 'table-td',
                    orderable: false,
                    render: function(data, type, row) {
                        if (data.length > 0) {
                            let createColor = 'bg-primary-500';
                            let readColor = 'bg-success-500';
                            let updateColor = 'bg-warning-500';
                            let deleteColor = 'bg-danger-500';
                            let colors = [createColor, readColor, updateColor, deleteColor];
                            let permissions = data.map(permission => permission.name);
                            let badge = permissions.map((permission, index) => {
                                // return `<span class="badge ${colors[index]} text-white capitalize">${permission}</span>`;
                                //gunakan warna random
                                return `<span class="badge ${colors[Math.floor(Math.random() * colors.length)]} text-white">${permission}</span>`;

                            }).join(' ');
                            return badge;
                        } else {
                            return 'No Permission';
                        }
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (data.name != 'admin') {
                            return `<div class="flex items-center justify-end space-x-2">
                            <button class="btn btn-sm btn-outline-success permission" data-id="${data.id}">Permission</button>
                            <button class="btn btn-sm btn-outline-primary edit" data-id="${data.id}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger delete" data-id="${data.id}">Delete</button>
                        </div>`;
                        }

                        return '';

                    }
                }
            ];
        }

        var table = $('#role-table');
        var config = {
            processing: true,
            serverSide: true,
            ajax: "{{ route('role.data') }}",
            paging: true,
            ordering: true,
            info: false,
            searching: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            columns: defineColumns()
        };

        initializeDataTable(table, config);

        $('#form-add-role').on('submit', function(e) {
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
                    $('#form-add-role button[type="submit"]').attr('disabled', true);
                    $('#form-add-role button[type="submit"]').html('Loading...');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-add-role').modal('hide');
                        $('#form-add-role')[0].reset();
                        toastr.success(response.message);
                        table.DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                    $('#form-add-role button[type="submit"]').attr('disabled', false);
                    $('#form-add-role button[type="submit"]').html('Save');
                }

            })
        })


        $(document).on('click', '.delete', function() {
            var id = $(this).data('id')
            console.log(id);
            confirm('Are you sure you want to delete this role?');

            $.ajax({
                url: '{{ route("role.destroy") }}',
                method: "DELETE",
                data: {
                    id: id
                },
                success: function(response) {
                    toastr.success(response.message);
                    table.DataTable().ajax.reload();
                }
            })
        })

        $(document).on('click', '.edit', function(e) {
            e.preventDefault()
            var data = table.DataTable().row($(this).closest('tr')).data();

            $('#modal-add-role').modal('show');
            $('#modal-add-role').find('#title').text('Edit Role');
            $('#form-add-role').attr('action', '{{ route("role.update") }}');
            $('#form-add-role').append('<input type="hidden" name="_method" value="PUT">');
            $('#form-add-role').append('<input type="hidden" name="id" value="' + data.id + '">');
            $('#role').val(data.name);
        })

        $('#modal-add-role').on('hidden.bs.modal', function() {
            $('#modal-add-role').find('#title').text('Add Role');
            $('#form-add-role input[name="_method"]').remove();
            $('#form-add-role input[name="id"]').remove();
            $('#form-add-role').attr('action', '{{ route("role.store") }}');
            $('#form-add-role')[0].reset();
        })
    })
</script>
@endpush