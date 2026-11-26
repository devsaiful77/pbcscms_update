# CIC API Log Check (UAT)

"Card transaction not found." / "Mini statement not found." dekhale API-r raw response check korar query.

```sql
SELECT id, service, url, status_code, status_msg, json_node, created_at
FROM osb_api_responses
WHERE service IN ('Card Mini Statement', 'Card Transactions')
ORDER BY id DESC
LIMIT 10;
```

## `json_node` e ki thakle ki bujhbo

| Response | Karon |
|---|---|
| `"status":400`, `"error":"Bad Request"` | Payload issue (beshirbhag somoy `cifNumber` faka) |
| `"status":401` | Token / authorization issue |
| `responseCode` 404 ba onno code, `messages` faka | Card ta credit card na / data nai |
