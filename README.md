# Airo

A small travel-insurance quotation service built with Laravel. It exposes a JWT-protected REST endpoint that prices a policy from a list of traveller ages, a currency, and a trip date range, persists each quote, and ships a Vue form page that calls the API and displays the result.

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Git

## Getting Started

**1. Clone the repository**
```bash
git clone https://github.com/ccordina/airo-test airo
cd airo
```

**2. Copy the environment file**
```bash
cp .env.example .env
```

**3. Install PHP dependencies**
```bash
docker run --rm -v $(pwd):/var/www/html -w /var/www/html laravelsail/php83-composer:latest composer install --no-interaction
```

**4. Start the containers**
```bash
./vendor/bin/sail up -d
```

**5. Generate the application key**
```bash
./vendor/bin/sail artisan key:generate
```

**6. Set the JWT secret**

API auth and the form's token are signed with `JWT_SECRET` (see [config/jwt.php](config/jwt.php)), which has no fallback and must be set. Generate a value and paste it into `JWT_SECRET` in your `.env`:
```bash
./vendor/bin/sail artisan key:generate --show
```

**7. Run the migrations**
```bash
./vendor/bin/sail artisan migrate
```

**8. Build the front-end assets**

The form is a Vue 3 single-file component compiled by Vite, so the assets must be built (or served) before the page works:
```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build      # production build
# or, for hot reload during development:
./vendor/bin/sail npm run dev
```

**9. Visit the app**

Open [http://localhost](http://localhost) and submit the form to get a quote.

## API

Base URL: `http://localhost`. All requests and responses are JSON.

### `POST /quotation`

Calculates and stores a quotation. Requires authentication.

**Headers**

| Key            | Value                  |
| -------------- | ---------------------- |
| Content-Type   | application/json       |
| Accept         | application/json       |
| Authorization  | Bearer &lt;JWT token&gt; |

**Request body** (all fields required)

| Key          | Description                                       | Example        |
| ------------ | ------------------------------------------------- | -------------- |
| age          | Comma-separated list of ages (each 18–70).        | `"28,35"`      |
| currency_id  | ISO 4217 code: `EUR`, `GBP`, or `USD`.            | `"EUR"`        |
| start_date   | Trip start date (ISO 8601).                       | `"2020-10-01"` |
| end_date     | Trip end date (ISO 8601), on/after `start_date`.  | `"2020-10-30"` |

**Response** `200 OK`

```json
{
  "total": 117.00,
  "currency_id": "EUR",
  "quotation_id": 1
}
```

**Error responses**

- `401 Unauthorized` — missing or invalid token.
- `422 Unprocessable Entity` — validation errors (Laravel error bag).

### `GET /currencies`

Returns the supported currencies for populating the form (no auth required).

```json
[
  { "value": "EUR", "label": "Euro" },
  { "value": "GBP", "label": "British Pound" },
  { "value": "USD", "label": "US Dollar" }
]
```

## Pricing rules

```
Total = Σ (Fixed Rate × Age Load × Trip Length)   for each age
Fixed Rate = 3 per day
```

Trip length is inclusive of both the start and end date (`2020-10-01` to `2020-10-30` = 30 days).

The fixed rate and age-load table live in [config/quotation.php](config/quotation.php).

## Authentication / getting a token

JWTs are verified by the `jwt` middleware ([app/Http/Middleware/JwtAuthenticator.php](app/Http/Middleware/JwtAuthenticator.php)) using the `JWT_SECRET`.
