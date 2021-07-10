@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form"
              action="{{ (isset($dataTypeContent->id)) ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->id) : route('voyager.'.$dataType->slug.'.store') }}"
              method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
        @if(isset($dataTypeContent->id))
            {{ method_field("PUT") }}
        @endif
        {{ csrf_field() }}

        <!-- User Creation -->
            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                        <div class="panel-title">
                            <span class="font-weight-bold">{{__("Profile")}}</span>
                        </div>
                        {{-- <div class="panel"> --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body" style="padding-top: 0;">
                            <div class="row">
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="name">{{ __('voyager.generic.name') }}</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               placeholder="{{ __('voyager.generic.name') }}"
                                               value="{{$dataTypeContent->name ?? old('name')}}">
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="name">{{ __('voyager.generic.last_name') }}</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                               placeholder="{{ __('voyager.generic.last_name') }}"
                                               value="{{$dataTypeContent->last_name ?? old('last_name')}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="name">{{ __('Company') }}</label>
                                        <input type="text" class="form-control" id="company" name="company"
                                               placeholder="{{ __('Company') }}"
                                               value="{{$dataTypeContent->company ?? old('company')}}">
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="name">{{ __('RFC') }}</label>
                                        <input type="text" class="form-control" id="rfc" name="rfc"
                                               placeholder="{{ __('RFC') }}"
                                               value="{{$dataTypeContent->rfc ?? old('rfc')}}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="email">{{ __('voyager.generic.email') }}</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               placeholder="{{ __('voyager.generic.email') }}"
                                               value="{{$dataTypeContent->email ?? old('email')}}">
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="phone">{{ __('voyager.generic.phone') }}</label>
                                        <input class="form-control" id="phone" name="phone" placeholder="XXXXXXXXXX"
                                               value="{{$dataTypeContent->phone ?? old('phone')}}"
                                               type="text">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="username">{{__("Username")}}</label>
                                        @if(isset($dataTypeContent->password))
                                            <br>
                                            <small>&nbsp;</small>
                                        @endif
                                        <input type="username" class="form-control" id="username" name="username"
                                               placeholder="{{__("Username")}}"
                                               value="{{$dataTypeContent->username ?? old('username')}}">
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="password">{{ __('voyager.generic.password') }}</label>
                                        @if(isset($dataTypeContent->password))
                                            <br>
                                            <small>{{ __('voyager.profile.password_hint') }}</small>
                                        @endif
                                        <input type="password" class="form-control" id="password" name="password" value=""
                                               autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            @php
                                $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                if(is_string($row->details)){
                                    $options = json_decode($row->details);
                                } else {
                                    $options = $row->details;
                                }
                            @endphp

                            <div class="row">
                                <div class="col-md-4" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="role_id">{{__('voyager.profile.primary_role')}}</label>
                                        @php $roles = TCG\Voyager\Models\Role::all(); @endphp
                                        <select name="role_id" id="role_id" class="select2" placeholder="">
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}"
                                                        @if($role->id == $dataTypeContent->role_id) selected @endif>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8" style="margin-bottom: 0;">
                                    <div class="form-group">
                                        <label for="additional_roles">{{ __('voyager::profile.roles_additional') }}</label>
                                        @php
                                            $row     = $dataTypeRows->where('field', 'user_belongstomany_role_relationship')->first();
                                            if(is_string($row->details)){
                                                $options = json_decode($row->details);
                                            } else {
                                                $options = $row->details;
                                            }
                                        @endphp
                                        @include('voyager::formfields.relationship')
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- Avatar -->
                <div class="col-md-4">
                    <div class="panel panel-bordered">
                        <div class="panel-title">
                            <span class="font-weight-bold">{{__("Logo")}}</span>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                @if(isset($dataTypeContent->avatar))
                                    <img
                                        src="{{ filter_var($dataTypeContent->avatar, FILTER_VALIDATE_URL) ? $dataTypeContent->avatar : Voyager::image( $dataTypeContent->avatar ) }}"
                                        style="width:200; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10;"/>
                                @endif
                                <input type="file" data-name="avatar" name="avatar">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Avatar -->
            </div>

            <!-- Address Shipping / Billing -->
            <div class="row" style="position: relative; top: -20;">
                <div class="col-md-6">
                    <div class="panel panel-bordered">
                        <div class="panel-title">
                            {{__("Shipping Address")}}
                        </div>
                        <div class="panel-body">
                            @include('voyager::users.partials.address', ['type' => 'shipping'])
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-bordered">
                        <div class="panel-title">
                            {{__("Billing Address")}}
                        </div>
                        <div class="panel-body">
                            @include('voyager::users.partials.address', ['type' => 'billing'])
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Address Shipping / Billing-->

            <button type="submit" class="btn btn-primary pull-right save">
                {{ __('voyager::generic.save') }}
            </button>
        </form>

        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>
    </div>
@stop

        @section('javascript')
            <script>
                $('document').ready(function () {
                    $('.toggleswitch').bootstrapToggle();
                });
            </script>
@stop
