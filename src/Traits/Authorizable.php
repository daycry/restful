<?php

declare(strict_types=1);

namespace Daycry\RestFul\Traits;

trait Authorizable
{
    /**
     * Checks user scopes and their group permissions
     * to see if the user has a specific scope.
     *
     * @param string $scope string consisting of a scope and action, like `users.create`
     */
    public function can(string $scope): bool
    {
        $scope = strtolower($scope);

        $scopes = ($this->scopes) ? $this->scopes : [];
        if($this->check($scope, $scopes)) {
            return true;
        }

        $scopes = [];
        foreach($this->getGroups() as $group) {
            $scopes = array_unique(array_merge($scopes, $group->scopes));
        }

        return $this->check($scope, $scopes);
    }

    private function check(string $scope, array $scopes = []): bool
    {
        $response = false;
        if(!in_array($scope, $scopes, true)) {
            $check = substr($scope, 0, strpos($scope, '.')) . '.*';
            $response = (in_array($check, $scopes, true)) ? true : false;
        } else {
            $response = true;
        }

        return $response;
    }
}
