@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>{{ trans('quickadmin::templates.templates-view_edit-edit') }}</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::model($entrust, array('class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array(config('quickadmin.route').'.entrust.update', $entrust->id))) !!}

<div class="form-group">
    {!! Form::label('customer_id', '客戶*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::select('customer_id', $customer, $customerid, array('class'=>'form-control')) !!}
        
        
    </div>
</div><div class="form-group">
    {!! Form::label('name', '委刊專案名稱*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$entrust->name), array('class'=>'form-control')) !!}
        
    </div>
</div>
<!-- <div class="form-group">
    {!! Form::label('owner_user', '委刊者*', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('owner_user', old('owner_user',$entrust->owner_user), array('class'=>'form-control')) !!}
        
    </div>
</div> -->

{{ Form::hidden('owner_user', Auth::user()->id, array('id' => 'invisible_id')) }}

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('quickadmin::templates.templates-view_edit-update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route(config('quickadmin.route').'.entrust.index', trans('quickadmin::templates.templates-view_edit-cancel'), null, array('class' => 'btn btn-default')) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection