@extends('admin.layouts.master')

@section('content')

<p>{!! link_to_route(config('quickadmin.route').'.myagent.create', '新增代理商' , null, array('class' => 'btn btn-success')) !!}</p>

@if ($agent->count())
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">代理商列表</div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-responsive datatable" id="datatable">
                <thead>
                    <tr>
                        <!-- <th>
                            {!! Form::checkbox('delete_all',1,false,['class' => 'mass']) !!}
                        </th> -->
                        <th>&nbsp;</th>
                        <th>公司簡稱</th>
<!-- <th>抬頭</th> -->
<th>統編</th>
<th>郵遞區號</th>
<!-- <th>公司地址</th> -->
<!-- <th>聯絡窗口</th> -->
<th>公司電話</th>
<!-- <th>公司傳真</th> -->
<!-- <th>手機</th> -->
<!-- <th>備註</th> -->

                        <th>狀態</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($agent as $row)
                        <tr>
                            <!-- <td>
                                {!! Form::checkbox('del-'.$row->id,1,false,['class' => 'single','data-id'=> $row->id]) !!}
                            </td> -->
                            <th>&nbsp;</th>
                            <td>{{ $row->name }}</td>
<!-- <td>{{ $row->tax_title }}</td> -->
<td>{{ $row->tax_num }}</td>
<td>{{ $row->zip_code }}</td>
<!-- <td>{{ $row->address }}</td> -->
<!-- <td>{{ $row->contact }}</td> -->
<td>{{ $row->com_tel }}</td>
<!-- <td>{{ $row->com_fax }}</td> -->
<!-- <td>{{ $row->mobile }}</td> -->
<!-- <td>{{ $row->note }}</td> -->

                            <td>
                                @if($row->deleted_at != '')
                                    停用
                                @endif
                            </td>
                            <td>
                                {!! link_to_route(config('quickadmin.route').'.myagent.edit', trans('quickadmin::templates.templates-view_index-edit'), array($row->id), array('class' => 'btn btn-xs btn-info')) !!}
                                
                                @if($row->deleted_at == '')
                                    {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'DELETE', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.myagent.destroy', $row->id))) !!}
                                    {!! Form::submit('停用', array('class' => 'btn btn-xs btn-warning')) !!}
                                @else
                                    {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('".trans("quickadmin::templates.templates-view_index-are_you_sure")."');",  'route' => array(config('quickadmin.route').'.myagent.resetDelete', $row->id))) !!}
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
            {!! Form::open(['route' => config('quickadmin.route').'.myagent.massDelete', 'method' => 'post', 'id' => 'massDelete']) !!}
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