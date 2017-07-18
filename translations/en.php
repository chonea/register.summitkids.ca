<?php

/**
 * Please note: we can use unencoded characters like ö, é etc here as we use the html5 doctype with utf8 encoding
 * in the application's header (in views/_header.php). To add new languages simply copy this file,
 * and create a language switch in your root files.
 */

// login & registration classes
define("MESSAGE_ACCOUNT_NOT_ACTIVATED", "Your account is not activated yet. Please click on the confirm link in the mail.");
define("MESSAGE_CAPTCHA_WRONG", "Captcha was wrong!");
define("MESSAGE_COOKIE_INVALID", "Invalid cookie.");
define("MESSAGE_DATABASE_ERROR", "Database connection problem.");
define("MESSAGE_EMAIL_ALREADY_EXISTS", "This email address is already registered. Please use the \"I forgot my password\" page if you don't remember it.");
define("MESSAGE_EMAIL_CHANGE_FAILED", "Sorry, your email change failed.");
define("MESSAGE_EMAIL_CHANGED_SUCCESSFULLY", "Your email address has been changed successfully. New email address is ");
define("MESSAGE_EMAIL_EMPTY", "Email cannot be empty.");
define("MESSAGE_EMAIL_INVALID", "Your email address is not in a valid email format.");
define("MESSAGE_EMAIL_SAME_LIKE_OLD_ONE", "Sorry, that email address is the same as your current one. Please choose another one.");
define("MESSAGE_EMAIL_TOO_LONG", "Email cannot be longer than 64 characters.");
define("MESSAGE_LINK_PARAMETER_EMPTY", "Empty link parameter data.");
define("MESSAGE_LOGGED_OUT", "You have been logged out.");
// The "login failed"-message is a security improved feedback that doesn't show a potential attacker if the user exists or not
define("MESSAGE_LOGIN_FAILED", "Login failed.");
define("MESSAGE_NAME_INVALID", "Please enter both first and last name.");
define("MESSAGE_OLD_PASSWORD_WRONG", "Your OLD password was wrong.");
define("MESSAGE_PASSWORD_BAD_CONFIRM", "Password and password repeat are not the same.");
define("MESSAGE_PASSWORD_CHANGE_FAILED", "Sorry, your password change failed.");
define("MESSAGE_PASSWORD_CHANGED_SUCCESSFULLY", "Password successfully changed!");
define("MESSAGE_PASSWORD_EMPTY", "Password field was empty.");
define("MESSAGE_PASSWORD_RESET_MAIL_FAILED", "Password reset mail NOT successfully sent! Error: ");
define("MESSAGE_PASSWORD_RESET_MAIL_SUCCESSFULLY_SENT", "Password reset mail successfully sent!");
define("MESSAGE_PASSWORD_TOO_SHORT", "Password has a minimum length of 6 characters.");
define("MESSAGE_PASSWORD_WRONG", "Wrong password. Try again.");
define("MESSAGE_PASSWORD_WRONG_3_TIMES", "You have entered an incorrect password 3 or more times already. Please wait 30 seconds to try again.");
define("MESSAGE_REGISTRATION_ACTIVATION_NOT_SUCCESSFUL", "Sorry, no such id/verification code combination.");
define("MESSAGE_REGISTRATION_ACTIVATION_SUCCESSFUL", "Activation was successful! You can now log in!");
define("MESSAGE_REGISTRATION_FAILED", "Sorry, your registration failed. Please go back and try again.");
define("MESSAGE_RESET_LINK_HAS_EXPIRED", "Your reset link has expired. Please use the reset link within one hour.");
define("MESSAGE_VERIFICATION_MAIL_ERROR", "Sorry, we could not send you an verification mail. Your account has NOT been created.");
define("MESSAGE_VERIFICATION_MAIL_NOT_SENT", "Verification Mail NOT successfully sent! Error: ");
define("MESSAGE_VERIFICATION_MAIL_SENT", "Your account has been created successfully and we have sent you an email. Please click the VERIFICATION LINK within that mail.");
define("MESSAGE_USER_DOES_NOT_EXIST", "This user does not exist.");
define("MESSAGE_USERNAME_BAD_LENGTH", "Username cannot be shorter than 2 or longer than 64 characters.");
define("MESSAGE_USERNAME_CHANGE_FAILED", "Sorry, your chosen username renaming failed.");
define("MESSAGE_USERNAME_CHANGED_SUCCESSFULLY", "Your username has been changed successfully. New username is ");
define("MESSAGE_USERNAME_EMPTY", "Username field was empty.");
define("MESSAGE_USERNAME_EXISTS", "Sorry, that username is already taken. Please choose another one.");
define("MESSAGE_USERNAME_INVALID", "Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters.");
define("MESSAGE_USERNAME_SAME_LIKE_OLD_ONE", "Sorry, that username is the same as your current one. Please choose another one.");

// views
define("WORDING_WELCOME", "Welcome");
define("WORDING_BACK_TO_LOGIN", "Return to Login");
define("WORDING_CHANGE_FIRSTLAST_NAME", "Change name");
define("WORDING_CHANGE_EMAIL", "Change Email");
define("WORDING_CHANGE_PASSWORD", "Change Password");
define("WORDING_CHANGE_USERNAME", "Change Username");
define("WORDING_CURRENTLY", "currently");
define("WORDING_EDIT_USER_DATA", "Edit Account");
define("WORDING_EDIT_YOUR_CREDENTIALS", "Edit Account");
define("WORDING_FORGOT_MY_PASSWORD", "Forgot my Password");
define("WORDING_LOGIN", "Login");
define("WORDING_LOGOUT", "Logout");
define("WORDING_NEW_FIRST_NAME", "First Name");
define("WORDING_NEW_LAST_NAME", "Last Name");
define("WORDING_NEW_EMAIL", "Email");
define("WORDING_NEW_PASSWORD", "New Password");
define("WORDING_NEW_PASSWORD_REPEAT", "Repeat New Password");
define("WORDING_NEW_USERNAME", "New Username");
define("WORDING_OLD_PASSWORD", "Your OLD Password");
define("WORDING_PASSWORD", "Password");
define("WORDING_PROFILE_PICTURE", "Your Profile Picture (from Gravatar):");
define("WORDING_REGISTER", "Signup");
define("WORDING_REGISTER_NEW_ACCOUNT", "Create an Account");
define("WORDING_REGISTRATION_MY_INFO", "My Info");
define("WORDING_REGISTRATION_CAPTCHA", "Enter Captcha");
define("WORDING_REGISTRATION_FIRST_NAME", "First Name");
define("WORDING_REGISTRATION_LAST_NAME", "Last Name");
define("WORDING_REGISTRATION_EMAIL", "Email Address");
define("WORDING_REGISTRATION_PASSWORD", "Password");
define("WORDING_REGISTRATION_PASSWORD_REPEAT", "Confirm Password");
define("WORDING_REGISTRATION_USERNAME", "Username");
define("WORDING_REMEMBER_ME", "Keep Me Logged In");
define("WORDING_REQUEST_PASSWORD_RESET", "Email Address");
define("WORDING_RESET_PASSWORD", "Reset My Password");
define("WORDING_SUBMIT_NEW_PASSWORD", "Submit New Password");
define("WORDING_USERNAME", "Email");
define("WORDING_YOU_ARE_LOGGED_IN_AS", "You are logged in as ");
define("WORDING_ENROLL", "New Registration");
define("WORDING_REENROLL", "Re-Registration");
