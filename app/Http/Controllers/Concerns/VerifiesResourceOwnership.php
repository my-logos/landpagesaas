<?php

namespace App\Http\Controllers\Concerns;

/**
 * Trait for verifying resource ownership
 * Provides common ownership verification methods
 */
trait VerifiesResourceOwnership
{
    /**
     * Verify that a resource belongs to the user
     *
     * @param mixed $resource Resource model instance
     * @param mixed $user User instance
     * @param string $userIdField Field name for user ID (default: 'user_id')
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function verifyOwnership($resource, $user, string $userIdField = 'user_id'): void
    {
        $resourceUserId = is_object($resource) ? $resource->{$userIdField} : $resource[$userIdField] ?? null;

        if ($resourceUserId !== $user->id) {
            abort(403);
        }
    }

    /**
     * Verify that a resource ID belongs to the user
     *
     * @param string $modelClass Model class name
     * @param int $resourceId Resource ID
     * @param mixed $user User instance
     * @param string $userIdField Field name for user ID (default: 'user_id')
     * @return mixed Resource instance
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function verifyResourceOwnershipById(
        string $modelClass,
        int $resourceId,
        $user,
        string $userIdField = 'user_id'
    ) {
        $resource = $modelClass::where('id', $resourceId)
            ->where($userIdField, $user->id)
            ->firstOrFail();

        return $resource;
    }
}
