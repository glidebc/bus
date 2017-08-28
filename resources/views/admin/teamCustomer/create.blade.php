@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>新增客戶</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::open(array('route' => config('quickadmin.route').'.teamcustomer.store', 'id' => 'form-with-validation', 'class' => 'form-horizontal')) !!}

<div class="form-group">
    {!! Form::label('name', '公司簡稱', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name'), array('class'=>'form-control', 'placeholder' => '10字以內', 'maxlength' => 10)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('tax_title', '公司全名', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('tax_title', old('tax_title'), array('class'=>'form-control', 'placeholder' => '發票抬頭，公司完整名稱')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('agent_id', '代理商', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::select('agent_id', $agent, null, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('tax_num', '統編', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('tax_num', old('tax_num'), array('class'=>'form-control', 'maxlength' => 8)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('zip_code', '郵遞區號', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('zip_code', old('zip_code'), array('class'=>'form-control', 'placeholder' => '3碼', 'maxlength' => 3)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('address', '公司地址', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('address', old('address'), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('contact', '聯絡窗口', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('contact', old('contact'), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('com_tel', '公司電話', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('com_tel', old('com_tel'), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('com_fax', '公司傳真', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('com_fax', old('com_fax'), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('mobile', '手機', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('mobile', old('mobile'), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '備註', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {{ Form::textarea('note', old('note'), array('class'=>'form-control', 'rows' => '5')) }}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '共用', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
    @foreach($arrayUser as $id => $name)
        <label>
            {!! Form::checkbox('array_user[]', $id, false) !!}
            {{ $name }}
        </label>　　
    @endforeach
        
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