# Mobile Sunday Capture API

Base path: `/api/mobile`

## Login

`POST /login`

```json
{
  "email": "treasurer@example.com",
  "password": "password",
  "device_name": "Treasurer Phone"
}
```

Returns a bearer token, current user, and active assemblies assigned to that user. Only `admin` and `treasurer` users can log in.

## Assigned Assemblies

`GET /assemblies`

Requires `Authorization: Bearer <token>`.

Returns only active assemblies available to the authenticated user.

## Chart Of Accounts

`GET /chart-accounts`

Optional query string: `?type=income` or `?type=expense`.

Returns active income, expense, asset, and equity accounts for mobile selection.

## Submit Transaction

`POST /transactions`

```json
{
  "mobile_client_id": "uuid-from-phone",
  "assembly_id": 1,
  "date": "2026-07-05",
  "type": "income",
  "flow": "offerings",
  "chart_account_id": 2,
  "category_purpose": "Sunday morning offering",
  "amount": 125.75,
  "currency": "USD",
  "payment_method": "cash",
  "notes": "Captured after service"
}
```

Allowed `type` values: `income`, `expense`.

Allowed `flow` values: `offerings`, `pledges`, `funeral_contributions`, `general_income`, `expenses`.

Allowed `payment_method` values: `cash`, `ecocash`, `bank_transfer`, `card`, `other`.

Mobile submissions are auto-approved and stored as `approved` in the main finance tables. The authenticated mobile user is recorded as both creator and approver. The `mobile_client_id` makes sync retries idempotent.

## Recent Submitted Transactions

`GET /transactions/recent`

Returns up to 20 recent records created by the authenticated user across their assigned assemblies.

## Mobile Sync Statuses

Local-only statuses in the Flutter app:

- `Draft`: saved locally, not yet sent.
- `Pending Sync`: currently being pushed or queued for retry.
- `Synced`: accepted by the cloud API.
- `Failed`: cloud submission failed; the record remains local for retry.
