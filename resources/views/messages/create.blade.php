@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Message</div>

                <div class="card-body">
                    <form action="{{ route('marketing.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" id="title">
                        </div>
                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" class="form-control" name="link" id="link">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <input type="text" class="form-control" name="message" id="message">
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
