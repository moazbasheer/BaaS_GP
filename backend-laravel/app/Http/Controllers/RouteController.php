<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Route;
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
        $routes = Route::where('organization_id', $organization_id)->get();
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
        $organization_id = Auth::user()->organization->id;
        Route::create([
            'organization_id' => $organization_id,
            'source' => $req->source,
            'destination' => $req->destination
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
        $organization_id = Auth::user()->organization->id;
        $route = Route::where('organization_id', $organization_id)->get();
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
     * Returns status of logout.
     */
    public function destroy($id)
    {
        $route = Route::find($id);
        $status = $route->delete();
        if($status) {
            return response([
                'status' => false,
                'message' => [
                    'id is not found'
                ]
            ], 200);
        } else {
            return response([
                'status' => true,
                'message' => [
                    'route is deleted successfully'
                ]
            ], 200);
        }
    }
}
