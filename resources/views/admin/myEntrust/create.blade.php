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
    {!! Form::label('customer_id', '客戶', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('customer_id', $customer, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '委刊單名稱', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), array('class'=>'form-control')) !!}
        
    </div>
</div>
<!-- <div class="form-group">
    {!! Form::label('publish_kind', '委刊類別', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        <select class="form-control" id="publish_kind" name="publish_kind">
            <option value="0">請選擇</option>
            <option value="1">必PO全網圖片廣告</option>
            <option value="2">快點TV全網圖片廣告</option>
            <option value="3">必PO定版圖片廣告</option>
            <option value="4">快點TV定版圖片廣告</option>
            <option value="5">必Po影音廣告</option>
            <option value="6">快點TV影音廣告</option>
        </select>
        
    </div>
</div> -->
<!-- <div class="form-group">
    {!! Form::label('publish_item', '委刊項', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        <select class="form-control" id="publish_item" name="publish_item">
            <option value="0">請選擇</option>
            <option value="1">必PO TV廣告委刊業務</option>
            <option value="2">快點TV廣告委刊業務</option>
            <option value="3">內容製作業務</option>
            <option value="4">其他業務</option>
        </select>
        <input class="form-control" name="publish_item_cost" type="text" id="publish_item_cost" />
    </div>
</div> -->

<!-- <div class="form-group">
    {!! Form::label('owner_user', '委刊者*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('owner_user', old('owner_user'), array('class'=>'form-control')) !!}
        
    </div>
</div> -->

{{ Form::hidden('owner_user', Auth::user()->id, array('id' => 'invisible_id')) }}

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit( trans('quickadmin::templates.templates-view_create-create') , array('class' => 'btn btn-primary')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection