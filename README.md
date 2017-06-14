#DokuWiki-AuthMe

An CraftBukkit AuthMe authentication backend for DokuWiki.

Refer to [offical DokuWiki API](https://www.dokuwiki.org/devel:auth_plugins) for more information.

##Installation

Put the php file in the `lib/plugins` folder.

##Configuration

Create a new file, `local.protected.php`, in DokuWiki `conf` folder.

###Example configuration

```
<?php
//require_once('mysql.conf.php');
// MySQL + Minecraft backend

$conf['superuser']='sntc06';
$conf['openregister']= 1;
//$conf['disableactions'] = 'register';
$conf['forwardClearPass'] = 0;
$conf['useacl']      = 1;
$conf['defaultgroup'] = 'user';
$conf['authtype'] = 'authminecraft';
$conf['auth']['minecraft']['debug'] = 1;
$conf['auth']['minecraft']['server']   = 'localhost';
$conf['auth']['minecraft']['user']     = 'authme';
$conf['auth']['minecraft']['password'] = 'password';

$conf['auth']['minecraft']['database'] = 'minecraft_authme';
$conf['auth']['minecraft']['checkPass']= "SELECT password FROM authme WHERE username='%{user}'";
$conf['auth']['minecraft']['getUserInfo'] = "SELECT password, nick AS name, email AS mail
    FROM authme
    WHERE username='%{user}'";
$conf['auth']['minecraft']['getGroups']   = "SELECT name as `group`
    FROM groups g, authme u, usergroup ug
    WHERE u.uid = ug.uid
    AND g.gid = ug.gid
    AND u.username='%{user}'";
$conf['auth']['minecraft']['getUsers']    = "SELECT DISTINCT username AS user
    FROM authme AS u
    LEFT JOIN usergroup AS ug ON u.uid=ug.uid
    LEFT JOIN groups AS g ON ug.gid=g.gid";
$conf['auth']['minecraft']['FilterLogin'] = "u.username LIKE '%{user}'";
$conf['auth']['minecraft']['FilterName']  = "u.nick LIKE '%{name}'";
$conf['auth']['minecraft']['FilterEmail'] = "u.email LIKE '%{email}'";
$conf['auth']['minecraft']['FilterGroup'] = "g.name LIKE '%{group}'";
$conf['auth']['minecraft']['SortOrder']   = "ORDER BY username";
$conf['auth']['minecraft']['addUser']     = "INSERT INTO authme
    (username, password, email, nick)
    VALUES ('%{user}', '%{pass}', '%{email}',
        '%{name}')";
$conf['auth']['minecraft']['addGroup']    = "INSERT INTO groups (name)
    VALUES ('%{group}')";
$conf['auth']['minecraft']['addUserGroup']= "INSERT INTO usergroup (uid, gid)
    VALUES ('%{uid}', '%{gid}')";

$conf['auth']['minecraft']['delGroup']    = "DELETE FROM groups
    WHERE gid='%{gid}'";
$conf['auth']['minecraft']['getUserID']   = "SELECT uid AS id
    FROM authme
    WHERE username='%{user}'";
$conf['auth']['minecraft']['delUser']     = "DELETE FROM authme
    WHERE uid='%{uid}'";
$conf['auth']['minecraft']['delUserRefs'] = "DELETE FROM usergroup
    WHERE uid='%{uid}'";
$conf['auth']['minecraft']['updateUser']  = "UPDATE authme SET";
$conf['auth']['minecraft']['UpdateLogin'] = "username='%{user}'";
$conf['auth']['minecraft']['UpdatePass']  = "password='%{pass}'";
$conf['auth']['minecraft']['UpdateEmail'] = "email='%{email}'";
$conf['auth']['minecraft']['UpdateName']  = "nick='%{name}'";
$conf['auth']['minecraft']['UpdateTarget']= "WHERE uid=%{uid}";
$conf['auth']['minecraft']['delUserGroup']= "DELETE FROM usergroup
    WHERE uid='%{uid}'
    AND gid='%{gid}'";
$conf['auth']['minecraft']['getGroupID']  = "SELECT gid AS id
    FROM groups
    WHERE name='%{group}'";
```
