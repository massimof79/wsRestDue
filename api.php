<?php
header("Content-Type: application/json; charset=utf-8");

// (opzionale) CORS per demo in locale / host diversi
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(204);
  exit;
}

$file = __DIR__ . "/messaggi.json";
if (!file_exists($file)) file_put_contents($file, json_encode([]));

function readAll($file) {
  $arr = json_decode(file_get_contents($file), true);
  return is_array($arr) ? $arr : [];
}
function writeAll($file, $arr) {
  file_put_contents($file, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
function bodyJson() {
  $b = json_decode(file_get_contents("php://input"), true);
  return is_array($b) ? $b : null;
}
function respond($code, $payload) {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

// ---- Routing: /api.php/messaggi oppure /api.php/messaggi/{id} ----
$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$pos = strpos($path, "api.php");
$after = ($pos !== false) ? substr($path, $pos + strlen("api.php")) : "";
$after = trim($after, "/"); // "messaggi/3"
$parts = $after === "" ? [] : explode("/", $after);

$resource = $parts[0] ?? null;            // "messaggi"
$id = isset($parts[1]) ? intval($parts[1]) : null;

if ($resource !== "messaggi") {
  respond(404, ["error" => "Risorsa non trovata. Usa /api.php/messaggi"]);
}

$messaggi = readAll($file);

// GET /messaggi  -> lista
if ($method === "GET" && $id === null) {
  respond(200, $messaggi);
}

// GET /messaggi/{id} -> dettaglio
if ($method === "GET" && $id !== null) {
  foreach ($messaggi as $m) {
    if ($m["id"] === $id) respond(200, $m);
  }
  respond(404, ["error" => "Messaggio non trovato"]);
}

// POST /messaggi -> crea
if ($method === "POST" && $id === null) {
  $b = bodyJson();
  if (!$b || !isset($b["testo"]) || trim($b["testo"]) === "") {
    respond(400, ["error" => "Campo 'testo' obbligatorio"]);
  }

  $newId = 1;
  foreach ($messaggi as $m) $newId = max($newId, $m["id"] + 1);

  $nuovo = [
    "id" => $newId,
    "testo" => trim($b["testo"])
  ];

  $messaggi[] = $nuovo;
  writeAll($file, $messaggi);

  respond(201, $nuovo);
}

// PUT /messaggi/{id} -> aggiorna
if ($method === "PUT" && $id !== null) {
  $b = bodyJson();
  if (!$b) respond(400, ["error" => "Body JSON non valido"]);
  if (!isset($b["testo"]) || trim($b["testo"]) === "") {
    respond(400, ["error" => "Campo 'testo' obbligatorio"]);
  }

  for ($i = 0; $i < count($messaggi); $i++) {
    if ($messaggi[$i]["id"] === $id) {
      $messaggi[$i]["testo"] = trim($b["testo"]);
      writeAll($file, $messaggi);
      respond(200, $messaggi[$i]);
    }
  }
  respond(404, ["error" => "Messaggio non trovato"]);
}

// DELETE /messaggi/{id} -> elimina
if ($method === "DELETE" && $id !== null) {
  $before = count($messaggi);
  $messaggi = array_values(array_filter($messaggi, fn($m) => $m["id"] !== $id));

  if (count($messaggi) === $before) {
    respond(404, ["error" => "Messaggio non trovato"]);
  }

  writeAll($file, $messaggi);
  respond(200, ["ok" => true]);
}

respond(405, ["error" => "Metodo non consentito per questo endpoint"]);
