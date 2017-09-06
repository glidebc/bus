@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>委刊單資料</h1>
    </div>
</div>

<form method="GET" accept-charset="UTF-8" class="form-horizontal">

<div class="form-group">
    {!! Form::label('entrust_number', '編號', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('entrust_number', $entrust->enum, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('customer_name', '客戶', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('customer_name', $entrust->customer_name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '委刊單名稱', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', $entrust->name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('duration', '總走期', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('txt_start_date', $entrust->txt_start_date, array('class'=>'form-control duration', 'readonly'=>'true')) !!}
        <span class="fa fa-arrow-right"></span>
        {!! Form::text('txt_end_date', $entrust->txt_end_date, array('class'=>'form-control duration', 'readonly'=>'true')) !!}&nbsp;
        共
        {!! Form::text('day_count', $entrust->day_count, array('class'=>'form-control item-count', 'readonly'=>'true')) !!}
        天
        
    </div>
</div><div class="form-group">
    {!! Form::label('publish_kind', '委刊類別', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        @foreach ($entrust->publish_kind as $publish_kind)
            {{  $publish_kind }}<br>
        @endforeach
        
    </div>
</div><div class="form-group">
    {!! Form::label('item', '委刊項', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('item_count', $entrust->item_count, array('class'=>'form-control item-count', 'readonly'=>'true')) !!}個 , 小計 ${{ $entrust->count }} 元整
        <div id="item-list">
            @foreach ($entrust->item as $item)
            <div>
                {!! Form::text(null, $item, array('class'=>'form-control item-name', 'readonly'=>'true')) !!}
                {!! Form::text(null, array_shift($entrust->itemCost), array('class'=>'form-control item-currency text-right', 'readonly'=>'true')) !!}
            </div>
            @endforeach
        </div>
    </div>
</div><div class="form-group">
    {!! Form::label('pay', '付款方式', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('pay', $entrust->pay, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('pay_status', '付款情況', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('pay_status', $entrust->pay_status, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '補充說明', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {{ Form::textarea('note', $entrust->note, array('class'=>'form-control', 'rows' => '5', 'readonly'=>'true')) }}
        
    </div>
</div>
</form>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
    @if($kind == 'verify')
        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrustverify.yes', $entrust->id))) !!}
        {!! Form::submit('核可', array('class' => 'btn btn-info')) !!}
        {!! Form::close() !!}&nbsp;
        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return confirm('確定要退件？');", 'route' => array(config('quickadmin.route').'.entrustverify.reject', $entrust->id))) !!}
        {!! Form::submit('退件', array('class' => 'btn btn-danger')) !!}
        {!! Form::close() !!}&nbsp;&nbsp;&nbsp;&nbsp;
    @endif
        <a href="javascript:history.back();" class="btn btn-default">返回</a>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">

</script>
<style>
    .form-group input[type="text"], .form-group textarea:-moz-read-only { /* For Firefox */
        display: inline-block;
        background-color: white;
    }
    .form-group input[type="text"], .form-group textarea:read-only { 
        display: inline-block;
        background-color: white;
    }
    .duration {
        width: 16%;
    }
    .item-count {
        width: 3%;
        padding: 0;
        text-align: center;
        border-width: 0px;
    }
    .item-name {
        width: 36%;
    }
    .item-currency {
        width: 15%;
    }
    .txt {
        width: auto;
    }
</style>
@stop