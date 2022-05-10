@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Rewards <a href="{{ route('rewards.create') }}" class="btn btn-primary">Create</a></div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Reward ID</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Valid Until</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Actions</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach ($rewards as $reward)
                                  <tr>
                                      <th scope="row">{{ $reward->id }}</th>
                                      <td>{{ $reward->title }}</td>
                                      <td>{{ $reward->description }}</td>
                                      <td>{{ $reward->value }}</td>
                                      <td>{{ $reward->valid_until }}</td>
                                      <td>{{ $reward->created_at }}</td>
                                      <td>
                                        <form method="POST" action="{{ route('rewards.destroy', $reward->id) }}">
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
