@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">User-Merchant Subscriptions</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">User ID</th>
                                    <th scope="col">Customer Name</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach ($subscriptions as $subscription)
                                  <tr>
                                      <th scope="row">{{ $subscription->user_id }}</th>
                                      <th scope="row">{{ $subscription->user->name }}</th>
                                      
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
