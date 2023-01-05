<?php
function get_user_data($username)
{
  global $db;
  $res = $db->query(sprintf("SELECT * FROM `users` WHERE `username`='%s'", $username));
  if ($res->num_rows > 0) {
    return $res->fetch_assoc();
  }
  return null;
}
function get_all_users_data()
{
  global $db;
  $res = $db->query("SELECT * FROM `users`");
  if ($res->num_rows > 0) {
    return $res->fetch_all(MYSQLI_ASSOC);
  }
  return null;
}

function authenticate($username = null, $password = null)
{
  if ($username != null && $password != null) {
    $user_data = get_user_data($username);
    return password_verify($password, $user_data['password_hash']);
  } else if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == true) return true;
  return false;
}

function register_user($username, $password)
{
  global $db;
  $values = [
    $username,
    password_hash($password, PASSWORD_DEFAULT),
  ];
  $q = $db->prepare("INSERT INTO `users` (username, password_hash) VALUES (?,?)");
  return $q->execute($values);
}
function login($username, $password)
{
  $_SESSION['authenticated'] = authenticate($username, $password);
  return $_SESSION['authenticated'];
}
function update_user($username, $new_password)
{
  global $db;
  $values = [
    password_hash($new_password, PASSWORD_DEFAULT),
    $username
  ];
  $q = $db->prepare("UPDATE `users` SET `password_hash`=? WHERE `username`=?");
  $q->execute($values);
  return $db->affected_rows;
}
function delete_user($uid)
{
  global $db;
  $q = $db->prepare("DELETE FROM `users` WHERE `username`=?");
  return $q->execute([$uid]);
}
function logout()
{
  session_destroy();
}
