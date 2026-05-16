<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiMessage;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        try {
            $staff = Staff::all();
            return ApiMessage::success("Sucessfully get staffs data", $staff, 200);
        } catch (\Exception $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $rule = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'sex' => 'required|in:male,female',
                'phone_number' => 'nullable|string|max:25',
                'email' => 'required|email|unique:staff,email',
            ];

            $message = [
                'first_name.required' => 'first name is required',
                'last_name.required' => 'last name is required',
                'sex.required' => 'Sex is required',
                'email.required' => 'Email is required',
            ];
            $validator = Validator::make($request->all(), $rule, $message);
            if ($validator->fails()) {
                return ApiMessage::error($validator->errors(), 400);
            }
            if ($validator->fails()) {
                return ApiMessage::error(
                    $validator->errors(),
                    400
                );
            }
            DB::beginTransaction();

            try {

                $staff = new Staff();
                $staff->first_name = $request->first_name;
                $staff->last_name = $request->last_name;
                $staff->sex = $request->sex;
                $staff->phone_number = $request->phone_number;
                $staff->email = $request->email;
                $staff->save();

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

    public function show($id)
    {
        try {

            $staff = Staff::find($id);

            if (!$staff) {

                return ApiMessage::error(
                    'Staff not found',
                    404
                );
            }

            return ApiMessage::success(
                'Successfully get staff data',
                $staff,
                200
            );
        } catch (\Exception $th) {

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $staff = Staff::find($id);
            if (!$staff) {
                return ApiMessage::error('Staff not found', 404);
            }
            $rule = [
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'sex' => 'sometimes|in:male,female',
                'phone_number' => 'nullable|string|max:25',
                'email' => 'sometimes|email|unique:staff,email,' . $id,
            ];
            $validator = Validator::make(
                $request->all(),
                $rule
            );

            if ($validator->fails()) {
                return ApiMessage::error(
                    $validator->errors(),
                    400
                );
            }
            DB::beginTransaction();

            try {
                $staff->update($request->all());
                DB::commit();
                return ApiMessage::success(
                    'Successfully update staff',
                    $staff,
                    200
                );
            } catch (\Throwable $th) {
                DB::rollBack();
                return ApiMessage::error($th->getMessage(), 500);
            }
        } catch (\Exception $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $staff = Staff::find($id);
            if (!$staff) {
                return ApiMessage::error('Staff not found', 404);
            }
            DB::beginTransaction();
            try {
                $staff->delete();
                DB::commit();
                return ApiMessage::success('Successfully delete staff', null, 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return ApiMessage::error($th->getMessage(), 500);
            }
        } catch (\Exception $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }
}
