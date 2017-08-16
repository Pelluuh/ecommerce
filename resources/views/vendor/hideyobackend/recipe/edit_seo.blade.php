@extends('hideyo_backend::_layouts.default')

@section('main')

<div class="row">
    <div class="col-sm-3 col-md-2 sidebar">
        @include('hideyo_backend::_partials.recipe-tabs', array('recipeEditSeo' => true))
    </div>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

        <ol class="breadcrumb">
            <li><a href="/"><i class="entypo-folder"></i>Dashboard</a></li>
            <li><a href="{!! URL::route('recipe.index') !!}">Content</a></li>
            <li><a href="{!! URL::route('recipe.edit', $recipe->id) !!}">edit</a></li>
            <li><a href="{!! URL::route('recipe.edit', $recipe->id) !!}">{!! $recipe->title !!}</a></li>
            <li class="active">seo</li>
        </ol>

        <h2>Content <small>edit seo</small></h2>
        <hr/>
        {!! Notification::showAll() !!}

        {!! Form::model($recipe, array('method' => 'put', 'route' => array('hideyo.recipe.update', $recipe->id), 'files' => true, 'class' => 'form-horizontal', 'data-toggle' => 'validator')) !!}
        <input type="hidden" name="_token" value="{!! Session::getToken() !!}">     
        {!! Form::hidden('seo', 1) !!}                      
            
        @include('hideyo_backend::_fields.seo-fields')

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-5">
                {!! Form::submit('Save', array('class' => 'btn btn-default')) !!}
                <a href="{!! URL::route('recipe.index') !!}" class="btn btn-large">Cancel</a>
            </div>
        </div>

        {!! Form::close() !!}

    </div>

</div>


@stop
