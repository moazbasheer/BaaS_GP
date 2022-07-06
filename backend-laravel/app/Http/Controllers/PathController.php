<?php

namespace App\Http\Controllers;

use App\Models\Path;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\UserController;
use App\Models\Stop;
use App\Models\Route;
use Http;
class PathController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/organization/paths/{route_id}",
     *      operationId="get a route's paths",
     *      tags={"Paths"},
     *      summary="get a route's paths",
     *      description="get a route's paths",
     *      @OA\Parameter(
     *          name="route_id",
     *          description="route_id is the route id",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns a route's paths object.
     */
    public function index($route_id)
    {
        $sample_route = Route::where('id', $route_id)->first();
        if(!$sample_route) {
            return response([
                'status' => true,
                'message' => ['this id is not valid']
            ], 200);
        }
        $all_paths = Path::where('route_id', $route_id)->get();
        
        $paths = [];
        foreach($all_paths as $path) {
            $stops = [
                'id' => $path->id,
                'path_name' => $path->name,
                'stops' => []
            ];
            $all_stops = Stop::where('path_id', $path->id)->get();
            foreach($all_stops as $stop) {
                $stops['stops'][] = [
                    'name' => $stop->name,
                    'langitude' => $stop->longitude,
                    'latitude' => $stop->latitude
                ];
            }
            $stops["distance"] = $path->distance;
            $stops["time"] = $path->time;
            $stops["price"] = $path->price;
            $paths []= $stops;
        }
        
        return response([
            'status' => true,
            'message' => $paths
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * @OA\Post(
     *      path="/api/organization/paths",
     *      operationId="add a path to a route",
     *      tags={"Paths"},
     *      summary="add a path to a route",
     *      description="add a path to a route",
     *      @OA\Parameter(
     *          name="route_id",
     *          description="route_id is the route id",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="path",
     *          description="path is a json object contains path_name and stops and each stop has name, longitude and latitude",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns a route's paths object.
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'route_id' => 'required',
            'path' => 'required'
        ]);
        
        if(gettype($req->path) != "array" || array_keys($req->path) == range(0, count($req->path) - 1)) {
            return response([
                'status' => false,
                "message" => ['path must be a json object']
            ]);
        }
        
        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $message = [];
        $route_id = $req->route_id;
        if(!array_key_exists("stops", $req->path) || !count($req->path["stops"]) > 1) {
            $message []= "The path field should have stops field (non-empty arrays)";
        }
        if(!array_key_exists("path_name", $req->path) || is_null($req->path["path_name"]) ) {
            $message []= "The path field should have path_name field (non-empty string)";
        }
        
        if(!array_key_exists("stops", $req->path) || !count($req->path["stops"]) > 0 || !array_key_exists("path_name", $req->path) || is_null($req->path["path_name"])) {
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $path_stops = $req->path["stops"];
        $path_name = $req->path["path_name"];
        $all_paths = Path::where('route_id', $route_id)->get();
        if(gettype($path_stops) != "array" || array_keys($path_stops) !== range(0, count($path_stops) - 1)) {
            return response([
                'status' => false,
                "message" => ['stops must be a list']
            ]);
        }
        
        
        foreach($path_stops as $stop) {
            if(gettype($stop) != "array" || array_keys($stop) == range(0, count($stop) - 1)) {
                return response([
                    'status' => false,
                    "message" => ['all stops must be json objects']
                ]);
            }
            if(!array_key_exists("name", $stop) || 
            !array_key_exists("longitude", $stop) || 
            !array_key_exists("latitude", $stop)) {
                return response([
                    'status' => false,
                    'message' => ['all stops should have name, longitude and latitude']
                ], 200);
            }
        }
        foreach($all_paths as $path) {
            $stops = ['path_name' => $path->name, 'stops' => []];
            if($path->name == $path_name) {
                return response([
                    'status' => false,
                    'message' => ['this path name is already taken']
                ]);
            }
            $all_stops = Stop::where('path_id', $path->id)->get();

            foreach($all_stops as $stop) {
                $stops['stops'][] = [
                    'name' => $stop->name,
                    'longitude' => $stop->longitude,
                    'latitude' => $stop->latitude
                ];
                if(!$stop->name || is_null($stop->longitude) || is_null($stop->latitude)) {
                    return response([
                        'status' => false,
                        'message' => ['each stop should have name, longitude and latitude']
                    ], 200);
                }
            }
            if($path_stops == $stops["stops"]) {
                return response([
                    'status' => false,
                    'message' => ['this path is already taken']
                ]);
            }
        }
        if(count($path_stops) < 2) {
            return response([
                'status' => true,
                'message' => ['provide at least two stops']
            ], 200);
        }
        $str = "https://graphhopper.com/api/1/route?key=bd47e377-3534-45d4-95a1-e35b7a1ac81d";
        foreach($path_stops as $stop) {
            $str = $str . '&point=' . $stop["longitude"] . "," . $stop["latitude"];
        }
        $res = Http::get($str);
        if(!array_key_exists("paths", $res)) {
            response([
                'status' => true,
                'message' => ['longitude or the latitude is not valid']
            ], 200);
        }
        
        $path = $res["paths"][0];
        $distance = $path["distance"];
        $time = $path["time"];
        $price = 0.2 * $distance + 0.02 * $time + 15;
        
        $path = Path::create([
            'route_id' => $route_id,
            'name' => $path_name,
            'distance' => $distance,
            'time' => $time,
            'price' => $price
        ]);
        foreach($path_stops as $stop) {
            $stop["path_id"] = $path->id;
            Stop::create($stop);
        }
        return response([
            'status' => true,
            'message' => ['path is added successfully']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Path  $path
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $path = Path::find($id);
        $stops = ['path_name' => $path->name, 'stops' => []];
        $all_stops = Stop::where('path_id', $path->id)->get();
        foreach($all_stops as $stop) {
            $stops['stops'][] = [
                'name' => $stop->name,
                'langitude' => $stop->longitude,
                'latitude' => $stop->latitude
            ];
        }
        $stops["distance"] = $path->distance;
        $stops["time"] = $path->time;
        $stops["price"] = $path->price;
        return response([
            'status' => true,
            'message' => $stops
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Path  $path
     * @return \Illuminate\Http\Response
     */
    public function edit(Path $path)
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/organization/paths/{path_name}",
     *      operationId="update a path",
     *      tags={"Paths"},
     *      summary="update a path",
     *      description="update a path",
     *      @OA\Parameter(
     *          name="stops",
     *          description="array of stops of the path",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns status of the update
     */
    public function update(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'stops' => 'required',
        ]);
        $stops = $req->stops;
        $message = [];
        if($validator->fails()) {
            $message = UserController::format_message($message, $validator);
            
            return response([
                'status' => true,
                'message' => $message
            ], 200);
        } 
        if(gettype($req->stops) != 'array') {

            $message []= 'stops must be an array';
            return response([
                'status' => true,
                'message' => $message
            ], 200);
        }

        foreach($req->stops as $stop) {
            if(!$stop["name"] || !$stop["longitude"] || !$stop["latitude"]) {
                return response([
                    'status' => true,
                    'message' => ['all objects must contain name, longitude and latitude']
                ], 200);
            }
        }
        $str = "https://graphhopper.com/api/1/route?key=bd47e377-3534-45d4-95a1-e35b7a1ac81d";
        foreach($req->stops as $stop) {
            $str = $str . '&point=' . $stop["longitude"] . "," . $stop["latitude"];
        }
        
        $path = Http::get($str)["paths"][0];
        $distance = $path["distance"];
        $time = $path["time"];
        $price = 0.2 * $distance + 0.02 * $time + 15;

        $path_name = $req->path_name;
        $path = Path::where('name', $path_name)->first();
        $route_id = $path->route_id;
        $path->delete();
        $path = Path::create([
            'route_id' => $route_id,
            'name' => $path_name,
            'distance' => $distance,
            'time' => $time,
            'price' => $price
        ]);
        foreach($req->stops as $stop) {
            $stop["path_id"] = $path->id;
            Stop::create($stop);
        }
        return response([
            'status' => true,
            'message' => ['the path is updated successfully']
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/organization/paths/{path_id}",
     *      operationId="delete a path",
     *      tags={"Paths"},
     *      summary="delete a path",
     *      description="delete a path",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns a route's paths object.
     */
    public function destroy($path_name)
    {
        $path = Path::where('name', $path_name)->first();
        if(!$path) {
            return response([
                'status' => false,
                'message' => ['name is not found']
            ], 200);
        }
        Path::where('id', $path->id)->delete();
        return response([
            'status' => true,
            'message' => ['path is deleted successfully']
        ], 200);
    }
}
