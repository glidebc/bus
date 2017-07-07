@extends('admin.layouts.master')

@section('content')
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption">預約現況表</div>
        </div>
        <div class="portlet-body">
        	<div class="row width-98">
        		<!-- <div class="col-md-12"> -->
    			<div class="form-horizontal">

    				<!-- <div class="form-group  width-98">
    					<label class="col-sm-2 control-label">預約者顏色</label>
    					<div class="col-sm-10">
							<input type="text" class="form-control color-box" readonly="true" value="使用者1" style="background-color: Red;">
							<input type="text" class="form-control color-box" readonly="true" value="大名" style="background-color: blue;">
						</div>
					</div> -->
		        	<div class="form-group">
					    {!! Form::label('entrust_id', '委刊單', array('class'=>'col-sm-2 control-label')) !!}
					    <div class="col-sm-10">
					        {!! Form::select('entrust_id', $entrust, $myEntrustId, array('class'=>'form-control width-auto')) !!}

					    </div>
					</div>
		        	<div class="form-group">
					    <label class="col-sm-2 control-label">顯示月份區間</label>
					    <div class="col-sm-10">
					        <select class="form-control width-auto" id="drpStartMonth">
					        	<option value="0">起始年月</option>
					        </select>
					        &nbsp;<span class="fa fa-arrow-right"></span>&nbsp;
					        <select class="form-control width-auto" id="drpEndMonth">
					        	<option value="0">結束年月</option>
					        </select>
					    </div>
					</div>

				</div>
				<!-- </div> -->
			</div>

			<div class="tab-group">

				<ul class="nav nav-tabs droptabs">
					@for($i = 0; $i < count($arraySite); $i++)
			        <li<?php
						if($i == 0) echo ' class="active always-visible"';
					?>><a href="#table-booking-{{ $arraySite[$i]->id }}" idx="{{ $i }}" data-toggle="tab">{{ $arraySite[$i]->name }}</a></li>
			        @endfor
			        <li class="dropdown pull-right">
			            <a href="#" id="myTabDrop1" class="dropdown-toggle" data-toggle="dropdown">其他位置 <b class="caret"></b></a>
			            <ul class="dropdown-menu" role="menu" aria-labelledby="myTabDrop1">
			                
			            </ul>
			        </li>
			    </ul>
			    <div class="row-fluid ">
			        <div class="row-fluid">
			            <div class="tab-content span4" id="table-tab-content">
			            	@for($i = 0; $i < count($arrayPositions); $i++)
			                <div class="tab-pane{{ $i == 0 ? ' active' : '' }}" id="table-booking-{{ $arrayPositions[$i]->site_id }}">
			                	<table siteid="{{ $arrayPositions[$i]->site_id }}">
							        <thead>
							            <tr>
							                <th><!-- foreach($arrayPositions[$i]->positions as $key => $value) --></th>
							                @foreach($arrayPositions[$i]->positions as $position)
						                	<th item='{{ $position->id }}' colspan='{{ $position->turns_count }}'>{{ $position->name }}</th>
						                	@endforeach
							            </tr>
							        </thead>
							        <tbody>
							        </tbody>
							    </table>
			                </div>
			                @endfor
			            </div>
			        </div>
			    </div>
		    </div>
		    <div class="row">
			    <div class="col-xs-12">
			    	<input type="button" id="btn-book" class="btn btn-primary" value="送出" onclick="bookSubmit();" />
			    </div>
			</div>
        </div>
	</div>
	<input id='hidVal' type="hidden" value="" />
	<div id='hidMsg' style="display: none;"></div>

@endsection

@section('javascript')
	<script src="{{asset('js/jquery.blockUI.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/jquery.droptabs.js')}}" type="text/javascript"></script>
	<script src="{{asset('js/publishBook_index.js')}}" type="text/javascript"></script>

    <style>
    table, th, td {
	   border: 1px solid #C5C5C5;
	}
	table, th {
	   color: #afafaf;
	}
	table {
	    border-collapse: separate;
	    border-spacing: 0;
	    border-radius: 8px;
	    -moz-border-radius: 8px;
	    width: 100%;
	    overflow: scroll;
	}
	th, td {
	    padding: 4px;
	}
	td {
		width: 88px;
	}
	.tab-group {
		width: 98%;
		margin: 0 auto 15px auto;
	}
	.width-98 {
        width: 98%;
        margin: 0 auto;
    }
	.width-12 {
        display: inline-block;
        width: 12%;
    }
    .width-auto {
        display: inline-block;
        width: auto;
    }
	/*.select-ym {
		font-size: 14px;
	    font-weight: normal;
	    color: #333;
	    background-color: #fff;
	    border: 1px solid #e5e5e5;
	    box-shadow: none;
	    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;

		height: 34px;
		padding: 6px 12px;
		line-height: 1.42857143;
		background-image: none;
	}*/
	@media (min-width: 768px) {
		/*.hid-data {
			height: 20px;
		}*/
		.tab-group {
			margin: 0 auto 15px auto;
		}
	}
	.project-pending {/*委刊單審核中*/
	    filter: alpha(opacity=50);/*ie8 ok*/
	    opacity: 0.5;/*firefox ok*/
	    color: black;
	}
    .project-ok {/* 預約通過審核 */
    	border: 2px solid black;
        /*background: #89c4f4;*/
    }
    .selected {
    	background: lightgray;
        /*border-color: black;*/
    }

    .selected-date,
    .selected-item {
        color: black;
    }
    /* Tooltip */
	.tooltip > .tooltip-inner {
		background-color: #26a69a; 
		color: #FFFFFF; 
		border: 1px solid green; 
		padding: 12px;
		font-size: 12pt;
		text-align: left;
		white-space: pre-wrap;
		max-width: 800px;
	    /* If max-width does not work, try using width instead */
	    /*width: 800px; */
	}
	/* Tooltip on top */
	.tooltip.top > .tooltip-arrow {
		border-top: 5px solid green;
	}

	.color-box {
        display: inline-block;
        width: 8%;
        padding: 0;
        border-width: 0px;
        text-align: center;
        color: white;
        border-radius: 4px;
    }
    </style>
@stop
