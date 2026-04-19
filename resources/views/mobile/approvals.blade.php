@extends('layout.mobile')

@section('mobile-content')
<div class="topbar mobile-top">
    <div class="topbar-title"><h3>Approval Queue</h3><span>Kitchen manager view</span></div>
    <div class="profile">M</div>
</div>

<div class="tabs">
    <div class="tab active">Pending</div>
    <div class="tab">Urgent</div>
    <div class="tab">Approved</div>
</div>

@foreach($approvalRequests as $request)
    <div class="card">
        <div class="approval-card">
            <div><h5>{{ $request['code'] }}</h5><p>{{ $request['requester'] }} · {{ $request['department'] }} · Needed {{ $request['neededBy'] }}</p></div>
            <div class="pill {{ $request['priority'] === 'Urgent' ? 'orange' : 'gray' }}">{{ $request['priority'] }}</div>
        </div>
        <div class="approval-card">
            <div><h5>{{ $request['summary'] }}</h5><p>{{ $request['priority'] === 'Urgent' ? '3 items · Requested 25 minutes ago' : '6 items · Needed tomorrow morning' }}</p></div>
            <div class="pill blue">Review</div>
        </div>
        <div class="grid-2">
            <div class="button soft">{{ $request['priority'] === 'Urgent' ? 'Return' : 'Reject' }}</div>
            <div class="button primary">{{ $request['priority'] === 'Urgent' ? 'Approve' : 'Open' }}</div>
        </div>
    </div>
@endforeach
@endsection

@section('mobile-footer')
<div class="mobile-nav">
    <div>Home</div><div class="active">Queue</div><div>Reports</div><div>Profile</div>
</div>
@endsection
