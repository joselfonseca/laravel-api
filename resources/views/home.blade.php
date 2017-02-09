@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Menu</div>
                <ul class="panel-body nav nav-pills nav-stacked" role="tablist">
                    <li role="presentation" class="active"><a href="#tokens" aria-controls="tokens" role="tab" data-toggle="tab">Personal access tokens</a></li>
                    <li role="presentation" class=""><a href="#clients" aria-controls="tokens" role="tab" data-toggle="tab">Clients</a></li>
                    <li role="presentation" class=""><a href="#auth-clients" aria-controls="tokens" role="tab" data-toggle="tab">Authorized clients</a></li>
                    <li><a href="{{ url('apidocs') }}" target="_blank">Api Docs</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tokens">
                    <passport-personal-access-tokens></passport-personal-access-tokens>
                </div>
                <div role="tabpanel" class="tab-pane" id="clients">
                    <passport-clients></passport-clients>
                </div>
                <div role="tabpanel" class="tab-pane" id="auth-clients">
                    <passport-authorized-clients></passport-authorized-clients>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
