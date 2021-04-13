<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyNotificationRequest;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\City;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\DBNotification;
use App\Traits\FirebaseFCM;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationsController extends Controller
{
    use FirebaseFCM;

    public function index()
    {
        //abort_if(Gate::denies('notification_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $notifications = Notification::all();


        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        //abort_if(Gate::denies('notification_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $cities = City::get();

        $models = [
            'Offer',
            'Department',
            'Product',
            'News',
            'Job',
        ];

        return view('admin.notifications.create', compact('cities', 'models'));
    }

    public function store(StoreNotificationRequest $request)
    {
//        dd($request->all());
        $data = [
            'title' => $request->title,
            'msg' => $request['content'],
            'model_type' => $request->model_type,
            'model_id' => $request->model_id,
        ];

        $allUsers = [];
        foreach ($request->city_id as $index => $single_city_id) {
            Notification::create([
                'title' => $request->title,
                'content' => $request['content'],
                'model_type' => $request->model_type,
                'model_id' => $request->model_id,
                'city_id' => $single_city_id,
            ]);
            $data['city_id'] = $single_city_id;

            $users = User::with('firebaseToken')->where([
                ['accept_notifications', 1],
                ['city_id', $single_city_id]
            ])->get();

            foreach ($users as $user) {
                $allUsers[] = $user;
            }
            $data['to'] = implode(',', $users->pluck('name')->toArray());

            \Illuminate\Support\Facades\Notification::send($users, new DBNotification($data));


        }

        /***********************************/
        $this->sendNotificationsFCM($allUsers, $data);
        /***********************************/


        return redirect()->route('admin.notifications.index');
    }

    public function edit(Notification $notification)
    {
        //abort_if(Gate::denies('notification_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        $models = [
            'Offer',
            'Department',
            'Product',
            'News',
            'Job',
        ];

        return view('admin.notifications.edit', compact('notification', 'models'));
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        $notification->update($request->all());

        return redirect()->route('admin.notifications.index');
    }

    public function show(Notification $notification)
    {
        //abort_if(Gate::denies('notification_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.notifications.show', compact('notification'));
    }

    public function destroy(Notification $notification)
    {
        //abort_if(Gate::denies('notification_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $notification->delete();

        return back();
    }

    public function massDestroy(MassDestroyNotificationRequest $request)
    {
        Notification::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }


}
