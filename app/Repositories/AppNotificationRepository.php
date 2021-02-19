<?php


namespace App\Repositories;

use App\Models\NotificationModels\NotificationModel;
use App\Traits\FirebaseFCM;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;

class AppNotificationRepository extends AppRepository
{

    use FirebaseFCM;

    private $data = null;
    private $type = null;
    private $topic = null;

    public function __construct(NotificationModel $notification)
    {
        parent::__construct($notification);
    }

    public function getAll(Request $request)
    {
        $process = NotificationModel::where('notifiable_id', Auth::id())
            ->orderBy('created_at', 'desc');

        return $process->get();
    }


    /**
     * get notification data
     *
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        $date = Carbon::now();
        $notification = NotificationModel::find($id);
        return $notification->update(['read_at' => $date->toDateTimeString()]);
    }



}
