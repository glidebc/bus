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
    {!! Form::label('entrust_number', '編號', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('enum', $enum, array('class'=>'form-control entrust-number', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('customer_id', '代理商｜客戶', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('customer_id', $customer, old('customer_id'), array('class'=>'form-control', 'id' => 'drpCustomer')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('contact_id', '承辦窗口', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('drpContact', [], null, array('class'=>'form-control', 'id' => 'drpContact')) !!}
        {{ Form::hidden('contact_id', old('contact_id',0), array('id' => 'hidContact')) }}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '其他說明', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <!-- {{ Form::textarea('note', old('note'), array('class'=>'form-control', 'rows' => '5')) }} -->
        {!! Form::text('note', old('note'), array('class'=>'form-control', 'placeholder' => '40字以內', 'maxlength' => 40, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '委刊單名稱', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), array('class'=>'form-control', 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('duration', '總走期', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('txt_start_date', old('txt_start_date'), array('class'=>'form-control duration', 'id'=>'txtStartDate', 'maxlength' => 10, 'autocomplete' => 'off')) !!}
        {{ Form::hidden('start_date', null, array('id' => 'hidStartDate')) }}
        <span class="fa fa-arrow-right"></span>
        {!! Form::text('txt_end_date', old('txt_end_date'), array('class'=>'form-control duration', 'id'=>'txtEndDate', 'maxlength' => 10, 'autocomplete' => 'off')) !!}
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
        <input class="btn btn-item" type="button" value="編輯委刊項" onclick="showOrHideItemList(this);">&nbsp;
        @if($userId==1 || $userId==7 || $userId==10)
        <input class="btn btn-item" type="button" value="編輯特殊委刊項" onclick="showOrHideItemListSpecial(this);">
        @endif
        <div id="item-list" style="display: none;">
            @for ($no=1; $no <= 5 ; $no++)
            <div>
                {!! Form::text('item_name_'.$no, old('item_name_'.$no), array('class'=>'form-control item-name', 'id'=>'item_name_'.$no, 'placeholder'=>'項次'.$no.'：委刊專案內容', 'autocomplete' => 'off')) !!}
                {!! Form::text('item_currency_'.$no, old('item_currency_'.$no), array('class'=>'form-control item-currency text-right', 'placeholder'=>'項次'.$no.'：預算金額', 'no'=>$no, 'autocomplete' => 'off')) !!}
                {{ Form::hidden('item_cost_'.$no, old('item_cost_'.$no), array('id' => 'item_cost_'.$no)) }}
            </div>
            @endfor
        </div>
        <div id="item-list-special" style="display: none;">
            <div>
                {!! Form::text('item_name_6', old('item_name_6', '其他專案執行，CPM$計價'), array('class'=>'form-control item-name', 'readonly'=>'true')) !!}
                {!! Form::text('item_currency_6', old('item_currency_6'), array('class'=>'form-control item-currency text-right', 'placeholder'=>'CPM $', 'no'=>6, 'autocomplete' => 'off')) !!}
            </div>
        </div>
    </div>
</div><div class="form-group">
    {!! Form::label('pay', '付款方式', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('pay', $pay, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('pay_status', '付款條件', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::select('pay_status', $payStatus, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('invoice_date', '發票日期', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('txt_invoice_date', old('txt_invoice_date'), array('class'=>'form-control', 'id'=>'txtInvoiceDate', 'maxlength' => 10, 'autocomplete' => 'off')) !!}
        {{ Form::hidden('invoice_date', old('invoice_date'), array('id' => 'hidInvoiceDate')) }}
        
    </div>
</div><div class="form-group">
    {!! Form::label('invoice_num', '發票號碼', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('invoice_num', old('invoice_num'), array('class'=>'form-control', 'placeholder' => '10碼', 'maxlength' => 10, 'autocomplete' => 'off')) !!}
        
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

<div id='hidMsg' style="display: none;"></div>

@endsection

@section('javascript')
<script type="text/javascript">
    var $drpCustomer = $('#drpCustomer');
    var $drpContact = $('#drpContact');
    var $hidContact = $('#hidContact');
    $drpContact.append('<option value="0">先選擇代理商｜客戶</option>');

    if($drpCustomer.val() > 0) {
        setContact($drpCustomer.val());
    }
    $drpCustomer.change(function(){
        setContact($(this).val());
    });
    $drpContact.change(function(){
        $hidContact.val($(this).val());
    });

    function setContact(custId){
        $drpContact.empty();

        $.ajax({
            type: 'POST',
            url: "../api/contact",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {
                id: custId
            },
            dataType: "json",
            complete: function(jqXHR, textStatus) {
                switch (jqXHR.status) {
                    case 200:
                        var jsonContact = JSON.parse(jqXHR.responseText); //將資料字串存成資料json
                        $drpContact.append('<option value="0">請選擇</option>');
                        var contactSelected = '';
                        for(var k in jsonContact) {
                            if(k == $hidContact.val())
                                contactSelected = ' selected';
                            $drpContact.append('<option value="'+k+'"'+contactSelected+'>'+jsonContact[k]+'</option>');
                        }
                        //
                        if(contactSelected == '')
                            $hidContact.val('0');
                        break;
                    default:
                        alert('聯絡人載入失敗，請再選擇代理商｜客戶');
                        $('#hidMsg').text(jqXHR.responseText);
                        break;
                }

            }
        });
    }

    $('#txtStartDate, #txtEndDate, #txtInvoiceDate').datepicker({
        changeMonth: true,
        numberOfMonths: 3,
        beforeShow: durationDatePick,
        dateFormat: "yy-mm-dd",
        onSelect: function(dateText, inst) {
            var dateValue = dateText.replace(/-/g, "");
            var hidID = inst.id.replace('txt', 'hid');
            $('#' + hidID).val(dateValue);
            //
            countDays();
        }
    });
    $('#txtStartDate').change(function() {
        var dateValue = $(this).val().replace(/-/g, "");
        $('#hidStartDate').val(dateValue);
    });
    $('#txtEndDate').change(function() {
        var dateValue = $(this).val().replace(/-/g, "");
        $('#hidEndDate').val(dateValue);
    });
    $('#txtInvoiceDate').change(function() {
        var dateValue = $(this).val().replace(/-/g, "");
        $('#hidInvoiceDate').val(dateValue);
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
        if(!isNaN(sd.getTime()))
            $('#day-count').val(1);
        else
            $('#day-count').val(0);

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

    function showOrHideItemListSpecial(e){
        var t = '';
        e.value.indexOf('編輯') >= 0 ? t = '隱藏' : t = '編輯';
        e.value = t + '特殊委刊項';
        $('#item-list-special').slideToggle();
    }

    var itemCount=0, count=0;//委刊項數量, 小計金額
    var strItemCountInfo = '目前有 item 個委刊項 , 小計 $count 元整';
    setItemInfo();

    $('#item_name_1, #item_name_2, #item_name_3, #item_name_4, #item_name_5, input[name=item_currency_6]').change(function() {setItemInfo();});

    function setItemInfo() {
        itemCount = 0;
        //計算委刊項數量
        for(var itemNo = 1; itemNo <= 5; itemNo++)
            if($('#item_name_' + itemNo).val().length > 0)
                    itemCount++;

        if($('input[name=item_currency_6]').val().length > 0)
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
        //計算預算總和
        for(var itemNo = 1; itemNo <= 5; itemNo++){
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
    .form-group input[type="text"]:-moz-read-only { /* For Firefox */
        background-color: white;
    }
    .form-group input[type="text"]:read-only { 
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
    .item-cost-text {
        display: inline-block;
        width: 27%;
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