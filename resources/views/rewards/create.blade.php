@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Reward</div>

                <div class="card-body">
                    <form action="{{ route('rewards.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" id="title">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control" name="description" id="description">
                        </div>
                        <div class="form-group">
                            <label>Value</label>
                            <input type="number" class="form-control" name="value" id="value">
                        </div>
                        <div class="form-group">
                            <label>Valid Until (days)</label>
                            <input type="number" class="form-control" name="days" id="days">
                        </div>

                        <br>
                        <input type="submit" name="send" value="Submit" class="btn btn-dark btn-block">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
