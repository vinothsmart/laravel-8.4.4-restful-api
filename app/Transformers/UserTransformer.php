<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        $roleId = "";
        $roleName = "";

        $roles = $user->roles;

        foreach ($roles as $role) {
            $roleId = $role->id;
            $roleName = $role->role;
        }

        return [
            'userId' => (int) $user->id,
            'userName' => (string) $user->name,
            'userEmail' => (string) $user->email,
            'userEmailVerifiedDate' => (string) $user->email_verified_at,
            'image' => (string) url("fileuploads/{$user->image}"),
            'isVerified' => (int) $user->verified,
            'isAdmin' => ($user->admin === 'true'),
            'creationDate' => (string) $user->created_at,
            'lastChange' => (string) $user->updated_at,
            'deletedDate' => isset($user->deleted_at) ? (string) $user->deleted_at : null,
            'clientDetails' => (string) $user->client_details,
            'roles' => [
                'userRoleId' =>(int) $roleId,
                'userRole' => $roleName,
            ],
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', (int) $user->id),
                ],
                [
                    'rel' => 'users.roles',
                    'href' => route('users.roles.index', (int) $user->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'userId' => 'id',
            'userName' => 'name',
            'userEmail' => 'email',
            'password' => 'password',
            'password_confirmation' => 'password_confirmation',
            'userEmailVerifiedDate' => 'email_verified_at',
            'image' => 'image',
            'isVerified' => 'verified',
            'isAdmin' => 'admin',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at',
            'userRoleId' => 'role_id',
            'clientDetails' => 'client_details',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'userId',
            'name' => 'userName',
            'email' => 'userEmail',
            'email_verified_at' => 'userEmailVerifiedDate',
            'password' => 'password',
            'password_confirmation' => 'password_confirmation',
            'image' => 'image',
            'verified' => 'isVerified',
            'admin' => 'isAdmin',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletedDate',
            'role_id' => 'userRoleId',
            'client_details' => 'clientDetails',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
