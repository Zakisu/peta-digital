<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\ControllerTrait;
use App\Http\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Time;

class ScheduleController extends Controller
{
    use ResponseTrait,ControllerTrait;

    /**
	 * @var Schedule
	 */
	protected $table;

    public function __construct(Schedule $table)
	{
		$this->table       = $table;
		$this->module_name = 'Schedule';
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
			$result = $this->customTable($request);
			return $this->jsonResponse($result);
		} catch (\Exception $e) {
			report($e);
			return $this->jsonResponse("Internal Error Creating");
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
			\DB::beginTransaction();

            $scheduleArray = $request->input('id_day');
            $timeArray = $request->input('id_time');
            $count = sizeof($scheduleArray);

            $items = array();
            for($i = 0; $i < $count; $i++){
                $item = array(
                    'id_day' => $scheduleArray[$i],
                    'id_time' => $timeArray[$i],
                    'id_retail' => $request->id_retail
            );
                $items[] = $item;
            }
            Schedule::insert($items);

            \DB::commit();
            return $this->jsonResponse($user ,201);
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating" . $this->module_name,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Creating". $this->module_name,400);
		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        if (!$schedule) {
			return $this->jsonResponse("Not Found",400);
		}

		return $this->jsonResponse($schedule);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        if (empty($schedule)) {
			return $this->jsonResponse("Not Found",400);
		}
        try {
			\DB::beginTransaction();

            $schedule->id_time = $request->phone_number;
            $schedule->type_retail = $request->type_retail;

            \DB::commit();
            return $this->jsonResponse($user ,201);
		} catch (\Exception $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Internal Error Creating" . $this->module_name,400);
		} catch (\Throwable $e) {
			\DB::rollBack();
			report($e);
			return $this->jsonResponse("Error Creating". $this->module_name,400);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
