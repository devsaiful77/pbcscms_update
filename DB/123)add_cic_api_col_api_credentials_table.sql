-- CIC API URL columns (App\Services\CIC\CICApiService)

ALTER TABLE api_credential
    ADD COLUMN CIC_CARD_STATUS_CHANGE_API_URL TEXT NULL DEFAULT NULL AFTER BPID_API_URL,
    ADD COLUMN CIC_CARD_DETAILS_BY_MOBILE_API_URL TEXT NULL DEFAULT NULL AFTER CIC_CARD_STATUS_CHANGE_API_URL,
    ADD COLUMN CIC_CARD_DETAILS_BY_ACCOUNT_API_URL TEXT NULL DEFAULT NULL AFTER CIC_CARD_DETAILS_BY_MOBILE_API_URL,
    ADD COLUMN CIC_CARD_TRANSACTIONS_API_URL TEXT NULL DEFAULT NULL AFTER CIC_CARD_DETAILS_BY_ACCOUNT_API_URL,
    ADD COLUMN CIC_CARD_DETAILS_API_URL TEXT NULL DEFAULT NULL AFTER CIC_CARD_TRANSACTIONS_API_URL,
    ADD COLUMN CIC_MINI_STATEMENT_API_URL TEXT NULL DEFAULT NULL AFTER CIC_CARD_DETAILS_API_URL,
    ADD COLUMN CIC_ACCOUNT_STATEMENT_API_URL TEXT NULL DEFAULT NULL AFTER CIC_MINI_STATEMENT_API_URL;

-- update CIC API URLs (UAT)

UPDATE api_credential
SET
    CIC_CARD_STATUS_CHANGE_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v2/card/status/change',
    CIC_CARD_DETAILS_BY_MOBILE_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v1/maskcard/details/by/mobile/number',
    CIC_CARD_DETAILS_BY_ACCOUNT_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v1/card/details/by/account/number',
    CIC_CARD_TRANSACTIONS_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v2/card/transactions',
    CIC_CARD_DETAILS_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/noncard/card-api/v2/card/details',
    CIC_MINI_STATEMENT_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v2/mini/statement',
    CIC_ACCOUNT_STATEMENT_API_URL = 'https://pblcon-uat-lb.primebank.com.bd:8443/core-api/api/v1/getAccountStatement'
WHERE id = 1;
