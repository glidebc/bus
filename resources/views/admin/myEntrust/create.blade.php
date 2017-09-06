@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>新增委刊單</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::open(array('route' => config('quickadmin.route').'.myentrust.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}

<div class="form-group">
    {!! Form::label('customer_id', '編號', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('enum', $enum, array('class'=>'form-control entrust-number', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('customer_id', '客戶', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('customer_id', $customer, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '委刊單名稱', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('duration', '總走期', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('txt_start_date', old('txt_start_date'), array('class'=>'form-control duration', 'id'=>'txtStartDate', 'maxlength' => 10)) !!}
        {{ Form::hidden('start_date', null, array('id' => 'hidStartDate')) }}
        <span class="fa fa-arrow-right"></span>
        {!! Form::text('txt_end_date', old('txt_end_date'), array('class'=>'form-control duration', 'id'=>'txtEndDate', 'maxlength' => 10)) !!}
        {{ Form::hidden('end_date', null, array('id' => 'hidEndDate')) }}&nbsp;
        共
        {!! Form::text('day_count', old('day_count',0), array('class'=>'form-control day-count', 'id'=>'day-count', 'readonly'=>'true')) !!}
        天
        
    </div>
</div><div class="form-group">
    {!! Form::label('publish_kind', '委刊類別', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('publish_kind[]', $publishKind, null, array('class'=>'form-control', 'multiple'=>true)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('item', '委刊項', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        <!-- 目前有 -->
        {!! Form::text('item_info', old('item_info',''), array('class'=>'form-control item-info', 'id'=>'item-info', 'readonly'=>'true')) !!}
        <!-- 個委刊項 , 小計 $<span id="count">{{ old('count',0) }}</span> 元整 -->&nbsp;&nbsp;
        <input class="btn btn-item" type="button" value="編輯委刊項" onclick="showOrHideItemList(this);">
        <div id="item-list" style="display: none;">
            @for ($no=1; $no <= 10 ; $no++)
            <div>
                {!! Form::text('item_name_'.$no, old('item_name_'.$no), array('class'=>'form-control item-name', 'id'=>'item_name_'.$no, 'placeholder'=>'項次'.$no.'：委刊專案內容')) !!}
                {!! Form::text('item_currency_'.$no, old('item_currency_'.$no), array('class'=>'form-control item-currency text-right', 'placeholder'=>'項次'.$no.'：預算', 'no'=>$no)) !!}
                {{ Form::hidden('item_cost_'.$no, old('item_cost_'.$no), array('id' => 'item_cost_'.$no)) }}
            </div>
            @endfor
        </div>
    </div>
</div><div class="form-group">
    {!! Form::label('pay', '付款方式', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('pay', $pay, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('pay_status', '付款情況', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::select('pay_status', $payStatus, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '補充說明', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {{ Form::textarea('note', old('note'), array('class'=>'form-control', 'rows' => '5')) }}
        <!-- {!! Form::text('note', old('note'), array('class'=>'form-control')) !!} -->
        
    </div>
</div>

{{ Form::hidden('item_count', 0, array('id' => 'item_count')) }}
{{ Form::hidden('owner_user', $userId, array('id' => 'invisible_id')) }}

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit( trans('quickadmin::templates.templates-view_create-create') , array('class' => 'btn btn-primary')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('javascript')
<script type="text/javascript">
    $('#txtStartDate, #txtEndDate').datepicker({
        changeMonth: true,
        numberOfMonths: 3,
        beforeShow: durationDatePick,
        dateFormat: "yy-m-d",
        onSelect: function(dateText, inst) {
            var d = new Date(dateText);
            var hidID = inst.id.replace('txt', 'hid');
            $('#' + hidID).val(dateString(d, 'date'));
            //
            countDays();
        }
    });
    function durationDatePick(input) {
        if (input.id == 'txtEndDate') {
            var minDate = new Date($('#txtStartDate').val());
            minDate.setDate(minDate.getDate() + 1)

            return {
                minDate: minDate
            };
        }
        return {}
    }
    //將日期轉成字串
    function dateString(date, type) {
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();
        switch (type) {
            case 'dateFull':
                return year + "-" + (month < 10 ? '0' + month : month) + "-" + (day < 10 ? '0' + day : day);
            case 'dateText':
                return year + "-" + month + "-" + day;
            case 'date':
                return year + (month < 10 ? '0' + month : month) + (day < 10 ? '0' + day : day);
        }
    }
    //計算天數
    function countDays() {
        var sd = new Date($('#txtStartDate').val());
        var ed = new Date($('#txtEndDate').val());
        if(!isNaN(sd.getTime()) && !isNaN(ed.getTime())){
            var days = Math.round((ed - sd) / (1000 * 60 * 60 * 24)) + 1;
            $('#day-count').val(days);
        }
    }

    function showOrHideItemList(e){
        var t = '';
        e.value.indexOf('編輯') >= 0 ? t = '隱藏' : t = '編輯';
        e.value = t + '委刊項';
        $('#item-list').slideToggle();
    }

    var itemCount=0, count=0;//委刊項數量, 小計金額
    var strItemCountInfo = '目前有 item 個委刊項 , 小計 $count 元整';
    setItemInfo();

    $('#item_name_1, #item_name_2, #item_name_3, #item_name_4, #item_name_5, #item_name_6, #item_name_7, #item_name_8, #item_name_9, #item_name_10').change(function() {
        //
        // var itemCount = 0, count = 0;
        setItemInfo();
    });

    function setItemInfo() {
        itemCount = 0;
        for(var itemNo = 1; itemNo <= 10; itemNo++)
            if($('#item_name_' + itemNo).val().length > 0)
                itemCount++;
        // alert(strItemCountInfo.replace(/item/i, itemCount.toString()).replace(/count/i, count.toString()));
        setCount();//小計
    }

    function formatCurrency(num){
        var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
        if(str.indexOf(".") > 0) {
            parts = str.split(".");
            str = parts[0];
        }
        str = str.split("").reverse();
        for(var j = 0, len = str.length; j < len; j++) {
            if(str[j] != ",") {
                output.push(str[j]);
                if(i%3 == 0 && j < (len - 1)) {
                    output.push(",");
                }
                i++;
            }
        }
        formatted = output.reverse().join("");
        return(formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
    };

    
    $('.form-control.item-currency.text-right').keypress(function(event) {
        if(event.which != 8 && isNaN(String.fromCharCode(event.which)))
            event.preventDefault();
    }).keyup(function(event) {
        setCurrencyAndCostText(this);
        setCount();//小計
    });
    $('.form-control.item-currency.text-right').change(function() {
        setCurrencyAndCostText(this);
        setCount();//小計
    });
    function setCurrencyAndCostText(e) {
        var cost = $(e).val().replace(/,/g, ''); //currency去掉comma
        $('#item_cost_' + $(e).attr('no')).val(cost);
        var currency = formatCurrency(cost);
        $(e).val(currency);
        // return {};
    }
    //小計
    function setCount() {
        $('#item_count').val(itemCount);
        count = 0;
        for(var itemNo = 1; itemNo <= 10; itemNo++){
            var cost = parseInt($('#item_cost_' + itemNo).val(), 10);
            if($('#item_name_' + itemNo).val().length > 0 && !isNaN(cost))
                count += cost;
        }
        $('#item-info').val(strItemCountInfo.replace(/item/i, itemCount.toString()).replace(/count/i, formatCurrency(count)));
        // $('#count').text(formatCurrency(count));
    }

</script>
<style>
    .duration {
        display: inline-block;
        width: 16%;
    }
    .day-count {
        display: inline-block;
        width: 3%;
        padding: 0;
        text-align: center;
        border-width: 0px;
    }
    .item-info {
        display: inline-block;
        width: 36%;
        padding: 0;
        /*text-align: center;*/
        border-width: 0px;
    }
    .item-info:-moz-read-only, .day-count:-moz-read-only, .entrust-number:-moz-read-only { /* For Firefox */
        background-color: white;
    }
    .item-info:read-only, .day-count:read-only, .entrust-number:read-only { 
        background-color: white;
    }
    .item-name {
        display: inline-block;
        width: 36%;
    }
    .item-currency {
        display: inline-block;
        width: 15%;
    }
    .btn-item {
        display: inline-block;
        border: 1px solid transparent;
        padding: 6px 13px;
        /*font-size: 14px;
        outline: none !important;*/
        color: #333;
        background-color: #fff;
        border-color: #ccc;
        /*background-image: none !important;
        filter: none;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        text-shadow: none;*/
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
    }
    .btn-item:hover {
        background-color: #ededed;
        border-color: #b3b3b3;
    }
</style>
@stop