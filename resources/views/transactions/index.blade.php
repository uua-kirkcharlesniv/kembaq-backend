@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Transactions</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Deposit ID</th>
                                    <th scope="col">Withdraw ID</th>
                                  <th scope="col">Customer Name</th>
                                  <th scope="col">Points</th>
                                  <th scope="col">Successful</th>
                                  <th scope="col">Sent at</th>
                                </tr>
                              </thead>
                              <tbody>
                                  @foreach ($transactions as $transaction)
                                  <tr>
                                      <th scope="row">{{ $transaction->id }}</th>
                                      <td>{{ $transaction->deposit_id }}</td>
                                      <td>{{ $transaction->withdraw_id }}</td>
                                    <td>{{ explode('-', $transaction->to->name)[0] }}</td>
                                    <td>{{ $transaction->deposit->amount }}</td>
                                    <td>{{ $transaction->deposit->confirmed == 1 ? 'Yes' : 'No' }}</td>
                                    <td>{{ $transaction->created_at }}</td>
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
