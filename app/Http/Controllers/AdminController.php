<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    //CREATE ACCOUNT
    public function admin_create(Request $request)
    {

        $admin = AdminModel::where('cancelled', 0)->where('email', $request->email)->first();
        if (!$admin) {
            $new_admin = new AdminModel;
            if ($request->password == $request->confirm_password) {
                if (strlen($request->password) < 5) {
                    return response()->json(
                        ['status' => "Password must be more or equal to 5 characters"],
                        403
                    );
                } else {
                    $new_admin->password = Hash::make($request->password);
                }
            } else {
                return response()->json(
                    ['status' => "doesn't match"],
                    403
                );
            }
            $new_admin->email = $request->email;
            $new_admin->f_name = $request->f_name;
            $new_admin->l_name = $request->l_name;
            $new_admin->cancelled = 0;
            $new_admin->token = md5(uniqid());
            $new_admin->save();
            return response()->json(
                ['status' => "success", 'new_admin' => $new_admin],
                200
            );
        } else {
            return response()->json([
                'failed' => [
                    'title' => 'Email',
                    'message' => 'Already exists'
                ],
            ], 400);
        }
    }

    //LOGIN ACCOUNT
    public function admin_login(Request $request)
    {
        $admin = AdminModel::where('cancelled', 0)->where('email', $request->email)->first();
        if ($admin) {
            $checkPass = Hash::check($request->password, $admin->password);
        }

        if (!$admin) {
            return response()->json(
                ['status' => 'Your email is incorrect'],
                403
            );
        } elseif ($checkPass === false) {
            return response()->json(
                ['status' => 'Your password is incorrect'],
                403
            );
        } else {
            $admin->token = md5(uniqid());
            $admin->save();
            return response()->json($admin, 200);
        }
    }
}
