<?php
class LinkUsersByIPPlugin extends Gdn_Plugin {
  public function usersApiController_getOutput_handler($result, $controller, $in, $query, $row) {
    if (!Gdn::session()->checkPermission('Garden.Moderation.Manage')) {
      return $result;
    }

    $userID = $result['userID'];
    $ips = $this->getUserIps($userID);

    $result['ips'] = array_map(function ($ip) use ($userID) {
      $otherUsers = $this->getUsersbyIp($ip, $userID);
      return [ 'ip' => $ip, 'otherUsers' => $otherUsers ];
    }, $ips);

    return $result;
  }

  function getUserIps($userID) {
    $this->userModel = $this->userModel ?: new UserModel();
    $userModel = new UserModel();
    $encodedIps = $userModel->getIPs($userID);
    return array_map('ipDecode', $encodedIps);
  }

  function getUsersbyIp($ip, $ignoreUserId) {
    $this->userModel = $this->userModel ?: new UserModel();
    $filter = ['Keywords' => $ip];
    $users = $this->userModel->search($filter)->resultArray();

    // Only include other users (omit $ignoreUserId)
    $otherUsers = array_values(array_filter($users, function ($user) use ($ignoreUserId) {
      return $user['UserID'] != $ignoreUserId;
    }));

    return array_map(function ($user) {
      // Omit unnecessary/private keys
      return [
        'userID' => $user['UserID'],
        'name' => $user['Name']
      ];
    }, $otherUsers);
  }
}
