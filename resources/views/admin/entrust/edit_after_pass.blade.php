@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>編輯委刊單 發票項目</h1>
    </div>
</div>

<form accept-charset="UTF-8" class="form-horizontal">
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
    {!! Form::label('name', '委刊單名稱', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', $entrust->name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div>
</form>

{!! Form::model($entrust, array('class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array(config('quickadmin.route').'.entrust.updateAfterPass', $entrust->id))) !!}

<div class="form-group">
    {!! Form::label('invoice_date', '發票日期', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('txt_invoice_date', old('txt_invoice_date'), array('class'=>'form-control', 'id'=>'txtInvoiceDate', 'maxlength' => 10, 'autocomplete' => 'off')) !!}
        {{ Form::hidden('invoice_date', null, array('id' => 'hidInvoiceDate')) }}
        
    </div>
</div><div class="form-group">
    {!! Form::label('invoice_num', '發票號碼', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('invoice_num', old('invoice_num',$entrust->invoice_num), array('class'=>'form-control', 'placeholder' => '10碼', 'maxlength' => 10, 'autocomplete' => 'off')) !!}
        
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        {!! Form::submit(trans('quickadmin::templates.templates-view_edit-update'), array('class' => 'btn btn-primary')) !!}
        {!! link_to_route(config('quickadmin.route').'.myentrust.index', trans('quickadmin::templates.templates-view_edit-cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('javascript')
<script type="text/javascript">
    $('#txtInvoiceDate').datepicker({
        changeMonth: true,
        numberOfMonths: 3,
        // beforeShow: durationDatePick,
        dateFormat: "yy-mm-dd",
        onSelect: function(dateText, inst) {
            var dateValue = dateText.replace(/-/g, "");
            var hidID = inst.id.replace('txt', 'hid');
            $('#' + hidID).val(dateValue);
        }
    }).change(function() {
        var dateValue = $(this).val().replace(/-/g, "");
        $('#hidInvoiceDate').val(dateValue);
    });
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