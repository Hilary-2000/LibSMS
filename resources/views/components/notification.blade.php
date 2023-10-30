@php
    function getTimeAgo($date)
    {
        $currentTimestamp = time();
        $timestamp = strtotime($date);
        $difference = $currentTimestamp - $timestamp;
        
        if ($difference < 60) {
            return $difference . " seconds ago";
        } elseif ($difference < 3600) {
            $minutes = floor($difference / 60);
            return $minutes . " minutes ago";
        } elseif ($difference < 86400) {
            $hours = floor($difference / 3600);
            return $hours . " hours ago";
        } elseif ($difference < 2592000) {
            $days = floor($difference / 86400);
            return $days . " days ago";
        } elseif ($difference < 31536000) {
            $months = floor($difference / 2592000);
            return $months . " months ago";
        } else {
            $years = floor($difference / 31536000);
            return $years . " years ago";
        }
    }
@endphp
<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
    <div class="p-3">
        <div class="row align-items-center">
            <div class="col">
                <h6 class="m-0" key="t-notifications"> Notifications </h6>
            </div>
            <div class="col-auto">
                <a href="#!" class="small" key="t-view-all"> View All ({{count($notifications)}}) </a>
            </div>
        </div>
    </div>
    <div data-simplebar style="max-height: 230px;">
            @if (count($notifications) > 0)
                @for ($i = 0; $i < count($notifications); $i++)
                    <a href="#" class="text-reset notification-item">
                        <div class="d-flex">
                            <div class="avatar-xs me-3">
                                <span class="avatar-title bg-white rounded-circle font-size-16 w-100">
                                    <i class="bx bx-badge-check text-success"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{Str::limit($notifications[$i]->notification_title, 50)}}</h6>
                                <div class="font-size-12 text-muted">
                                    <p class="mb-1" key="t-occidental">{{Str::limit(strip_tags($notifications[$i]->notification_content),50)}}</p>
                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">{{getTimeAgo($notifications[$i]->date_created)}}</span></p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endfor
            @else
                <a href="#" class="text-reset notification-item">
                    <div class="d-flex">
                        <div class="avatar-xs me-3">
                            <span class="avatar-title bg-success rounded-circle font-size-16">
                                <i class="bx bx-badge-check"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">No Notifications</h6>
                            <div class="font-size-12 text-muted">
                                <p class="mb-1" key="t-occidental">You have no notifications at the moment.</p>
                                {{-- <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span key="t-hours-ago">1 hours ago</span></p> --}}
                            </div>
                        </div>
                    </div>
                </a>
        @endif
    </div>
    <div class="p-2 border-top d-grid">
        <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
            <i class="mdi mdi-arrow-right-circle me-1"></i> <span key="t-view-more">View More..</span>
        </a>
    </div>
</div>
