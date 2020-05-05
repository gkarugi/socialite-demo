@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    <p>You are logged in! <br> <br>From here you can set the user's password</p>
                    @if(auth()->user()->socialProfiles()->where('provider', '=','facebook')->first())
                        <a href="{{ route('fbPages') }}" class="btn btn-sm btn-info">List my facebook pages</a>
                    @endif
                </div>
                <div class="card-body">
                    <h3>Connected social profiles</h3>
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">provider</th>
                            <th scope="col">provider_user_id</th>
                            <th scope="col">oauth_token</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($socialProfiles as $profile)
                                <tr>
                                    <td scope="row">{{ $loop->iteration }}</td>
                                    <td>{{ $profile->provider }}</td>
                                    <td>{{ $profile->provider_user_id }}</td>
                                    <td class="overflow-hidden">{{ $profile->oauth_token }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
