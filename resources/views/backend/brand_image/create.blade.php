@extends('backend._layouts.default')

@section('main')

<div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
        @include('backend._partials.brand-tabs', array('brandImages' => true))
    </div>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}">Dashboard</a></li>
            <li><a href="{!! URL::route('brand.index') !!}">Brand</a></li>
            <li><a href="{!! URL::route('brand.edit', $brand->id) !!}">edit</a></li>
            <li><a href="{!! URL::route('brand.edit', $brand->id) !!}">{!! $brand->title !!}</a></li>
            <li><a href="{!! URL::route('brand.images.index', $brand->id) !!}">images</a></li>
          <li class="active">upload image</li>            
        </ol>

        <a href="{!! URL::route('brand.images.index', $brand->id) !!}" class="btn btn-danger pull-right">back to images<i class="entypo-plus"></i></a>

        <h2>Brand <small>images create</small></h2>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <div class="panel-body">

                    {!! Notification::showAll() !!}
    			    {!! Form::open(array('route' => array('brand.images.store', $brand->id), 'files' => true, 'class' => 'form-horizontal', 'data-toggle' => 'validator')) !!}
    				    <input type="hidden" name="_token" value="{!! Session::token() !!}">

    			        <div class="form-group">
    			            {!! Form::label('file', 'File', array('class' => 'col-sm-3 control-label')) !!}

    			            <div class="col-sm-5">
    			                {!! Form::file('file', null, array('class' => 'form-control', 'data-message-required' => 'This field is required.')) !!}
    			            </div>
    			        </div>

                        <div class="form-group">
                            {!! Form::label('tag', 'Tag', array('class' => 'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::select('tag', array('square' => 'square', 'widescreen' => 'widescreen'), null,  array('class' => 'form-control', 'required' => 'required', 'data-message-required' => 'This is custom message for required field.')) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('rank', 'Rank', array('class' => 'col-sm-3 control-label')) !!}
                            <div class="col-sm-5">
                                {!! Form::text('rank', null, array('class' => 'form-control', 'data-validate' => 'required,number', 'data-message-required' => 'This is custom message for required field.')) !!}
                            </div>
                        </div>

    			        <div class="form-group">
    			            <div class="col-sm-offset-3 col-sm-5">
    			                {!! Form::submit('Save', array('class' => 'btn btn-default')) !!}
    			                <a href="{!! URL::route('brand.images.store', $brand->id) !!}" class="btn btn-large">Cancel</a>
    			            </div>
    			        </div>

    			    {!! Form::close() !!}
                </div>
            </div>
        </div>  
    </div>
</div>    

@stop