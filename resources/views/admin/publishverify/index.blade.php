@extends('admin.layouts.master')

@section('content')

@if ($entrusts->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">委刊單審核列表</div>
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
<th>部門</th>
<th>使用者</th>
<th>狀態</th>

                        <th>審核</th>
                        <!-- <th>狀態回復</th> -->
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entrusts as $row)
                        <tr>
                            <!-- <td>
                                {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                            </td> -->
                            <td>&nbsp;</td>
                            <td>{{ $row->name }}</td>
<td>{{ $row->customer_name }}</td>
<td>{{ $row->user_dept }}</td>
<td>{{ $row->user_name }}</td>
<td>{{ $row->status_name }}</td>

                            <td>
                                @if($row->status == 2)
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrustverify.yes', $row->id))) !!}
                                {!! Form::submit('核可', array('class' => 'btn btn-xs btn-info')) !!}
                                {!! Form::close() !!}
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要退件？');", 'route' => array(config('quickadmin.route').'.entrustverify.reject', $row->id))) !!}
                                {!! Form::submit('退件', array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!}

                                <!-- {!! link_to_route('publish.yes', '核可', array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                {!! link_to_route('publish.no', '取消', array($row->id), array('class' => 'btn btn-xs btn-danger')) !!} -->
                                @endif
                                <!-- {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.publish.destroy', $row->id))) !!}
                                {!! Form::submit(trans('quickadmin::templates.templates-view_index-delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!} -->
                            </td>
                            <!-- <td>
                                @if($row->status == 3)
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要退回審核中？');", 'route' => array('publish.init', $row->id))) !!}
                                {!! Form::submit('退回審核中', array('class' => 'btn btn-xs btn-warning')) !!}
                                {!! Form::close() !!}

                                {!! link_to_route('publish.init', '退回審核中', array($row->id), array('class' => 'btn btn-xs btn-warning')) !!}
                                @endif
                            </td> -->
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
@stop