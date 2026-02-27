Descrizione
Questa applicazione mostra un esempio semplice ma completo di Web Service REST realizzato in PHP e consumato da un client HTML + JavaScript.

L’applicazione gestisce una risorsa:
messaggi

Il backend espone un’API REST che consente di:

leggere tutti i messaggi

leggere un messaggio specifico

creare un messaggio

aggiornare un messaggio

eliminare un messaggio

I dati sono salvati in un file JSON (nessun database necessario), così il progetto è immediatamente eseguibile.

Struttura del progetto

Il progetto è composto da:

index.html
Interfaccia utente + client REST (usa fetch)

api.php
Web Service REST in PHP

messaggi.json
File dati (creato automaticamente)

Requisiti

È sufficiente un ambiente con:

PHP 7+

un web server locale

Ad esempio:

XAMPP
oppure

server PHP integrato:
php -S localhost:8000

Avvio

Inserire index.html e api.php nella stessa cartella

Avviare il server PHP nella cartella

php -S localhost:8000

Aprire il browser:

http://localhost:8000/index.html

Al primo utilizzo verrà creato automaticamente il file messaggi.json.

Modello REST

Risorsa: messaggi

Endpoint disponibili

GET /api.php/messaggi
Restituisce la lista dei messaggi

GET /api.php/messaggi/{id}
Restituisce un singolo messaggio

POST /api.php/messaggi
Crea un nuovo messaggio
Body JSON:

{
"testo": "ciao"
}

PUT /api.php/messaggi/{id}
Aggiorna un messaggio
Body JSON:

{
"testo": "nuovo testo"
}

DELETE /api.php/messaggi/{id}
Elimina un messaggio

Formato dati

Le richieste e risposte utilizzano JSON.

Esempio risposta GET:

{
"messaggi": [
{
"id": 1,
"testo": "Hello REST",
"creato_il": "2026-02-27T18:30:00+01:00"
}
]
}
