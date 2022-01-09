@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vimeo/psalm/psalm-plugin
php "%BIN_TARGET%" %*
