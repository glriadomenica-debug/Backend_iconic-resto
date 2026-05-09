<?php

namespace App\Http\Controllers;

use App\Helpers\ApiMessage;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Roles::all();
            return ApiMessage::success("Sucessfully get roles data", $roles, 200);
        } catch (\Exception $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rule = [
                'name' => 'required|string'
            ];

            $message = [
                'name.required' => 'name is required'
            ];
            $validator = Validator::make($request->all(), $rule, $message);
            if ($validator->fails()) {
                return ApiMessage::error($validator->errors(), 400);
            }
            try {
                //save ke table role
                $role = new Roles();
                $role->name = $request->name;
                $role->save();

                DB::commit();
                return ApiMessage::success('Success', 'Registration successful', 201);
            } catch (\Throwable $th) {
                DB::rollBack();
                return ApiMessage::error($th->getMessage(), 500);
            }
        } catch (\Exception $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $roles = Roles::find($id);
            if (!$roles) {
                return ApiMessage::error('Error', 'Role not found', 400);
            }
            return ApiMessage::success('Success', $roles, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $rules = [
                'name' => 'sometimes|string|max:10'
            ];
            $message = [
                'name.required' => 'Name is required'
            ];
            $validator = Validator::make($request->all(), $rules, $message);
            if ($validator->fails()) {
                return ApiMessage::error('error', $validator->errors(), 400);
            }
            $role = Roles::find($id);

            if (!$role) {
                return ApiMessage::error('Error', 'Role not found', 404);
            }
            if ($request->has('name')) {
                $role->name = $request->name;
            }
            $role->save();
            return ApiMessage::success('Role successfully updated', $role, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error('Error', $th->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $role = Roles::find($id);
            if (!$role) {
                return ApiMessage::error('Error', 'Role not found', 404);
            }
            $role->delete();
            return ApiMessage::success('Role successfully deleted', null, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error('Error', $th->getMessage(), 400);
        }
    }
}
