@extends('layouts.app')

@section('content')
<div class="content-wrapper">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Permission</h1>
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
                            <h3 class="card-title">Permission</h3>
                            <div class="card-tools">
                                @can('create-permissions')
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-permission">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add New Permission
                                </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="permission-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th width="40%">Permission Name</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Permission Name</th>
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

@can('create-permissions')
<div class="modal fade" id="modal-add-permission">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('permission.store') }}" method="POST" id="form-add-permission">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="title">Add New Permission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="permission">Permission Name</label>
                        <input type="text" class="form-control" id="permission" name="permission" placeholder="Permission name">
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
                    class: 'table-td'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div class="flex items-center justify-end space-x-2">
                        @can('update-permissions')
                            <button class="btn btn-sm btn-outline-primary edit" data-id="${data.id}">Edit</button>
                        @endcan
                        @can('delete-permissions')
                            <button class="btn btn-sm btn-outline-danger delete" data-id="${data.id}">Delete</button>
                        @endcan
                        </div>`;
                    }
                }
            ]
        }

        var table = $('#permission-table');
        var config = {
            processing: true,
            serverSide: true,
            ajax: "{{ route('permission.data') }}",
            paging: true,
            ordering: true,
            info: false,
            searching: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],

            columns: defineColumns()
        };

        initializeDataTable(table, config);

        $('#form-add-permission').on('submit', function(e) {
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
                    $('#form-add-permission button[type="submit"]').attr('disabled', true);
                    $('#form-add-permission button[type="submit"]').html('<iconify-icon class="text-xl spin-slow ltr:mr-2 rtl:ml-2 relative top-[1px]" icon="line-md:loading-twotone-loop"></iconify-icon><span>Loading</span>');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modal-add-permission').modal('hide');
                        $('#form-add-permission')[0].reset();
                        toastr.success(response.message);
                        table.DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                    $('#form-add-permission button[type="submit"]').attr('disabled', false);
                    $('#form-add-permission button[type="submit"]').html('Submit');
                }

            })
        })

        $(document).on('click', '.delete', function() {
            var id = $(this).data('id')
            console.log(id);
            var result = confirm('Are you sure you want to delete this permission?');

            if(result) {
                $.ajax({
                url: '{{ route("permission.destroy") }}',
                method: "DELETE",
                data: {
                    id: id
                },
                success: function(response) {
                    table.DataTable().ajax.reload();
                }
            })
            }
        })

        $(document).on('click', '.edit', function(e) {
            e.preventDefault()
            var data = table.DataTable().row($(this).closest('tr')).data();

            $('#modal-add-permission').modal('show');
            $('#modal-add-permission').find('#title').text('Edit Permission');
            $('#form-add-permission').attr('action', '{{ route("permission.update") }}');
            $('#form-add-permission').append('<input type="hidden" name="_method" value="PUT">');
            $('#form-add-permission').append('<input type="hidden" name="id" value="' + data.id + '">');
            $('#permission').val(data.name);
        })

        $('#modal-add-permission').on('hidden.bs.modal', function() {
            $('#modal-add-permission').find('#title').text('Add Permission');
            $('#form-add-permission input[name="_method"]').remove();
            $('#form-add-permission input[name="id"]').remove();
            $('#form-add-permission').attr('action', '{{ route("permission.store") }}');
            $('#form-add-permission')[0].reset();
        })
    })
</script>
@endpush