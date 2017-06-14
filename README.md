# DokuWiki-AuthMe

An CraftBukkit AuthMe authentication backend for DokuWiki.

Refer to [offical DokuWiki API](https://www.dokuwiki.org/devel:auth_plugins) for more information.
Based on `authpdo` plugin: https://www.dokuwiki.org/plugin:authpdo

## Installation

Put the php file in the `lib/plugins` folder.

## Configuration

Create a new file, `local.protected.php`, in DokuWiki `conf` folder.

### Example configuration

```
$conf['plugin']['authminecraft']['debug'] = 0;
$conf['plugin']['authminecraft']['dsn'] = 'mysql:host=localhost;dbname=minecraft_authme';
$conf['plugin']['authminecraft']['user'] = 'authme';
$conf['plugin']['authminecraft']['pass'] = '';

/**
 * statement to select a single user identified by its login name
 *
 * input: :user
 * return: user, name, mail, (clear|hash), [uid], [*]
 */
$conf['plugin']['authminecraft']['select-user'] = ' 
    SELECT `uid`,
    `username` AS "user",
    `realname` AS "name",
    `password` AS "hash",
    `email` AS "mail"
    FROM `authme`
    WHERE `authme`.`username` = :user';

$conf['plugin']['authminecraft']['check-pass'] = ''; 

$conf['plugin']['authminecraft']['select-user-groups'] = 'SELECT name as `group`
FROM groups g, authme u, usergroup ug
WHERE u.uid = ug.uid
AND g.gid = ug.gid
AND u.username= :user';
/**
 * Select all the existing group names                                                                                  
 *
 * return: group, [gid], [*]
 */
$conf['plugin']['authminecraft']['select-groups'] = 'SELECT `gid` AS "gid",
`name AS "group"
FROM `groups`';

/**
 * Create a new user
 *
 * input: :user, :name, :mail, (:clear|:hash)
 */
$conf['plugin']['authminecraft']['insert-user'] = '';

/**
 * Remove a user
 *
 * input: :user, [:uid], [*]
 */
$conf['plugin']['authminecraft']['delete-user'] = '';
/**                                                                                                                     
 * list user names matching the given criteria
 *
 * Make sure the list is distinct and sorted by user name. Apply the given limit and offset
 *
 * input: :user, :name, :mail, :group, :start, :end, :limit
 * out: user
 */
$conf['plugin']['authminecraft']['list-users'] = '
SELECT DISTINCT `username` AS "user"
  FROM `authme` AS U,
       `groups` AS G
 WHERE G.`name` LIKE :group
   AND U.`username` LIKE :user
   AND U.`realname`  LIKE :name
   AND U.`email`  LIKE :mail
ORDER BY `username` LIMIT :start, :limit

';

/**
 * count user names matching the given criteria
 *
 * Make sure the counted list is distinct
 *
 * input: :user, :name, :mail, :group
 * out: count
 */
$conf['plugin']['authminecraft']['count-users'] = '';
$conf['plugin']['authminecraft']['update-user-info'] = '';
$conf['plugin']['authminecraft']['update-user-login'] = '';
$conf['plugin']['authminecraft']['update-user-pass'] = '';

/**
 * Create a new group
 *
 * input: :group
 */
$conf['plugin']['authminecraft']['insert-group'] = '';

/**
 * Make user join group
 *
 * input: :user, [:uid], group, [:gid], [*]
 */
$conf['plugin']['authminecraft']['join-group'] = '
INSERT INTO `usergroup` ( `uid`, `gid`) VALUES ( :uid, :gid);
';

/**
 * Make user leave group
 *
 * input: :user, [:uid], group, [:gid], [*]
 */
$conf['plugin']['authminecraft']['leave-group'] = '
DELETE FROM `usergroup` WHERE `uid` = :uid AND `gid` = :gid;
';

```
