<?php

namespace App\Http\Controllers;

use App\Models\Path;
use Illuminate\Http\Request;

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
        $paths = Path::where('route_id', $route_id)->get();
        if($id == null) {
            return response([
                'status' => false,
                'message' => 'id is not provided'
            ], 200);
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
        //
    }

    /**
     * @OA\Post(
     *      path="/api/organization/paths/{route_id}",
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Path  $path
     * @return \Illuminate\Http\Response
     */
    public function show(Path $path)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Path  $path
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Path $path)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Path  $path
     * @return \Illuminate\Http\Response
     */
    public function destroy(Path $path)
    {
        //
    }
}
