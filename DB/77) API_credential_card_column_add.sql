

-- Card API add column

ALTER TABLE `api_credential`
    ADD COLUMN `PULL_CARD_API_BY_CIF` TEXT AFTER `Pull_API_URL`,
ADD COLUMN `PULL_CARD_API_BY_MOBILE` TEXT AFTER `PULL_CARD_API_BY_CIF`;

-- update CIF and Mobile Api

UPDATE `api_credential`
SET
    `PULL_CARD_API_BY_CIF` = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v1/card/details/by/cif',
    `PULL_CARD_API_BY_MOBILE` = 'https://pblcon-uat-lb.primebank.com.bd:8443/card-api/v1/card/details/by/mobile/number'
WHERE `id` = 1;