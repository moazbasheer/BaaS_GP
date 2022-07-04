<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Validator;
use App\Imports\DriversImport;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Driver;
use Excel;
class DriverController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/organization/drivers",
     *      operationId="create driver",
     *      tags={"Drivers"},
     *      summary="create driver",
     *      description="create driver",
     *      @OA\Parameter(
     *          name="email",
     *          description="email",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="password",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="role_name",
     *          description="role name",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="name",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="address",
     *          description="address",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="phone",
     *          description="phone",
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
    public function create_driver(Request $req) {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string|size:11'
        ]);
        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $user = User::create([
            'email' => $req->email,
            'role_name' => 'driver',
            'password' => Hash::make($req->password)
        ]);
        $role = Role::where('name', 'driver')->first();
        $user->assignRole($role);
        Driver::create([
            'user_id' => $user->id,
            'name' => $req->name,
            'address' => $req->address,
            'phone' => $req->phone,
            'activated' => 1
        ]);
        return response([
            'status' => true,
            'message' => ['driver is added successfully']
        ], 200);
    }
    /**
     * @OA\Post(
     *      path="/api/organization/drivers/import",
     *      operationId="import drivers",
     *      tags={"Drivers"},
     *      summary="import drivers",
     *      description="import drivers",
     *      @OA\Parameter(
     *          name="file",
     *          description="the data file",
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
    public function import_drivers_data(Request $req) {
        $validator = Validator::make($req->all(), [
            'file' => 'required|file'
        ]);
        $message = [];
        if($validator->fails()) {
            $message = $this->format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $import = new DriversImport();
        //$import->import($req->file('file'));
        Excel::import(new DriversImport, $req->file);
        return response([
            'status' => true,
            'message' => ['drivers are added succesfully']
        ]);
    }
}
