@extends('layouts.app')

@section('title', 'Hồ sơ nhân viên')

@section('content')
<div class="max-w-6xl mx-auto space-y-5" x-data="{ tab: 'info' }">
    @include('components.staff-profile', [
        'backUrl' => route('leader.staffList'),
        'backLabel' => 'Danh sách nhân sự ca',
        'assessmentLink' => [route('leader.scoring'), 'Mở màn hình chấm điểm năng lực'],
    ])
</div>
@endsection
