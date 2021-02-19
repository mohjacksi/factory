<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationRequest;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Http\Resources\Admin\NotificationResource;
use App\Models\Notification;
use App\Repositories\AppNotificationRepository;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AppNotificationsApiController extends Controller
{

    use ResponseTrait;

    protected $notificationRepo ;

    public function __construct(AppNotificationRepository $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * @param NotificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getAll(NotificationRequest $request)
    {
        $process = $this->notificationRepo->getAll($request);

        return $this->sendResponse($process, 'Notification retrieved successfully.');

    }


    /**
     * @param NotificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function markAsRead(NotificationRequest $request)
    {
        $date = Carbon::now();
        $notifications = Auth::user()->unreadNotifications;
        foreach ($notifications as $notification) {
            $notification->read_at = $date->toDateTimeString();
            $notification->save();
        }
        return $this->sendResponse(true);
    }

    public function totalUnreadNotificationsCount()
    {
        $process = Auth::user()->unreadNotifications()->count();
        return $this->sendResponse($process);
    }

    /**
     * total unread notifications
     * @return mixed
     */
    public function getTotalUnreadNotifications()
    {

//        return Auth::user()->unreadNotifications()->paginate();
        return $this->sendResponse(Auth::user()->unreadNotifications);

    }


    /**
     * @param NotificationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function get(NotificationRequest $request)
    {
        $process = $this->notificationRepo->get($request->id);
        return $this->sendResponse($process);
    }


}
