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
    {!! Form::label('customer_name', '代理商｜客戶', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('customer_name', $entrust->customer_name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('contact_name', '承辦窗口', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('contact_name', $entrust->contact_name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '其他說明', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('note', $entrust->note, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
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
                @if(count($entrust->itemCost) > 0)
                {!! Form::text(null, array_shift($entrust->itemCost), array('class'=>'form-control item-currency text-right', 'readonly'=>'true')) !!}
                @else
                {!! Form::text(null, array_shift($entrust->itemCostText), array('class'=>'form-control item-currency text-right', 'readonly'=>'true')) !!}
                @endif
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
    {!! Form::label('pay_status', '付款條件', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('pay_status', $entrust->pay_status, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('txt_invoice_date', '發票日期', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('txt_invoice_date', $entrust->txt_invoice_date, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('invoice_num', '發票號碼', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('invoice_num', $entrust->invoice_num, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div>
@if($entrust->status == 4)
<div class="form-group">
    {!! Form::label('reject_text', '退件原因', array('class'=>'col-sm-2 control-label text-danger')) !!}
    <div class="col-sm-10">
        {!! Form::text('reject_text', $entrust->reject_text, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div>
@endif

</form>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
    @if($kind == 'verify')
        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'route' => array(config('quickadmin.route').'.entrustverify.yes', $entrust->id))) !!}
        {!! Form::submit('核可', array('class' => 'btn btn-info')) !!}
        {!! Form::close() !!}&nbsp;
        {!! Form::open(array('style' => 'display: inline-block;', 'method' => 'POST', 'onsubmit' => "return rejectPrompt();", 'route' => array(config('quickadmin.route').'.entrustverify.reject', $entrust->id))) !!}
        {!! Form::submit('退件', array('class' => 'btn btn-danger')) !!}
        {{ Form::hidden('reject_text', null, array('id' => 'reject_text')) }}
        {!! Form::close() !!}&nbsp;&nbsp;&nbsp;&nbsp;
    @endif
        <a href="javascript:history.back();" class="btn btn-default">返回</a>
    </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
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
    .item-cost-text {
        width: 27%;
    }
    .txt {
        width: auto;
    }
</style>
@stop