<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Route;
use App\Models\Path;
use Validator;
class RouteController extends Controller
{
   /**
     * @OA\Get(
     *      path="/api/organization/routes",
     *      operationId="get all routes",
     *      tags={"Routes"},
     *      summary="get all routes",
     *      description="get all routes",
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
     * Returns array of all routes.
     */
    public function index()
    {
        $organization_id = Auth::user()->organization->id;
        $all_routes = Route::where('organization_id', $organization_id)->get();
        $routes = [];
        foreach($all_routes as $route) {
            $routes[] = [
                'id' => $route->id,
                'source_name' => $route->source,
                'source_longitude' => $route->source_longitude,
                'source_latitude' => $route->source_latitude,
                'destination_name' => $route->destination,
                'destination_longitude' => $route->destination_longitude,
                'destination_latitude' => $route->destination_latitude
            ];
        }
        return response([
            'status' => true,
            'message' => $routes
        ], 200);
    }

    
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/organization/routes",
     *      operationId="add a route",
     *      tags={"Routes"},
     *      summary="add a route",
     *      description="add a route",
     *      @OA\Parameter(
     *          name="source",
     *          description="source of the route",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="destination",
     *          description="destination of the route",
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
     * Returns array of all routes.
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'source_name' => 'required',
            'source_longitude' => 'required',
            'source_latitude' => 'required',
            'destination_name' => 'required',
            'destination_longitude' => 'required',
            'destination_latitude' => 'required',
        ]);
        $message = [];
        $message = UserController::format_message($message, $validator);
        if($validator->fails()) {
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $organization_id = Auth::user()->organization->id;
        Route::create([
            'organization_id' => $organization_id,
            'name' => $req->name,
            'source' => $req->source_name,
            'source_longitude' => $req->source_longitude,
            'source_latitude' => $req->source_latitude,
            'destination' => $req->destination_name,
            'destination_longitude' => $req->destination_longitude,
            'destination_latitude' => $req->destination_latitude,
        ]);
        return response([
            'status' => true,
            'message' => ['route added successfully']
        ], 200);
    }

    /**
     * @OA\Get(
     *      path="/api/organization/routes/{id}",
     *      operationId="get a route",
     *      tags={"Routes"},
     *      summary="get a route",
     *      description="get a route",
     *      @OA\Parameter(
     *          name="source",
     *          description="source of the route",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="destination",
     *          description="destination of the route",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          description="id is the route id",
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
     * Returns a route object.
     */
    public function show($id)
    {
        if($id == null) {
            return response([
                'status' => false,
                'message' => [
                    'id is not provided'
                ]
            ], 200);
        }
        $organization_id = Auth::user()->organization->id;
        $route = Route::where('id', $id)->get();
        return response([
            'status' => true,
            'message' => $route
        ], 200);
    }


    public function edit($id)
    {
        //
    }

    /**
     * @OA\Put(
     *      path="/api/organization/routes/{id}",
     *      operationId="update the route",
     *      tags={"Routes"},
     *      summary="update the route",
     *      description="update the route",
     *      @OA\Parameter(
     *          name="source",
     *          description="source of the route",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="destination",
     *          description="destination of the route",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          description="id is the route id",
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
     * Returns status of the update.
     */
    public function update(Request $req, $id)
    {
        if($id == null) {
            return response([
                'status' => false,
                'message' => [
                    'id is not provided'
                ]
            ], 200);
        }
        $validator = Validator::make($req->all(), [
            'source' => 'required',
            'destination' => 'required'
        ]);
        $message = [];
        $message = $this->format_message($message, $validator);
        if($validator->fails()) {
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $route = Route::find($id);
        $route->source = $req->source;
        $route->destination = $req->destination;
        $route->save();
        return response([
            'status' => true,
            'message' => ['route updated successfully']
        ], 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/organization/routes/{id}",
     *      operationId="delete the route",
     *      tags={"Routes"},
     *      summary="delete the route",
     *      description="delete the route",
     *      @OA\Parameter(
     *          name="id",
     *          description="id is the route id",
     *          required=true,
     *          in="path"
     *      ),
     *       @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       security={
     *           {"api_key_security_example": {}}
     *       }
     *     )
     *
     * Returns status of logout.
     */
    public function destroy($id)
    {
        if($id == null) {
            return response([
                'status' => false,
                'message' => [
                    'id is not provided'
                ]
            ], 200);
        }
        
        $route = Route::find($id);
        
        if(!$route) {
            return response([
                'status' => false,
                'message' => [
                    'id is not found'
                ]
            ], 200);
        } else {
            Path::where('route_id', $route->id)->delete();
            $status = $route->delete();
            return response([
                'status' => true,
                'message' => [
                    'route is deleted successfully'
                ]
            ], 200);
        }
    }
}
