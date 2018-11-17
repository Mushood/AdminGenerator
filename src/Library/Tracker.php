<?php

namespace App\Library;

use Illuminate\Support\Facades\Auth;
use App\Models\Session;

class Tracker
{
    /**
     * @param $request
     * @param null $model_type
     * @param null $model_id
     */
    public function trackVisitor($request, $model_type = null, $model_id = null)
    {
        $sessionCurrent = $request->session();

        if($model_id != 17) {
            $session = new Session();
            $session->id = $sessionCurrent->getId();

            if(Auth::user() != null)
                $session->user_id = Auth::user()->id;

            $session->ip_address = $request->ip();
            $session->user_agent = $request->header('User-Agent');
            $session->payload = collect($request->query());
            $session->last_activity = 0;
            $session->model_type = $model_type;
            $session->model_id = $model_id;
            $session->save();
        }
    }

    /**
     * @return array
     */
    public function modelVisitorStats()
    {
        $stats = [];

        $modelTypes = Session::select('model_type')->distinct()->get();

        foreach ($modelTypes as $type) {
            $modelIds = Session::where('model_type', $type->model_type)->select('model_id')->distinct()->get();
            foreach ($modelIds as $id) {
                $count = Session::where('model_type', $type->model_type)->where('model_id', $id->model_id)->count();
                $model = $type->model_type::find($id->model_id);
                $ref = $type->model_type::statRef;
                $stats[$model->$ref] = $count;
            }
        }

        return $stats;
    }
}
