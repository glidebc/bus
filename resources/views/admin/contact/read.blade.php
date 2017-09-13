@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>聯絡人資料</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::model($contact, array('class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array(config('quickadmin.route').'.contact.update', $contact->id))) !!}

<div class="form-group">
    {!! Form::label('customer_id', '代理商｜客戶', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('customer_name', $contact->customer_name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '姓名', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$contact->name), array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('address', '地址', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('address', old('address',$contact->address), array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('tel', '電話', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('tel', old('tel',$contact->tel), array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('fax', '傳真', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('fax', old('fax',$contact->fax), array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('mobile', '手機', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('mobile', old('mobile',$contact->mobile), array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('email', 'Email', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('email', old('email',$contact->email), array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        <a href="javascript:history.back();" class="btn btn-default">返回</a>
    </div>
</div>

{!! Form::close() !!}

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
</style>
@stop