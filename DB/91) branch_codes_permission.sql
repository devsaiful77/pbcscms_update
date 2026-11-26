INSERT INTO
    `permissions` (
        `id`,
        `controller_name`,
        `name`,
        `display_name`,
        `description`,
        `guard_name`,
        `module`,
        `created_at`,
        `updated_at`
    )
VALUES (
        NULL,
        'BranchCodeController',
        'accessBranchCode',
        'Access BranchCode',
        'Access All',
        'web',
        '1',
        '2023-06-08 06:12:54',
        '2023-06-08 06:12:54'
    );

INSERT INTO
    `permissions` (
        `id`,
        `controller_name`,
        `name`,
        `display_name`,
        `description`,
        `guard_name`,
        `module`,
        `created_at`,
        `updated_at`
    )
VALUES (
        NULL,
        'BranchCodeController',
        'branchCodeChecker',
        'BranchCode Checker',
        'Access Checker',
        'web',
        '1',
        '2023-06-08 06:12:54',
        '2023-06-08 06:12:54'
    );