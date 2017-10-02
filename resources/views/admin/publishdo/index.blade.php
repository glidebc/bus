@extends('admin.layouts.master')

@section('content')

@if ($entrusts->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">委刊單執行列表</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>委刊單</th>
                        <th>客戶</th>
                        <th>部門</th>
                        <th>使用者</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entrusts as $row)
                        <tr>
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
                            <td>{{ $row->user_dept }}</td>
                            <td>{{ $row->user_name }}</td>
                            <td>
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要結案？');", 'route' => array(config('quickadmin.route').'.publish.close', $row->id))) !!}
                                {!! Form::submit('結案', array('class' => 'btn btn-xs btn-warning')) !!}
                                {!! Form::close() !!}
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return rejectPrompt();", 'route' => array(config('quickadmin.route').'.publish.reject', $row->id))) !!}
                                {!! Form::submit('退件', array('class' => 'btn btn-xs btn-danger')) !!}
                                {{ Form::hidden('reject_text', null, array('id' => 'reject_text')) }}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
	</div>
@else
    {{ trans('quickadmin::templates.templates-view_index-no_entries_found') }}
@endif

@endsection

@section('javascript')
<script>
    function rejectPrompt() {
        var rejectText = prompt("請輸入退件原因");
        if (rejectText == null || rejectText == "") {
            return false;
        } else {
            $('#reject_text').val(rejectText);
            return true;
        }
    }
</script>
<style>
    .btn-read {
        opacity: .5;
        color: #26a69a;
        cursor: pointer;
    }
    .btn-read:hover {
        opacity: 1;
    }
</style>
@stop