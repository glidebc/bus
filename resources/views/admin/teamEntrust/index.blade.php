@extends('admin.layouts.master')

@section('content')

@if ($entrusts->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">團隊委刊單列表</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                <thead>
                    <tr>
                        <!-- <th>
                            {!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}
                        </th> -->
                        <th>&nbsp;</th>
                        <th>委刊單</th>
<th>客戶</th>
<th>總走期</th>
<th>部門</th>
<th>使用者</th>
<th>審核狀態</th>
<th>執行狀態</th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($entrusts as $row)
                        @if($row->status_publish == '執行中')
                        <tr class="text-primary">
                        @else
                        <tr>
                        @endif
                            <!-- <td>
                                {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                            </td> -->
                            <td>
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrust.read', $row->id))) !!}
                                {!! Form::submit('查看', array('class' => 'hide', 'id' => 'read-'.$row->id)) !!}
                                <span class="fa fa-file-o btn-read" onclick='$("#read-{{ $row->id }}").click();'></span>
                                {!! Form::close() !!}
                            </td>
                            <td>{{ $row->name }}</td>
<td>{{ $row->customer_name }}</td>
<td>{{ $row->duration }}</td>
<td>{{ $row->user_dept }}</td>
<td>{{ $row->user_name }}</td>
<td>{{ $row->status_name }}</td>
<td>{{ $row->status_publish }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- <div class="row">
                <div class="col-xs-12">
                    <button class="btn btn-danger" id="delete">
                        {{ trans('quickadmin::templates.templates-view_index-delete_checked') }}
                    </button>
                </div>
            </div>
            {!! Form::open(['route' => config('quickadmin.route').'.publish.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!} -->
        </div>
	</div>
@else
    {{ trans('quickadmin::templates.templates-view_index-no_entries_found') }}
@endif

@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            $('#delete').click(function () {
                if (window.confirm('{{ trans('quickadmin::templates.templates-view_index-are_you_sure') }}')) {
                    var send = $('#send');
                    var mass = $('.mass').is(":checked");
                    if (mass == true) {
                        send.val('mass');
                    } else {
                        var toDelete = [];
                        $('.single').each(function () {
                            if ($(this).is(":checked")) {
                                toDelete.push($(this).data('id'));
                            }
                        });
                        send.val(JSON.stringify(toDelete));
                    }
                    $('#massDelete').submit();
                }
            });
        });
    </script>
    <style>
        .btn-read {
            opacity: .5;
            color: #26a69a;
            cursor: pointer;;
        }
        .btn-read:hover {
            opacity: 1;
        }
        .test-1 {
            color: #26a69a;
        }
    </style>
@stop