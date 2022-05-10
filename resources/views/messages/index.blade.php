@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Messages <a href="{{ route('marketing.create') }}" class="btn btn-primary">Create</a></div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                  <th scope="col">ID</th>
                                  <th scope="col">Title</th>
                                  <th scope="col">Link</th>
                                  <th scope="col">Message</th>
                                  <th scope="col">Sent at</th>
                                  <th scope="col">Actions</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach ($messages as $message)
                                  <tr>
                                    <th scope="row">{{ $message->id }}</th>
                                    <td>{{ $message->title }}</td>
                                    <td><a href="{{ $message->link }}">Link</a></td>
                                    <td>{{ $message->message }}</td>
                                    <td>{{ $message->created_at }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('marketing.destroy', $message->id) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                    
                                            <div class="form-group">
                                                <input type="submit" class="btn btn-danger delete-user" value="Delete">
                                            </div>
                                        </form>
                                    </td>
                                  </tr>
                                  @endforeach
                              
                                
                              </tbody>
                        </table>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
