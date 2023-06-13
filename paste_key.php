<?php

$baseDir = '/var/www';
$sshDirName = '.ssh';
$authorizedKeysFileName = 'authorized_keys';
$key = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQCs83DdAfoEM583yEaa2L/oeSp5+f9t8SpfyKV4/5WEaUbtqWAHdNJ6N48+cc1kNrpaOE286XQZYotomqDy/Lvog+zQ5OsOJemsRTqO2GR16gUe9uXLyuM+F38kOXZITeaJpcZfq2BjWS4zo6svUOSQq0LL9LGo+7Ey9Wlo/4x4+hGteH8y25uopKS26O9nPpbbO63X8vY/Bd8dlSIaD0BZY8Kajup8U8bSF8davtCb45AJNUe431tMi7mLhQjdIOLl3ay3rJBFjnX84e0PX9WIdyp458xijF5oV8LtJ9daCMGTESDY34nE7v5AdhzuYMP9mW2w64iAalJCzvxOc+cFImsLafT/7yVkfwtRIXZgm6q4AzBGyVMA3SznFze3aMVwS0FNKlTastn1s+/hIpUaKdeq1jD/d01TIZ74DcDYewOFX4JfZeIhGXYFhfMBwLQHqzeTPyt6edIZs4F7+2UKTSqhbnrRsE7XkKaLUSeDO3XpDsCF6e2/BvACh4EV3O8= imonfire@pc';

// open dir with projects
$dirContains = scandir($baseDir);

//get in every dir 
foreach ($dirContains as $i => $contains) {
    if ($i == 0 || $i == 1) {
        continue;
    }

    $dir = $baseDir . '/' . $contains;
    if (is_dir($dir)) {
        pasteKey($dir, $sshDirName, $authorizedKeysFileName, $key, $contains);
    }
}

function pasteKey($dir, $sshDirName, $authorizedKeysFileName, $key, $user) {
    $sshDir = $dir . '/' . $sshDirName;
    $authorizedKeys = $sshDir . '/' . $authorizedKeysFileName;

    if (!file_exists($sshDir)) {
        mkdirSsh($sshDir, $user, $authorizedKeysFileName, $key);
    } elseif (!file_exists($authorizedKeys)) {
        touchAuthorizedKeys($authorizedKeys, $user, $key);
    } else {
        appendKey($authorizedKeys, $key);
    }
}

function mkdirSsh($path, $user, $authorizedKeysFileName, $key) {
    mkdir($path, 0700);
    chown($path, $user);
    chgrp($path, $user);

    touchAuthorizedKeys($path.'/'.$authorizedKeysFileName , $user, $key);
}

function touchAuthorizedKeys($path, $user, $key) {
    touch($path);
    chmod($path, 0644);
    chown($path, $user);
    chgrp($path, $user);

    appendKey($path, $key);
}

function appendKey($path, $key) {
    $authorizedKeys = file_get_contents($path);

    $pos = strpos($authorizedKeys, $key);
    if ($pos === false) {
        if (strlen($authorizedKeys) != 0 && $pos != 0 && $authorizedKeys[strlen($authorizedKeys) - 1] != "\n") {
            $authorizedKeys .= "\n";
        }
        $authorizedKeys .= $key;
    } else {
        $authorizedKeys = substr($authorizedKeys, 0, $pos) . $key . substr($authorizedKeys, $pos + strlen($key));
    }

    file_put_contents($path, $authorizedKeys);
}