<?php if (!defined('APPLICATION')) exit();
/**
 * Use this file for settings that you don't want Vanilla to overwrite
 * in config.php. For example, if you put getenv(..) in config.php,
 * after an admin makes a change in the dashboard, Vanilla will rewrite
 * config.php and put the plaintext version of the env var.
 */

if (c('Garden.Installed')) {
    saveToConfig('Debug', getenv('DEBUG'), false);
    saveToConfig('Garden.UpdateToken', getenv('UPDATE_TOKEN'), false);

    saveToConfig('Database.Host', getenv('DB_HOSTNAME'), false);
    saveToConfig('Database.Name', getenv('DB_DATABASE'), false);
    saveToConfig('Database.User', getenv('DB_USERNAME'), false);
    saveToConfig('Database.Password', getenv('DB_PASSWORD'), false);

    saveToConfig('Garden.Cookie.Salt', getenv('COOKIE_SALT'), false);
    saveToConfig('Garden.Cookie.Domain', getenv('COOKIE_DOMAIN'), false);

    saveToConfig('Garden.Email.SupportName', getenv('MAIL_FROM_NAME'), false);
    saveToConfig('Garden.Email.SupportAddress', getenv('MAIL_FROM_ADDRESS'), false);
    saveToConfig('Garden.Email.SmtpHost', getenv('MAIL_SMTP_HOSTNAME'), false);
    saveToConfig('Garden.Email.SmtpUser', getenv('MAIL_SMTP_USERNAME'), false);
    saveToConfig('Garden.Email.SmtpPassword', getenv('MAIL_SMTP_PASSWORD'), false); # Get this in Gmail etc.
    saveToConfig('Garden.Email.SmtpPort', getenv('MAIL_SMTP_PORT'), false);
    saveToConfig('Garden.Email.SmtpSecurity', getenv('MAIL_SMTP_SECURITY'), false);

    saveToConfig('Garden.Registration.CaptchaPrivateKey', getenv('CAPTCHA_PRIVATE_KEY'), false);
    saveToConfig('Garden.Registration.CaptchaPublicKey', getenv('CAPTCHA_PUBLIC_KEY'), false);

    saveToConfig('Garden.TrustedDomains', getenv('PERSONNEL_HOST_NAME'), false);
}
