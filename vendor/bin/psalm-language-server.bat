@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vimeo/psalm/psalm-language-server
php "%BIN_TARGET%" %*
