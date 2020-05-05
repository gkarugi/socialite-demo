@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">My facebook pages <a href="{{ route('home') }}" class="btn btn-sm btn-success float-right">Back Home</a></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pages as $page)
                            <tr>
                                <td scope="row">{{ $loop->iteration }}</td>
                                <td>{{ $page['name'] }}</td>
                                <td>{{ $page['category'] }}</td>
                                <td class="overflow-hidden">{{ $page['id'] }}</td>
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
