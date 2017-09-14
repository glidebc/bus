@extends('admin.layouts.master')

@section('content')

<!-- <p>{!! link_to_route(config('quickadmin.route').'.teamcustomer.create', '新增客戶' , null, array('class' => 'btn btn-success')) !!}</p> -->

@if ($customer->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">團隊客戶列表</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                <thead>
                    <tr>
                        <th></th>
                        <th>公司簡稱</th>
                        <th>代理商</th>
                        <th>統編</th>
                        <th>公司電話</th>
                        <th>郵遞區號</th>
                        <th>公司地址</th>
                        <th>聯絡窗口</th>
                        <th>建立者</th>
                        <th>共用</th>
                        <th>狀態</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($customer as $row)
                        <tr>
                            <td></td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->agent_name }}</td>
                            <td>{{ $row->tax_num }}</td>
                            <td>{{ $row->com_tel }}</td>
                            <td>{{ $row->zip_code }}</td>
                            <td>{{ $row->address }}</td>
                            <td>{{ $row->contact_name }}</td>
                            <td>{{ $row->owner_user_name }}</td>
                            <td>
                            @if(!empty($row->user_names))
                                <label class="label-user-share" data-original-title='{{ $row->user_names }}' data-container="body" data-toggle="tooltip" data-placement="bottom">
                                    <span class="fa fa-child user-name"></span>
                                    @if($row->user_count > 1)
                                        x{{ $row->user_count }}
                                    @endif
                                </label>
                            @endif
                            </td>
                            <td>
                                @if($row->deleted_at != '')
                                    停用
                                @endif
                            </td>

                            <td>
                                {!! link_to_route(config('quickadmin.route').'.teamcustomer.edit', trans('quickadmin::templates.templates-view_index-edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                
                                @if($row->deleted_at == '')
                                    {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.teamcustomer.destroy', $row->id))) !!}
                                    {!! Form::submit('停用', array('class' => 'btn btn-xs btn-warning')) !!}
                                @else
                                    {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.teamcustomer.resetDelete', $row->id))) !!}
                                    {!! Form::submit('啟用', array('class' => 'btn btn-xs btn-success')) !!}
                                @endif
                                {!! Form::close() !!}
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
            {!! Form::open(['route' => config('quickadmin.route').'.teamcustomer.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
                <input type="hidden" id="send" name="toDelete">
            {!! Form::close() !!}
        </div>
	</div>
@else
    {{ trans('quickadmin::templates.templates-view_index-no_entries_found') }}
@endif

@endsection

@section('javascript')
    <script>
        $('.label-user-share').tooltip();

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
        .user-name {
            color: #26a69a;
        }
        .label-user-share {
            cursor: pointer;
        }

        /* Tooltip */
        .tooltip > .tooltip-inner {
            background-color: white; 
            color: black; 
            border: 1px solid lightgray; 
            padding: 12px;
            font-size: 12pt;
            text-align: left;
            white-space: pre-wrap;
            max-width: 800px;
            /* If max-width does not work, try using width instead */
            /*width: 800px; */
        }
        /* Tooltip on top */
        .tooltip.bottom > .tooltip-arrow {
            border-bottom: 5px solid lightgray;
        }
    </style>
@stop