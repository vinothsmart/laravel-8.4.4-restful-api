<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Validation
         */
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required',
            'image' => 'required',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        /**
         * Role based condition
         */
        // Here we get hashids
        $encryptedRoleId = $request->role_id;

        // Decrypyt Role Id
        $decryptedRoleId = \Hashids::connection(\App\Role::class)->decode($encryptedRoleId);
        $roleId = $decryptedRoleId[0];

        if ($roleId == 1 || $roleId == 2) {
            $isAdmin = true;
        } else {
            $isAdmin = false;
        }
        $data['password'] = bcrypt($request->password);

        if ($request->file('image') == null) {
            $data['image'] = "default.jpeg";
        } else {
            $data['image'] = $request->file('image')->store('');
        }

        $data['email_verified_at'] = $isAdmin == true ? now() : null;
        $data['verified'] = $isAdmin == true ? User::VERIFIED_USER : User::UNVERIFIED_USER;
        $data['verification_token'] = $isAdmin == true ? null : User::generateVerificationCode();
        $data['admin'] = $isAdmin == true ? User::ADMIN_USER : User::REGULAR_USER;
        $data['client_details'] = $this->applicationDetector();

        $user = User::create($data);

        // Adding to Pivot Table
        $userRoleAssign = ['role_id' => $roleId, 'user_id' => $user->id];

        DB::table('role_user')
            ->insert($userRoleAssign);

        $user->roles;

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * Validation
         */
        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:' . User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        $this->validate($request, $rules);

        /**
         * Role based condition
         */
        if ($request->has('role_id')) {
            // Here we get hashids
            $encryptedRoleId = $request->role_id;

            // Decrypyt Role Id
            $decryptedRoleId = \Hashids::connection(\App\Role::class)->decode($encryptedRoleId);
            $roleId = $decryptedRoleId[0];
        } else {
            $roleOfuser = DB::table('role_user')->where('user_id', $user->id)->first();
            $roleId = $roleOfuser->role_id;
        }

        if ($roleId == 1 || $roleId == 2) {
            $isAdmin = true;
        } else {
            $isAdmin = false;
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email') && $user->email != $request->email) {
            $user->verified = $isAdmin == true ? User::VERIFIED_USER : User::UNVERIFIED_USER;
            $user->verification_token = $isAdmin == true ? null : User::generateVerificationCode();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            // Delete old image
            Storage::delete($user->image);
            $user->image = $request->file('image')
                ->store('');
        }

        if ($request->has('admin')) {

            $this->allowedAdminAction();

            if (!$user->isAdmin()) {
                return $this->errorResponse('Only Admin users can modify the admin field', 409);
            }
            $user->admin = $isAdmin == true ? $request->admin : User::REGULAR_USER;
        }

        // Update role
        if ($request->has('role_id')) {
            $userRoleAssign = ['role_id' => $roleId];
            DB::table('role_user')
                ->where('user_id', $user->id)
                ->update($userRoleAssign);

            // Getting Client Details
            $user->client_details = $this->applicationDetector();
        }

        if (!$user->isDirty()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        // Getting Client Details
        $user->client_details = $this->applicationDetector();

        $user->save();

        $user->roles;

        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        // Delete image
        Storage::delete($user->image);
        // Delete Role
        DB::table('role_user')
            ->where('user_id', $user->id)
            ->delete();
        return $this->showOne($user);
    }
}
