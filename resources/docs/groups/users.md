# Users


## List users


Returns the Users resource with the roles relation.

> Example request:

```bash
curl -X GET \
    -G "http://localhost/api/users" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/api/users"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://localhost/api/users',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


> Example response (200):

```json
{
    "data": [
        {
            "id": "fb8cab66-23e4-3f0b-8880-094f1ed59223",
            "name": "Mr. Dayton Pagac",
            "email": "urolfson@example.net",
            "created_at": "2020-12-30T20:48:33+00:00",
            "updated_at": "2020-12-30T20:48:33+00:00",
            "roles": {
                "data": []
            }
        },
        {
            "id": "6db61d41-17bc-3604-b7d5-83da76f9b03a",
            "name": "Annabel Senger",
            "email": "cstoltenberg@example.com",
            "created_at": "2020-12-30T20:48:33+00:00",
            "updated_at": "2020-12-30T20:48:33+00:00",
            "roles": {
                "data": []
            }
        }
    ],
    "meta": {
        "pagination": {
            "total": 2,
            "count": 2,
            "per_page": 20,
            "current_page": 1,
            "total_pages": 1,
            "links": {}
        }
    }
}
```
<div id="execution-results-GETapi-users" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-users"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users"></code></pre>
</div>
<div id="execution-error-GETapi-users" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users"></code></pre>
</div>
<form id="form-GETapi-users" data-method="GET" data-path="api/users" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-users', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/users</code></b>
</p>
</form>


## Get single user


Returns the User resource by it's uuid

> Example request:

```bash
curl -X GET \
    -G "http://localhost/api/users/minima" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json"
```

```javascript
const url = new URL(
    "http://localhost/api/users/minima"
);

let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response => response.json());
```

```php

$client = new \GuzzleHttp\Client();
$response = $client->get(
    'http://localhost/api/users/minima',
    [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));
```


> Example response (200):

```json
{
    "data": {
        "id": "c6eaae09-1a89-33ed-93a5-6c8528d810d8",
        "name": "Kaelyn Lemke",
        "email": "alta.heller@example.com",
        "created_at": "2020-12-30T20:48:33+00:00",
        "updated_at": "2020-12-30T20:48:33+00:00",
        "roles": {
            "data": []
        }
    }
}
```
<div id="execution-results-GETapi-users--uuid-" hidden>
    <blockquote>Received response<span id="execution-response-status-GETapi-users--uuid-"></span>:</blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-users--uuid-"></code></pre>
</div>
<div id="execution-error-GETapi-users--uuid-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-users--uuid-"></code></pre>
</div>
<form id="form-GETapi-users--uuid-" data-method="GET" data-path="api/users/{uuid}" data-authed="0" data-hasfiles="0" data-headers='{"Content-Type":"application\/json","Accept":"application\/json"}' onsubmit="event.preventDefault(); executeTryOut('GETapi-users--uuid-', this);">
<h3>
    Request&nbsp;&nbsp;&nbsp;
    </h3>
<p>
<small class="badge badge-green">GET</small>
 <b><code>api/users/{uuid}</code></b>
</p>
<h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
<p>
<b><code>uuid</code></b>&nbsp;&nbsp;<small>string</small>  &nbsp;
<input type="text" name="uuid" data-endpoint="GETapi-users--uuid-" data-component="url" required  hidden>
<br>
The UUID of the user.</p>
</form>



