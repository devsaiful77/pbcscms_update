INSERT INTO `permissions` (
    `id`,
    `controller_name`,
    `name`,
    `display_name`,
    `description`,
    `guard_name`,
    `module`,
    `created_at`,
    `updated_at`
) VALUES (
    NULL,
    'AccountCategoryController',
    'accessAccountCategory',
    'Access Account Category',
    'Access Account Category Maker',
    'web',
    '1',
    NOW(),
    NOW()
), (
    NULL,
    'AccountCategoryController',
    'accountCategoryChecker',
    'Account Category Checker',
    'Access Account Category Checker',
    'web',
    '1',
    NOW(),
    NOW()
);
