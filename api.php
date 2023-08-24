<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$host = "localhost";
$dbname = "database";
$username = "root";
$password = "";
$secretPassword = 'asd123';

function sanitize($input)
{
  return htmlspecialchars(strip_tags($input));
}

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error database conntection: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $stmt = $pdo->prepare("SELECT * FROM items");
  $stmt->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);
  http_response_code(200);
  exit;
}

if ($_POST['pass'] !== $secretPassword) {
  http_response_code(403);
  echo json_encode(["message" => "Wrong password"]);
  exit;
}

$type = sanitize($_POST["type"]);
$name = sanitize($_POST["name"]);
$extra_field = sanitize($_POST["extra_field"]);
$id = $_POST["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $stmt = $pdo->prepare("INSERT INTO items (type, name, extra_field, save_at) VALUES (:type, :name, :extra_field, NOW())");
  $stmt->bindParam(":type", $type);
  $stmt->bindParam(":name", $sanitize1);
  $stmt->bindParam(":extra_field", $extra_field);
  $stmt->execute();

  http_response_code(201);
  exit;
}

if (!is_int($id)) {
  http_response_code(400);
  echo json_encode(["message" => "ID must be integer"]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
  $stmt = $pdo->prepare("UPDATE items SET type = :type, name = :name, extra_field = :extra_field, save_at = NOW() WHERE id = :id");
  $stmt->bindParam(":type", $type);
  $stmt->bindParam(":name", $name);
  $stmt->bindParam(":extra_field", $extra_field);
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  http_response_code(204);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
  $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
  $stmt->bindParam(":id", $id);
  $stmt->execute();

  http_response_code(204);
  exit;
}

http_response_code(404);
echo json_encode(["message" => "Wrong request"]);