@extends('admin.layouts.master')

@section('content')
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="row">
    <div class="col-sm-10 col-sm-offset-2">
        <h1>我的資訊</h1>

        @if (session('result'))
            <div class="alert alert-success">
                我的資訊已更新
            </div>
        @endif

        @if ($errors->any())
        	<div class="alert alert-danger">
        	    <ul>
                    {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                </ul>
        	</div>
        @endif
    </div>
</div>

{!! Form::model($publishuser, array('class' => 'form-horizontal', 'id' => 'form-with-validation', 'method' => 'PATCH', 'route' => array(config('quickadmin.route').'.myuser.update', $publishuser->id))) !!}

<div class="form-group">
    {!! Form::label('user_name', '使用者', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('user_name', $user->name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('dept_name', '部門', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('dept_name', $dept_name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('team_name', '組別', array('class'=>'col-sm-2 control-label')) !!}
    <div class="col-sm-10">
        {!! Form::text('team_name', $team_name, array('class'=>'form-control', 'readonly'=>'true')) !!}
        
    </div>
</div><div class="form-group">
    {!! Form::label('color_name', '委刊預約的顏色', array('class'=>'col-sm-2 control-label text-primary')) !!}
    <div class="col-sm-10" id="color-area">
        目前的顏色 – 
        {!! Form::text('color_name', $publishuser->color_name, array('class'=>'form-control color-box', 'readonly'=>'true', 'style'=>'background-color: '.$publishuser->color_name.';color: '.$publishuser->font_color)) !!}　
    @if($publishuser->color_name == 'Gray')
        <input class="btn btn-color" type="button" value="換顏色" onclick="showOrHideColorList(this);"><br>
        <div id="color-list" style="display: none;">
            請選擇下列替換的顏色<br>
            <hr>
            @foreach ($colors as $color_name => $font_color)
            <input type="text" class="form-control color-box" readonly="true" value="{{ $color_name }}" onclick='colorSelected("{{ $color_name }}", "{{ $font_color }}");'' style="cursor:pointer; background-color: {{ $color_name }};color: {{ $font_color }};">
            @endforeach
        </div>
    @endif
    </div>
</div>
@if($publishuser->color_name == 'Gray')
<div class="form-group">
    <div class="col-sm-10 col-sm-offset-2">
        {!! Form::submit(trans('quickadmin::templates.templates-view_edit-update'), array('class' => 'btn btn-primary')) !!}
    </div>
</div>
@endif

{{ Form::hidden('user_id', $user->id, array('id' => 'invisible_id')) }}

{!! Form::close() !!}

@endsection

@section('javascript')
<script type="text/javascript">
    function showOrHideColorList(e) {
        $('#color-list').slideToggle();
        if($(e).val() == '換顏色')
            $(e).val('隱藏顏色列表');
        else
            $(e).val('換顏色');
    }

    function colorSelected(bgcolor, color){
        $('#color-area').slideToggle(function() {
            var html = '<input type="text" class="form-control color-box" name="color_name" readonly="true" value="'+bgcolor+'" style="background-color:'+bgcolor+';color:'+color+';">';
            $(this).html(html).fadeIn('fast');
        });    
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
    .color-box {
        display: inline-block;
        width: auto;
        padding: 0;
        border-width: 0px;
        text-align: center;
        color: white;
        border-radius: 4px;
    }
    .btn-color {
        display: inline-block;
        border: 1px solid transparent;
        padding: 6px 13px;
        color: #333;
        background-color: #fff;
        border-color: #ccc;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
    }
    .btn-color:hover {
        background-color: #ededed;
        border-color: #b3b3b3;
    }
</style>
@stop