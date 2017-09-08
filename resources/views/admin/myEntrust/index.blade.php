@extends('admin.layouts.master')

@section('content')

<p>{!! link_to_route(config('quickadmin.route').'.myentrust.create', '新增委刊單' , null, array('class' => 'btn btn-success')) !!}</p>

@if ($entrust->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">委刊單列表</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                <thead>
                    <tr>
                        <!-- <th>
                            {!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}
                        </th> -->
                        <th>&nbsp;</th>
                        <th>編號</th>
                        <th>委刊單</th>
<th>公司簡稱</th>
<th>總走期</th>
<th>付款方式</th>
<th>付款情況</th>

                        <th>審核狀態</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($entrust as $row)
                        <tr>
                            <!-- <td>
                                {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                            </td> -->
                            <td>
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrust.read', $row->id))) !!}
                                {!! Form::submit('查看', array('class' => 'hide', 'id' => 'read-'.$row->id)) !!}
                                <span class="fa fa-file-o  btn-<?php
                                if($row->status==3)
                                    echo 'ok';
                                else if($row->status==4)
                                    echo 'reject';
                                else
                                    echo 'read';
                                ?>" onclick='$("#read-{{ $row->id }}").click();'></span>
                                {!! Form::close() !!}
                            </td>
                            <td>{{ $row->enum }}</td>
                            <td>{{ $row->name }}</td>
<td>{{ $row->customer_name }}</td>
<td>{{ $row->duration }}</td>
<td>{{ $row->txt_pay }}</td>
<td>{{ $row->txt_pay_status }}</td>

                            <td>
                            @if($row->status == 1)
                                提案
                            @elseif($row->status == 2)
                                審核中
                            @elseif($row->status == 3)
                                <span class="text-success">審核通過</span>
                                <!-- @if($row->verify_result)
                                    
                                @else
                                    
                                @endif -->
                            @elseif($row->status == 4)
                                <span class="text-danger">退件</span>
                                <!-- @if($row->verify_result)
                                    
                                @else
                                    
                                @endif -->
                            @elseif($row->status == 0)
                                取消委刊
                            @endif
                            </td>
                            <td>
                            @if($row->status == 1 || $row->status == 4)
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.myentrust.go', $row->id))) !!}
                                {!! Form::submit('送審', array('class' => 'btn btn-xs btn-success')) !!}
                                {!! Form::close() !!}
                                <!-- {!! link_to_route('admin.myentrust.go', '送審', array($row->id), array('class' => 'btn btn-xs btn-success')) !!} -->
                            @elseif($row->status == 2 && !$row->verifying)
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要退回提案？');", 'route' => array(config('quickadmin.route').'.myentrust.back', $row->id))) !!}
                                {!! Form::submit('取消送審', array('class' => 'btn btn-xs btn-warning')) !!}
                                {!! Form::close() !!}
                                <!-- {!! link_to_route('admin.myentrust.back', '退回提案', array($row->id), array('class' => 'btn btn-xs btn-warning')) !!} -->
                            @elseif($row->status == 3)
                                {!! link_to_route(config('quickadmin.route').'.publishbook.index', '委刊預約', array('eid='.$row->id), array('class' => 'btn btn-xs btn-default')) !!}
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.myentrust.excel', $row->id))) !!}
                                {!! Form::submit('產生Excel', array('class' => 'btn btn-xs btn-default btn-excel')) !!}
                                {!! Form::close() !!}
                            @endif
                            </td>
                            <td>
                            @if($row->status == 1 || ($row->status == 2&&!$row->verifying) || $row->status == 4)
                                {!! link_to_route(config('quickadmin.route').'.myentrust.edit', trans('quickadmin::templates.templates-view_index-edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定取消委刊單？');",  'route' => array(config('quickadmin.route').'.myentrust.cancel', $row->id))) !!}
                                <!-- {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.myentrust.destroy', $row->id))) !!} -->
                                {!! Form::submit('取消', array('class' => 'btn btn-xs btn-danger')) !!}
                                {!! Form::close() !!}
                            @endif
                            </td>
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
            </div> -->
            {!! Form::open(['route' => config('quickadmin.route').'.myentrust.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!}
        </div>
	</div>
@else
    {{ trans('quickadmin::templates.templates-view_index-no_entries_found') }}
@endif

@if (session('msg'))
<script>alert("{{ session('msg') }}");</script>
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
    .btn-ok {
        color: #26a69a;
        cursor: pointer;
    }
    .btn-reject {
        opacity: .5;
        color: #F3565D;
        cursor: pointer;
    }
    .btn-reject:hover {
        opacity: 1;
    }
    .btn-read {
        opacity: .5;
        color: #26a69a;
        cursor: pointer;
    }
    .btn-read:hover {
        opacity: 1;
    }
    .btn-excel {
        /*opacity: .6;*/
        border-color: LightGreen;
        color: green;
    }
    .btn-excel:hover {
        /*opacity: 1;*/
        border-color: ForestGreen;
        color: green;
    }
    .btn-excel:focus {
        opacity: .8;
        border-color: ForestGreen;
        color: green;
    }
    </style>
@stop