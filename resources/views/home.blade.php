@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">API Utilities</div>
                <ul class="panel-body nav nav-pills nav-stacked" role="tablist">
                    <li role="presentation" class="active"><a href="#auth" aria-controls="tokens" role="tab" data-toggle="tab">Autentication documentation</a></li>
                    <li><a href="{{ url('apidocs') }}" target="_blank">Api documentation</a></li>
                    <li role="presentation" class=""><a href="#tokens" aria-controls="tokens" role="tab" data-toggle="tab">Personal access tokens</a></li>
                    <li role="presentation" class=""><a href="#clients" aria-controls="tokens" role="tab" data-toggle="tab">Clients</a></li>
                    <li role="presentation" class=""><a href="#auth-clients" aria-controls="tokens" role="tab" data-toggle="tab">Authorized clients</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-8">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="auth">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Authentication for your API
                        </div>
                        <div class="panel-body">
                            Here your Auth Docs
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tokens">
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
