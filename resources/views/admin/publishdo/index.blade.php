@extends('admin.layouts.master')

@section('content')

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

                        <th>審核</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entrusts as $row)
                        <tr>
                            <!-- <td>
                                {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                            </td> -->
                            <td>
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrust.verify', $row->id))) !!}
                                {!! Form::submit('查看', array('class' => 'hide', 'id' => 'verify-'.$row->id)) !!}
                                <span class="fa fa-file-o btn-{{ $row->verify ? 'verify' : 'read' }}" onclick='$("#verify-{{ $row->id }}").click();'></span>
                                {!! Form::close() !!}
                            </td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->customer_name }}</td>
                            <td>{{ $row->user_dept }}</td>
                            <td>{{ $row->user_name }}</td>
<!-- <td>{{ $row->status_name }}</td> -->

                            <td>
                            @if($row->status == 2)
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrustverify.yes', $row->id))) !!}
                                {!! Form::submit('核可', array('class' => 'btn btn-xs btn-info')) !!}
                                {!! Form::close() !!}
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要退件？');", 'route' => array(config('quickadmin.route').'.entrustverify.reject', $row->id))) !!}
                                {!! Form::submit('退件', array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!}
                            @endif
                                <!-- {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.publish.destroy', $row->id))) !!}
                                {!! Form::submit(trans('quickadmin::templates.templates-view_index-delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!} -->
                            </td>
                            <!-- <td>
                                @if($row->status == 3)
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要回復成審核中？');", 'route' => array(config('quickadmin.route').'.entrustverify.back', $row->id))) !!}
                                {!! Form::submit('回復審核中', array('class' => 'btn btn-xs btn-warning')) !!}
                                {!! Form::close() !!}
                                @endif
                            </td> -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
	</div>

@endsection