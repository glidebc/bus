@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>新增聯絡人</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::open(array('route' => config('quickadmin.route').'.contact.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}

<div class="form-group">
    {!! Form::label('customer_id', '代理商｜客戶', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::select('customer_id', $agent_and_customer, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '姓名', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), array('class'=>'form-control', 'maxlength' => 20, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('zip_code', '郵遞區號', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('zip_code', old('zip_code'), array('class'=>'form-control', 'placeholder' => '3碼', 'maxlength' => 3, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('address', '地址', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('address', old('address'), array('class'=>'form-control', 'maxlength' => 200, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('tel', '電話', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('tel', old('tel'), array('class'=>'form-control', 'maxlength' => 20, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('fax', '傳真', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('fax', old('fax'), array('class'=>'form-control', 'maxlength' => 20, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('mobile', '手機', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('mobile', old('mobile'), array('class'=>'form-control', 'maxlength' => 22, 'autocomplete' => 'off')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('email', 'Email', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('email', old('email'), array('class'=>'form-control', 'maxlength' => 50, 'autocomplete' => 'off')) !!}
        
    </div>
</div>

{{ Form::hidden('owner_user', $userId, array('id' => 'invisible_id')) }}

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit( trans('quickadmin::templates.templates-view_create-create') , array('class' => 'btn btn-primary')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection