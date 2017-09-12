@extends('admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>修改客戶資料</h1>

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::model($customer, array('class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array(config('quickadmin.route').'.mycustomer.update', $customer->id))) !!}

<div class="form-group">
    {!! Form::label('name', '公司簡稱', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('name', old('name',$customer->name), array('class'=>'form-control', 'placeholder' => '10字以內', 'maxlength' => 10)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('tax_title', '公司全名', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('tax_title', old('tax_title',$customer->tax_title), array('class'=>'form-control', 'placeholder' => '發票抬頭，公司完整名稱')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('agent_id', '代理商', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        <!-- {!! Form::text('agent_name', old('agent',$agent), array('class'=>'form-control', 'readonly'=>'true')) !!} -->
        {!! Form::select('agent_id', old('agent',$agent), $agentid, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('tax_num', '統編', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('tax_num', old('tax_num',$customer->tax_num), array('class'=>'form-control', 'maxlength' => 8)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('zip_code', '郵遞區號', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('zip_code', old('zip_code',$customer->zip_code), array('class'=>'form-control', 'placeholder' => '3碼', 'maxlength' => 3)) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('address', '公司地址', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::text('address', old('address',$customer->address), array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('contact_id', '聯絡窗口', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10">
        {!! Form::select('contact_id', $contact, $customer->contact_id, array('class'=>'form-control')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('note', '備註', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {{ Form::textarea('note', old('note',$customer->note), array('class'=>'form-control', 'rows' => '5')) }}
        
    </div>
</div>

<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
      {!! Form::submit(trans('quickadmin::templates.templates-view_edit-update'), array('class' => 'btn btn-primary')) !!}
      {!! link_to_route(config('quickadmin.route').'.mycustomer.index', trans('quickadmin::templates.templates-view_edit-cancel'), null, array('class' => 'btn btn-default')) !!}
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