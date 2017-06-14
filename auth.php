<?php
/**
<<<<<<< HEAD
 * DokuWiki Plugin authpdo (Auth Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <andi@splitbrain.org>
=======
 * DokuWiki Plugin authminecraft (Auth Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  sntc06 <taya86334@gmail.com>
>>>>>>> 545b3facd206f670a2d26bd70c783dedf84438a9
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

<<<<<<< HEAD
/**
 * Class auth_plugin_authpdo
 */
class auth_plugin_authminecraft extends DokuWiki_Auth_Plugin {

    /** @var PDO */
    protected $pdo;

    /** @var null|array The list of all groups */
    protected $groupcache = null;

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(); // for compatibility

        if(!class_exists('PDO')) {
            $this->_debug('PDO extension for PHP not found.', -1, __LINE__);
            $this->success = false;
            return;
        }

        if(!$this->getConf('dsn')) {
            $this->_debug('No DSN specified', -1, __LINE__);
=======
class auth_plugin_authminecraft extends DokuWiki_Auth_Plugin {

    var $dbcon        = 0;
    var $dbver        = 0;    // database version
    var $dbrev        = 0;    // database revision
    var $dbsub        = 0;    // database subrevision
    var $cnf          = null;
    var $defaultgroup = "";

    /**
     * Constructor
     *
     * checks if the mysql interface is available, otherwise it will
     * set the variable $success of the basis class to false
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function __construct() {
        global $conf;
        $this->cnf = $conf['auth']['minecraft'];

        if (method_exists($this, 'auth_basic')){
            parent::__construct();
        }

        if(!function_exists('mysql_connect')) {
            if ($this->cnf['debug']){
                msg("MySQL err: PHP MySQL extension not found.",-1,__LINE__,__FILE__);
            }
>>>>>>> 545b3facd206f670a2d26bd70c783dedf84438a9
            $this->success = false;
            return;
        }

<<<<<<< HEAD
        try {
            $this->pdo = new PDO(
                $this->getConf('dsn'),
                $this->getConf('user'),
                conf_decodeString($this->getConf('pass')),
                array(
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // always fetch as array
                    PDO::ATTR_EMULATE_PREPARES => true, // emulating prepares allows us to reuse param names
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // we want exceptions, not error codes
                )
            );
        } catch(PDOException $e) {
            $this->_debug($e);
            msg($this->getLang('connectfail'), -1);
            $this->success = false;
            return;
        }

        // can Users be created?
        $this->cando['addUser'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'select-groups',
                'insert-user',
                'insert-group',
                'join-group'
            )
        );

        // can Users be deleted?
        $this->cando['delUser'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'select-groups',
                'leave-group',
                'delete-user'
            )
        );

        // can login names be changed?
        $this->cando['modLogin'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'update-user-login'
            )
        );

        // can passwords be changed?
        $this->cando['modPass'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'update-user-pass'
            )
        );

        // can real names be changed?
        $this->cando['modName'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'update-user-info:name'
            )
        );

        // can real email be changed?
        $this->cando['modMail'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'update-user-info:mail'
            )
        );

        // can groups be changed?
        $this->cando['modGroups'] = $this->_chkcnf(
            array(
                'select-user',
                'select-user-groups',
                'select-groups',
                'leave-group',
                'join-group',
                'insert-group'
            )
        );

        // can a filtered list of users be retrieved?
        $this->cando['getUsers'] = $this->_chkcnf(
            array(
                'list-users'
            )
        );

        // can the number of users be retrieved?
        $this->cando['getUserCount'] = $this->_chkcnf(
            array(
                'count-users'
            )
        );

        // can a list of available groups be retrieved?
        $this->cando['getGroups'] = $this->_chkcnf(
            array(
                'select-groups'
            )
        );

        $this->success = true;
    }

    /**
     * Check user+password
     *
     * @param   string $user the user name
     * @param   string $pass the clear text password
     * @return  bool
     */
    public function checkPass($user, $pass) {

        $userdata = $this->_selectUser($user);
        if($userdata == false) return false;
        // password checking done in SQL?
        if($this->_chkcnf(array('check-pass'))) {
            $userdata['clear'] = $pass;
            $userdata['hash'] = auth_cryptPassword($pass);
            $result = $this->_query($this->getConf('check-pass'), $userdata);
            if($result === false) return false;
            return (count($result) == 1);
        }

        // we do password checking on our own
        if(isset($userdata['hash'])) {
            // hashed password
            // $passhash = new PassHash();
            $this->_debug($userdata['hash'], 0, __LINE__);

            $sha_info = explode("$",$userdata['hash']);
            if( $sha_info[1] === "SHA" ) {
	            $salt = $sha_info[2];
                    $sha256_password = hash('sha256', $pass);
                    $sha256_password .= $sha_info[2];
                    if( strcasecmp(trim($sha_info[3]),hash('sha256', $sha256_password) ) == 0 ) {
                    	$result = true;
                    }
		    else $result = false;
            }

            //$result = $passhash->verify_hash($pass, $userdata['hash']);
            return $result;
        } else {
            // clear text password in the database O_o
            return ($pass === $userdata['clear']);
        }
    }

    /**
     * Return user info
     *
     * Returns info about the given user needs to contain
     * at least these fields:
     *
     * name string  full name of the user
     * mail string  email addres of the user
     * grps array   list of groups the user is in
     *
     * @param   string $user the user name
     * @param   bool $requireGroups whether or not the returned data must include groups
     * @return array|bool containing user data or false
     */
    public function getUserData($user, $requireGroups = true) {
        $data = $this->_selectUser($user);
        if($data == false) return false;

        if(isset($data['hash'])) unset($data['hash']);
        if(isset($data['clean'])) unset($data['clean']);

        if($requireGroups) {
            $data['grps'] = $this->_selectUserGroups($data);
            if($data['grps'] === false) return false;
        }

        return $data;
    }

    /**
     * Create a new User [implement only where required/possible]
     *
     * Returns false if the user already exists, null when an error
     * occurred and true if everything went well.
     *
     * The new user HAS TO be added to the default group by this
     * function!
     *
     * Set addUser capability when implemented
     *
     * @param  string $user
     * @param  string $clear
     * @param  string $name
     * @param  string $mail
     * @param  null|array $grps
     * @return bool|null
     */
    public function createUser($user, $clear, $name, $mail, $grps = null) {
        global $conf;

        if(($info = $this->getUserData($user, false)) !== false) {
            msg($this->getLang('userexists'), -1);
            return false; // user already exists
        }

        // prepare data
        if($grps == null) $grps = array();
        array_unshift($grps, $conf['defaultgroup']);
        $grps = array_unique($grps);
        $hash = auth_cryptPassword($clear);
        $userdata = compact('user', 'clear', 'hash', 'name', 'mail');

        // action protected by transaction
        $this->pdo->beginTransaction();
        {
            // insert the user
            $ok = $this->_query($this->getConf('insert-user'), $userdata);
            if($ok === false) goto FAIL;
            $userdata = $this->getUserData($user, false);
            if($userdata === false) goto FAIL;

            // create all groups that do not exist, the refetch the groups
            $allgroups = $this->_selectGroups();
            foreach($grps as $group) {
                if(!isset($allgroups[$group])) {
                    $ok = $this->addGroup($group);
                    if($ok === false) goto FAIL;
                }
            }
            $allgroups = $this->_selectGroups();

            // add user to the groups
            foreach($grps as $group) {
                $ok = $this->_joinGroup($userdata, $allgroups[$group]);
                if($ok === false) goto FAIL;
            }
        }
        $this->pdo->commit();
        return true;

        // something went wrong, rollback
        FAIL:
        $this->pdo->rollBack();
        $this->_debug('Transaction rolled back', 0, __LINE__);
        msg($this->getLang('writefail'), -1);
        return null; // return error
    }

    /**
     * Modify user data
     *
     * @param   string $user nick of the user to be changed
     * @param   array $changes array of field/value pairs to be changed (password will be clear text)
     * @return  bool
     */
    public function modifyUser($user, $changes) {
        // secure everything in transaction
        $this->pdo->beginTransaction();
        {
            $olddata = $this->getUserData($user);
            $oldgroups = $olddata['grps'];
            unset($olddata['grps']);

            // changing the user name?
            if(isset($changes['user'])) {
                if($this->getUserData($changes['user'], false)) goto FAIL;
                $params = $olddata;
                $params['newlogin'] = $changes['user'];

                $ok = $this->_query($this->getConf('update-user-login'), $params);
                if($ok === false) goto FAIL;
            }

            // changing the password?
            if(isset($changes['pass'])) {
                $params = $olddata;
                $params['clear'] = $changes['pass'];
                $params['hash'] = auth_cryptPassword($changes['pass']);

                $ok = $this->_query($this->getConf('update-user-pass'), $params);
                if($ok === false) goto FAIL;
            }

            // changing info?
            if(isset($changes['mail']) || isset($changes['name'])) {
                $params = $olddata;
                if(isset($changes['mail'])) $params['mail'] = $changes['mail'];
                if(isset($changes['name'])) $params['name'] = $changes['name'];

                $ok = $this->_query($this->getConf('update-user-info'), $params);
                if($ok === false) goto FAIL;
            }

            // changing groups?
            if(isset($changes['grps'])) {
                $allgroups = $this->_selectGroups();

                // remove membership for previous groups
                foreach($oldgroups as $group) {
                    if(!in_array($group, $changes['grps']) && isset($allgroups[$group])) {
                        $ok = $this->_leaveGroup($olddata, $allgroups[$group]);
                        if($ok === false) goto FAIL;
                    }
                }

                // create all new groups that are missing
                $added = 0;
                foreach($changes['grps'] as $group) {
                    if(!isset($allgroups[$group])) {
                        $ok = $this->addGroup($group);
                        if($ok === false) goto FAIL;
                        $added++;
                    }
                }
                // reload group info
                if($added > 0) $allgroups = $this->_selectGroups();

                // add membership for new groups
                foreach($changes['grps'] as $group) {
                    if(!in_array($group, $oldgroups)) {
                        $ok = $this->_joinGroup($olddata, $allgroups[$group]);
                        if($ok === false) goto FAIL;
                    }
                }
            }

        }
        $this->pdo->commit();
        return true;

        // something went wrong, rollback
        FAIL:
        $this->pdo->rollBack();
        $this->_debug('Transaction rolled back', 0, __LINE__);
        msg($this->getLang('writefail'), -1);
        return false; // return error
    }

    /**
     * Delete one or more users
     *
     * Set delUser capability when implemented
     *
     * @param   array $users
     * @return  int    number of users deleted
     */
    public function deleteUsers($users) {
        $count = 0;
        foreach($users as $user) {
            if($this->_deleteUser($user)) $count++;
=======
        // default to UTF-8, you rarely want something else
        if(!isset($this->cnf['charset'])) $this->cnf['charset'] = 'utf8';

        $this->defaultgroup = $conf['defaultgroup'];

        // set capabilities based upon config strings set
        if (empty($this->cnf['server']) || empty($this->cnf['user']) ||
            !isset($this->cnf['password']) || empty($this->cnf['database'])){

                if ($this->cnf['debug']){
                    msg("MySQL err: insufficient configuration.",-1,__LINE__,__FILE__);
                }
                $this->success = false;
                return;
            }

        $this->cando['addUser']      = $this->_chkcnf(array(
            'getUserInfo',
            'getGroups',
            'addUser',
            'getUserID',
            'getGroupID',
            'addGroup',
            'addUserGroup'),true);
        $this->cando['delUser']      = $this->_chkcnf(array(
            'getUserID',
            'delUser',
            'delUserRefs'),true);
        $this->cando['modLogin']     = $this->_chkcnf(array(
            'getUserID',
            'updateUser',
            'UpdateTarget'),true);
        $this->cando['modPass']      = $this->cando['modLogin'];
        $this->cando['modName']      = $this->cando['modLogin'];
        $this->cando['modMail']      = $this->cando['modLogin'];
        $this->cando['modGroups']    = $this->_chkcnf(array(
            'getUserID',
            'getGroups',
            'getGroupID',
            'addGroup',
            'addUserGroup',
            'delGroup',
            'getGroupID',
            'delUserGroup'),true);
        /* getGroups is not yet supported
           $this->cando['getGroups']    = $this->_chkcnf(array('getGroups',
        'getGroupID'),false); */
        $this->cando['getUsers']     = $this->_chkcnf(array(
            'getUsers',
            'getUserInfo',
            'getGroups'),false);
        $this->cando['getUserCount'] = $this->_chkcnf(array('getUsers'),false);


        // FIXME set capabilities accordingly
        $this->cando['addUser']     = true; // can Users be created?
        $this->cando['delUser']     = true; // can Users be deleted?
        $this->cando['modLogin']    = true; // can login names be changed?
        $this->cando['modPass']     = true; // can passwords be changed?
        $this->cando['modName']     = true; // can real names be changed?
        $this->cando['modMail']     = true; // can emails be changed?
        $this->cando['modGroups']   = true; // can groups be changed?
        $this->cando['getUsers']    = true; // can a (filtered) list of users be retrieved?
        $this->cando['getUserCount']= true; // can the number of users be retrieved?
        $this->cando['getGroups']   = true; // can a list of available groups be retrieved?
        $this->cando['external']    = false; // does the module do external auth checking?
        $this->cando['logout']      = true; // can the user logout again? (eg. not possible with HTTP auth)

        // FIXME intialize your auth system and set success to true, if successful
        $this->success = true;


    }

    /**
     * Check if the given config strings are set
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     * @return  bool
     */
    function _chkcnf($keys, $wop=false){
        foreach ($keys as $key){
            if (empty($this->cnf[$key])) return false;
        }

        /* write operation and lock array filled with tables names? */
        if ($wop && (!is_array($this->cnf['TablesToLock']) ||
            !count($this->cnf['TablesToLock']))){
                return false;
            }

        return true;
    }

    /**
     * Checks if the given user exists and the given plaintext password
     * is correct. Furtheron it might be checked wether the user is
     * member of the right group
     *
     * Depending on which SQL string is defined in the config, password
     * checking is done here (getpass) or by the database (passcheck)
     *
     * @param  $user  user who would like access
     * @param  $pass  user's clear text password to check
     * @return bool
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */

    function checkPass($user,$pass){
        $rc  = false;

        if($this->_openDB()) {

            $sql    = str_replace('%{user}',$this->_escape($user),$this->cnf['checkPass']);
            //$sql    = str_replace('%{dgroup}',$this->_escape($this->defaultgroup),$sql);
            $result = $this->_queryDB($sql);
            //echo $sql;

            if($result !== false && count($result) == 1) {
                if($this->cnf['forwardClearPass'] == 0)

                    //echo $result[0]['password'];

                    $sha_info = explode("$",$result[0]['password']);
                if( $sha_info[1] === "SHA" ) {
                    $salt = $sha_info[2];
                    $sha256_password = hash('sha256', $pass);
                    $sha256_password .= $sha_info[2];
                    if( strcasecmp(trim($sha_info[3]),hash('sha256', $sha256_password) ) == 0 ) {
                        $rc = true;
                    }
                }
                //$rc = true;
                else
                    $rc = auth_verifyPassword($pass,$result[0]['pass']);
            }
            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * [public function]
     *
     * Returns info about the given user needs to contain
     * at least these fields:
     *   name  string  full name of the user
     *   mail  string  email addres of the user
     *   grps  array   list of groups the user is in
     *
     * @param $user   user's nick to get data for
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function getUserData($user){
        if($this->_openDB()) {
            $this->_lockTables("READ");
            $info = $this->_getUserInfo($user);
            $this->_unlockTables();
            $this->_closeDB();
        } else
            $info = false;
        return $info;
    }

    /**
     * [public function]
     *
     * Create a new User. Returns false if the user already exists,
     * null when an error occurred and true if everything went well.
     *
     * The new user will be added to the default group by this
     * function if grps are not specified (default behaviour).
     *
     * @param $user  nick of the user
     * @param $pwd   clear text password
     * @param $name  full name of the user
     * @param $mail  email address
     * @param $grps  array of groups the user should become member of
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @author  Chris Smith <chris@jalakai.co.uk>
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function createUser($user,$pwd,$name,$mail,$grps=null){
        if($this->_openDB()) {
            if (($info = $this->_getUserInfo($user)) !== false)
                return false;  // user already exists

            // set defaultgroup if no groups were given
            if ($grps == null)
                $grps = array($this->defaultgroup);

            $this->_lockTables("WRITE");
            $pwd = $this->cnf['forwardClearPass'] ? $pwd : auth_cryptPassword($pwd);
            $rc = $this->_addUser($user,$pwd,$name,$mail,$grps);
            $this->_unlockTables();
            $this->_closeDB();
            if ($rc) return true;
        }
        return null;  // return error
    }

    /**
     * Modify user data [public function]
     *
     * An existing user dataset will be modified. Changes are given in an array.
     *
     * The dataset update will be rejected if the user name should be changed
     * to an already existing one.
     *
     * The password must be provides unencrypted. Pasword cryption is done
     * automatically if configured.
     *
     * If one or more groups could't be updated, an error would be set. In
     * this case the dataset might already be changed and we can't rollback
     * the changes. Transactions would be really usefull here.
     *
     * modifyUser() may be called without SQL statements defined that are
     * needed to change group membership (for example if only the user profile
     * should be modified). In this case we asure that we don't touch groups
     * even $changes['grps'] is set by mistake.
     *
     * @param   $user     nick of the user to be changed
     * @param   $changes  array of field/value pairs to be changed (password
     *                    will be clear text)
     * @return  bool      true on success, false on error
     *
     * @author  Chris Smith <chris@jalakai.co.uk>
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function modifyUser($user, $changes) {
        $rc = false;

        if (!is_array($changes) || !count($changes))
            return true;  // nothing to change

        if($this->_openDB()) {
            $this->_lockTables("WRITE");

            if (($uid = $this->_getUserID($user))) {
                $rc = $this->_updateUserInfo($changes, $uid);

                if ($rc && isset($changes['grps']) && $this->cando['modGroups']) {
                    $groups = $this->_getGroups($user);
                    $grpadd = array_diff($changes['grps'], $groups);
                    $grpdel = array_diff($groups, $changes['grps']);

                    foreach($grpadd as $group)
                        if (($this->_addUserToGroup($user, $group, 1)) == false)
                            $rc = false;

                    foreach($grpdel as $group)
                        if (($this->_delUserFromGroup($user, $group)) == false)
                            $rc = false;
                }
            }

            $this->_unlockTables();
            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * [public function]
     *
     * Remove one or more users from the list of registered users
     *
     * @param   array  $users   array of users to be deleted
     * @return  int             the number of users deleted
     *
     * @author  Christopher Smith <chris@jalakai.co.uk>
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function deleteUsers($users) {
        $count = 0;

        if($this->_openDB()) {
            if (is_array($users) && count($users)) {
                $this->_lockTables("WRITE");
                foreach ($users as $user) {
                    if ($this->_delUser($user))
                        $count++;
                }
                $this->_unlockTables();
            }
            $this->_closeDB();
>>>>>>> 545b3facd206f670a2d26bd70c783dedf84438a9
        }
        return $count;
    }

    /**
<<<<<<< HEAD
     * Bulk retrieval of user data [implement only where required/possible]
     *
     * Set getUsers capability when implemented
     *
     * @param   int $start index of first user to be returned
     * @param   int $limit max number of users to be returned
     * @param   array $filter array of field/pattern pairs, null for no filter
     * @return  array list of userinfo (refer getUserData for internal userinfo details)
     */
    public function retrieveUsers($start = 0, $limit = -1, $filter = null) {
        if($limit < 0) $limit = 10000; // we don't support no limit
        if(is_null($filter)) $filter = array();

        if(isset($filter['grps'])) $filter['group'] = $filter['grps'];
        foreach(array('user', 'name', 'mail', 'group') as $key) {
            if(!isset($filter[$key])) {
                $filter[$key] = '%';
            } else {
                $filter[$key] = '%' . $filter[$key] . '%';
            }
        }
        $filter['start'] = (int) $start;
        $filter['end'] = (int) $start + $limit;
        $filter['limit'] = (int) $limit;

        $result = $this->_query($this->getConf('list-users'), $filter);
        if(!$result) return array();
        $users = array();
        foreach($result as $row) {
            if(!isset($row['user'])) {
                $this->_debug("Statement did not return 'user' attribute", -1, __LINE__);
                return array();
            }
            $users[] = $this->getUserData($row['user']);
        }
        return $users;
    }

    /**
     * Return a count of the number of user which meet $filter criteria
     *
     * @param  array $filter array of field/pattern pairs, empty array for no filter
     * @return int
     */
    public function getUserCount($filter = array()) {
        if(is_null($filter)) $filter = array();

        if(isset($filter['grps'])) $filter['group'] = $filter['grps'];
        foreach(array('user', 'name', 'mail', 'group') as $key) {
            if(!isset($filter[$key])) {
                $filter[$key] = '%';
            } else {
                $filter[$key] = '%' . $filter[$key] . '%';
            }
        }

        $result = $this->_query($this->getConf('count-users'), $filter);
        if(!$result || !isset($result[0]['count'])) {
            $this->_debug("Statement did not return 'count' attribute", -1, __LINE__);
        }
        return (int) $result[0]['count'];
    }

    /**
     * Create a new group with the given name
     *
     * @param string $group
     * @return bool
     */
    public function addGroup($group) {
        $sql = $this->getConf('insert-group');

        $result = $this->_query($sql, array(':group' => $group));
        $this->_clearGroupCache();
        if($result === false) return false;
        return true;
    }

    /**
     * Retrieve groups
     *
     * Set getGroups capability when implemented
     *
     * @param   int $start
     * @param   int $limit
     * @return  array
     */
    public function retrieveGroups($start = 0, $limit = 0) {
        $groups = array_keys($this->_selectGroups());
        if($groups === false) return array();

        if(!$limit) {
            return array_splice($groups, $start);
        } else {
            return array_splice($groups, $start, $limit);
        }
    }

    /**
     * Select data of a specified user
     *
     * @param string $user the user name
     * @return bool|array user data, false on error
     */
    protected function _selectUser($user) {
        $sql = $this->getConf('select-user');

        $result = $this->_query($sql, array(':user' => $user));
        if(!$result) return false;
        $this->_debug('user found', 0, __LINE__);
        if(count($result) > 1) {
            $this->_debug('Found more than one matching user', -1, __LINE__);
            return false;
        }

        $data = array_shift($result);
        $dataok = true;

        if(!isset($data['user'])) {
            $this->_debug("Statement did not return 'user' attribute", -1, __LINE__);
            $dataok = false;
        }
        if(!isset($data['hash']) && !isset($data['clear']) && !$this->_chkcnf(array('check-pass'))) {
            $this->_debug("Statement did not return 'clear' or 'hash' attribute", -1, __LINE__);
            $dataok = false;
        }
        if(!isset($data['name'])) {
            $this->_debug("Statement did not return 'name' attribute", -1, __LINE__);
            $dataok = false;
        }
        if(!isset($data['mail'])) {
            $this->_debug("Statement did not return 'mail' attribute", -1, __LINE__);
            $dataok = false;
        }

        if(!$dataok) return false;
        return $data;
    }

    /**
     * Delete a user after removing all their group memberships
     *
     * @param string $user
     * @return bool true when the user was deleted
     */
    protected function _deleteUser($user) {
        $this->pdo->beginTransaction();
        {
            $userdata = $this->getUserData($user);
            if($userdata === false) goto FAIL;
            $allgroups = $this->_selectGroups();

            // remove group memberships (ignore errors)
            foreach($userdata['grps'] as $group) {
                if(isset($allgroups[$group])) {
                    $this->_leaveGroup($userdata, $allgroups[$group]);
                }
            }

            $ok = $this->_query($this->getConf('delete-user'), $userdata);
            if($ok === false) goto FAIL;
        }
        $this->pdo->commit();
        return true;

        FAIL:
        $this->pdo->rollBack();
=======
     * [public function]
     *
     * Counts users which meet certain $filter criteria.
     *
     * @param  array  $filter  filter criteria in item/pattern pairs
     * @return count of found users.
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function getUserCount($filter=array()) {
        $rc = 0;

        if($this->_openDB()) {
            $sql = $this->_createSQLFilter($this->cnf['getUsers'], $filter);

            if ($this->dbver >= 4) {
                $sql = substr($sql, 6);  /* remove 'SELECT' or 'select' */
                $sql = "SELECT SQL_CALC_FOUND_ROWS".$sql." LIMIT 1";
                $this->_queryDB($sql);
                $result = $this->_queryDB("SELECT FOUND_ROWS()");
                $rc = $result[0]['FOUND_ROWS()'];
            } else if (($result = $this->_queryDB($sql)))
                $rc = count($result);

            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * Bulk retrieval of user data. [public function]
     *
     * @param   first     index of first user to be returned
     * @param   limit     max number of users to be returned
     * @param   filter    array of field/pattern pairs
     * @return  array of userinfo (refer getUserData for internal userinfo details)
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function retrieveUsers($first=0,$limit=10,$filter=array()) {
        $out   = array();

        if($this->_openDB()) {
            $this->_lockTables("READ");
            $sql  = $this->_createSQLFilter($this->cnf['getUsers'], $filter);
            $sql .= " ".$this->cnf['SortOrder']." LIMIT $first, $limit";
            $result = $this->_queryDB($sql);

            if (!empty($result)) {
                foreach ($result as $user)
                    if (($info = $this->_getUserInfo($user['user'])))
                        $out[$user['user']] = $info;
            }

            $this->_unlockTables();
            $this->_closeDB();
        }
        return $out;
    }

    /**
     * Give user membership of a group [public function]
     *
     * @param   $user
     * @param   $group
     * @return  bool    true on success, false on error
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function joinGroup($user, $group) {
        $rc = false;

        if ($this->_openDB()) {
            $this->_lockTables("WRITE");
            $rc  = $this->_addUserToGroup($user, $group);
            $this->_unlockTables();
            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * Remove user from a group [public function]
     *
     * @param   $user    user that leaves a group
     * @param   $group   group to leave
     * @return  bool
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function leaveGroup($user, $group) {
        $rc = false;

        if ($this->_openDB()) {
            $this->_lockTables("WRITE");
            $uid = $this->_getUserID($user);
            $rc  = $this->_delUserFromGroup($user, $group);
            $this->_unlockTables();
            $this->_closeDB();
        }
        return $rc;
    }

    /**
     * MySQL is case-insensitive
     */
    function isCaseSensitive(){
        return false;
    }

    /**
     * Adds a user to a group.
     *
     * If $force is set to '1' non existing groups would be created.
     *
     * The database connection must already be established. Otherwise
     * this function does nothing and returns 'false'. It is strongly
     * recommended to call this function only after all participating
     * tables (group and usergroup) have been locked.
     *
     * @param   $user    user to add to a group
     * @param   $group   name of the group
     * @param   $force   '1' create missing groups
     * @return  bool     'true' on success, 'false' on error
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _addUserToGroup($user, $group, $force=0) {
        $newgroup = 0;

        if (($this->dbcon) && ($user)) {
            $gid = $this->_getGroupID($group);
            if (!$gid) {
                if ($force) {  // create missing groups
                    $sql = str_replace('%{group}',$this->_escape($group),$this->cnf['addGroup']);
                    $gid = $this->_modifyDB($sql);
                    $newgroup = 1;  // group newly created
                }
                if (!$gid) return false; // group didn't exist and can't be created
            }

            $sql = $this->cnf['addUserGroup'];
            if(strpos($sql,'%{uid}') !== false){
                $uid = $this->_getUserID($user);
                $sql = str_replace('%{uid}',  $this->_escape($uid),$sql);
            }
            $sql = str_replace('%{user}', $this->_escape($user),$sql);
            $sql = str_replace('%{gid}',  $this->_escape($gid),$sql);
            $sql = str_replace('%{group}',$this->_escape($group),$sql);
            if ($this->_modifyDB($sql) !== false) return true;

            if ($newgroup) { // remove previously created group on error
                $sql = str_replace('%{gid}',  $this->_escape($gid),$this->cnf['delGroup']);
                $sql = str_replace('%{group}',$this->_escape($group),$sql);
                $this->_modifyDB($sql);
            }
        }
        return false;
    }

    /**
     * Remove user from a group
     *
     * @param   $user    user that leaves a group
     * @param   $group   group to leave
     * @return  bool     true on success, false on error
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _delUserFromGroup($user, $group) {
        $rc = false;

        if (($this->dbcon) && ($user)) {
            $sql = $this->cnf['delUserGroup'];
            if(strpos($sql,'%{uid}') !== false){
                $uid = $this->_getUserID($user);
                $sql = str_replace('%{uid}',  $this->_escape($uid),$sql);
            }
            $gid = $this->_getGroupID($group);
            if ($gid) {
                $sql = str_replace('%{user}', $this->_escape($user),$sql);
                $sql = str_replace('%{gid}',  $this->_escape($gid),$sql);
                $sql = str_replace('%{group}',$this->_escape($group),$sql);
                $rc  = $this->_modifyDB($sql) == 0 ? true : false;
            }
        }
        return $rc;
    }

    /**
     * Retrieves a list of groups the user is a member off.
     *
     * The database connection must already be established
     * for this function to work. Otherwise it will return
     * 'false'.
     *
     * @param  $user  user whose groups should be listed
     * @return bool   false on error
     * @return array  array containing all groups on success
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _getGroups($user) {
        $groups = array();

        if($this->dbcon) {
            $sql = str_replace('%{user}',$this->_escape($user),$this->cnf['getGroups']);
            $result = $this->_queryDB($sql);

            if($result !== false && count($result)) {
                foreach($result as $row)
                    $groups[] = $row['group'];
            }
            $groups[] = $this->defaultgroup;
            return $groups;
        }
        return false;
    }

    /**
     * Retrieves the user id of a given user name
     *
     * The database connection must already be established
     * for this function to work. Otherwise it will return
     * 'false'.
     *
     * @param  $user   user whose id is desired
     * @return user id
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _getUserID($user) {
        if($this->dbcon) {
            $sql = str_replace('%{user}',$this->_escape($user),$this->cnf['getUserID']);
            $result = $this->_queryDB($sql);
            return $result === false ? false : $result[0]['id'];
        }
        return false;
    }

    /**
     * Adds a new User to the database.
     *
     * The database connection must already be established
     * for this function to work. Otherwise it will return
     * 'false'.
     *
     * @param  $user  login of the user
     * @param  $pwd   encrypted password
     * @param  $name  full name of the user
     * @param  $mail  email address
     * @param  $grps  array of groups the user should become member of
     * @return bool
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     * @author  Chris Smith <chris@jalakai.co.uk>
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _addUser($user,$pwd,$name,$mail,$grps){
        if($this->dbcon && is_array($grps)) {
            $sql = str_replace('%{user}', $this->_escape($user),$this->cnf['addUser']);
            $sql = str_replace('%{pass}', $this->_escape($pwd),$sql);
            $sql = str_replace('%{name}', $this->_escape($name),$sql);
            $sql = str_replace('%{email}',$this->_escape($mail),$sql);
            $uid = $this->_modifyDB($sql);

            if ($uid) {
                foreach($grps as $group) {
                    $gid = $this->_addUserToGroup($user, $group, 1);
                    if ($gid === false) break;
                }

                if ($gid) return true;
                else {
                    /* remove the new user and all group relations if a group can't
                     * be assigned. Newly created groups will remain in the database
                     * and won't be removed. This might create orphaned groups but
                     * is not a big issue so we ignore this problem here.
                     */
                    $this->_delUser($user);
                    if ($this->cnf['debug'])
                        msg ("MySQL err: Adding user '$user' to group '$group' failed.",-1,__LINE__,__FILE__);
                }
            }
        }
>>>>>>> 545b3facd206f670a2d26bd70c783dedf84438a9
        return false;
    }

    /**
<<<<<<< HEAD
     * Select all groups of a user
     *
     * @param array $userdata The userdata as returned by _selectUser()
     * @return array|bool list of group names, false on error
     */
    protected function _selectUserGroups($userdata) {
        global $conf;
        $sql = $this->getConf('select-user-groups');
        $result = $this->_query($sql, $userdata);
        if($result === false) return false;

        $groups = array($conf['defaultgroup']); // always add default config
        foreach($result as $row) {
            if(!isset($row['group'])) {
                $this->_debug("No 'group' field returned in select-user-groups statement");
                return false;
            }
            $groups[] = $row['group'];
        }

        $groups = array_unique($groups);
        sort($groups);
        return $groups;
    }

    /**
     * Select all available groups
     *
     * @return array|bool list of all available groups and their properties
     */
    protected function _selectGroups() {
        if($this->groupcache) return $this->groupcache;

        $sql = $this->getConf('select-groups');
        $result = $this->_query($sql);
        if($result === false) return false;

        $groups = array();
        foreach($result as $row) {
            if(!isset($row['group'])) {
                $this->_debug("No 'group' field returned from select-groups statement", -1, __LINE__);
                return false;
            }

            // relayout result with group name as key
            $group = $row['group'];
            $groups[$group] = $row;
        }

        ksort($groups);
        return $groups;
    }

    /**
     * Remove all entries from the group cache
     */
    protected function _clearGroupCache() {
        $this->groupcache = null;
    }

    /**
     * Adds the user to the group
     *
     * @param array $userdata all the user data
     * @param array $groupdata all the group data
     * @return bool
     */
    protected function _joinGroup($userdata, $groupdata) {
        $data = array_merge($userdata, $groupdata);
        $sql = $this->getConf('join-group');
        $result = $this->_query($sql, $data);
        if($result === false) return false;
        return true;
    }

    /**
     * Removes the user from the group
     *
     * @param array $userdata all the user data
     * @param array $groupdata all the group data
     * @return bool
     */
    protected function _leaveGroup($userdata, $groupdata) {
        $data = array_merge($userdata, $groupdata);
        $sql = $this->getConf('leave-group');
        $result = $this->_query($sql, $data);
        if($result === false) return false;
        return true;
    }

    /**
     * Executes a query
     *
     * @param string $sql The SQL statement to execute
     * @param array $arguments Named parameters to be used in the statement
     * @return array|int|bool The result as associative array for SELECTs, affected rows for others, false on error
     */
    protected function _query($sql, $arguments = array()) {
        $sql = trim($sql);
        if(empty($sql)) {
            $this->_debug('No SQL query given', -1, __LINE__);
            return false;
        }

        // execute
        $params = array();
        $sth = $this->pdo->prepare($sql);
        try {
            // prepare parameters - we only use those that exist in the SQL
            foreach($arguments as $key => $value) {
                if(is_array($value)) continue;
                if(is_object($value)) continue;
                if($key[0] != ':') $key = ":$key"; // prefix with colon if needed
                if(strpos($sql, $key) === false) continue; // skip if parameter is missing

                if(is_int($value)) {
                    $sth->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $sth->bindValue($key, $value);
                }
                $params[$key] = $value; //remember for debugging
            }

            $sth->execute();
            if(strtolower(substr($sql, 0, 6)) == 'select') {
                $result = $sth->fetchAll();
            } else {
                $result = $sth->rowCount();
            }
        } catch(Exception $e) {
            // report the caller's line
            $trace = debug_backtrace();
            $line = $trace[0]['line'];
            $dsql = $this->_debugSQL($sql, $params, !defined('DOKU_UNITTEST'));
            $this->_debug($e, -1, $line);
            $this->_debug("SQL: <pre>$dsql</pre>", -1, $line);
            $result = false;
        }
        $sth->closeCursor();
        $sth = null;

        return $result;
    }

    /**
     * Wrapper around msg() but outputs only when debug is enabled
     *
     * @param string|Exception $message
     * @param int $err
     * @param int $line
     */
    protected function _debug($message, $err = 0, $line = 0) {
        if(!$this->getConf('debug')) return;
        if(is_a($message, 'Exception')) {
            $err = -1;
            $msg = $message->getMessage();
            if(!$line) $line = $message->getLine();
        } else {
            $msg = $message;
        }

        if(defined('DOKU_UNITTEST')) {
            printf("\n%s, %s:%d\n", $msg, __FILE__, $line);
        } else {
            msg('authpdo: ' . $msg, $err, $line, __FILE__);
        }
    }

    /**
     * Check if the given config strings are set
     *
     * @author  Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     *
     * @param   string[] $keys
     * @return  bool
     */
    protected function _chkcnf($keys) {
        foreach($keys as $key) {
            $params = explode(':', $key);
            $key = array_shift($params);
            $sql = trim($this->getConf($key));

            // check if sql is set
            if(!$sql) return false;
            // check if needed params are there
            foreach($params as $param) {
                if(strpos($sql, ":$param") === false) return false;
            }
        }

        return true;
    }

    /**
     * create an approximation of the SQL string with parameters replaced
     *
     * @param string $sql
     * @param array $params
     * @param bool $htmlescape Should the result be escaped for output in HTML?
     * @return string
     */
    protected function _debugSQL($sql, $params, $htmlescape = true) {
        foreach($params as $key => $val) {
            if(is_int($val)) {
                $val = $this->pdo->quote($val, PDO::PARAM_INT);
            } elseif(is_bool($val)) {
                $val = $this->pdo->quote($val, PDO::PARAM_BOOL);
            } elseif(is_null($val)) {
                $val = 'NULL';
            } else {
                $val = $this->pdo->quote($val);
            }
            $sql = str_replace($key, $val, $sql);
        }
        if($htmlescape) $sql = hsc($sql);
        return $sql;
    }
}

// vim:ts=4:sw=4:et:
=======
     * Deletes a given user and all his group references.
     *
     * The database connection must already be established
     * for this function to work. Otherwise it will return
     * 'false'.
     *
     * @param  $user   user whose id is desired
     * @return bool
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _delUser($user) {
        if($this->dbcon) {
            $uid = $this->_getUserID($user);
            if ($uid) {
                $sql = str_replace('%{uid}',$this->_escape($uid),$this->cnf['delUserRefs']);
                $this->_modifyDB($sql);
                $sql = str_replace('%{uid}',$this->_escape($uid),$this->cnf['delUser']);
                $sql = str_replace('%{user}',  $this->_escape($user),$sql);
                $this->_modifyDB($sql);
                return true;
            }
        }
        return false;
    }

    /**
     * getUserInfo
     *
     * Gets the data for a specific user The database connection
     * must already be established for this function to work.
     * Otherwise it will return 'false'.
     *
     * @param  $user  user's nick to get data for
     * @return bool   false on error
     * @return array  user info on success
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _getUserInfo($user){
        $sql = str_replace('%{user}',$this->_escape($user),$this->cnf['getUserInfo']);
        $result = $this->_queryDB($sql);
        if($result !== false && count($result)) {
            $info = $result[0];
            $info['grps'] = $this->_getGroups($user);
            //$info['grps'][] = $conf['defaultgroup'];
            return $info;
        }
        return false;
    }

    /**
     * Updates the user info in the database
     *
     * Update a user data structure in the database according changes
     * given in an array. The user name can only be changes if it didn't
     * exists already. If the new user name exists the update procedure
     * will be aborted. The database keeps unchanged.
     *
     * The database connection has already to be established for this
     * function to work. Otherwise it will return 'false'.
     *
     * The password will be crypted if necessary.
     *
     * @param  $changes  array of items to change as pairs of item and value
     * @param  $uid      user id of dataset to change, must be unique in DB
     * @return true on success or false on error
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _updateUserInfo($changes, $uid) {
        $sql  = $this->cnf['updateUser']." ";
        $cnt = 0;
        $err = 0;

        if($this->dbcon) {
            foreach ($changes as $item => $value) {
                if ($item == 'user') {
                    if (($this->_getUserID($changes['user']))) {
                        $err = 1; /* new username already exists */
                        break;    /* abort update */
                    }
                    if ($cnt++ > 0) $sql .= ", ";
                    $sql .= str_replace('%{user}',$value,$this->cnf['UpdateLogin']);
                } else if ($item == 'name') {
                    if ($cnt++ > 0) $sql .= ", ";
                    $sql .= str_replace('%{name}',$value,$this->cnf['UpdateName']);
                } else if ($item == 'pass') {
                    if (!$this->cnf['forwardClearPass'])
                        $salt = substr(md5(uniqid(rand(), true)),0,15);
                    $value = '$SHA$' . $salt . '$' . hash('sha256',hash('sha256',$value) . $salt);
                    if ($cnt++ > 0) $sql .= ", ";
                    $sql .= str_replace('%{pass}',$value,$this->cnf['UpdatePass']);
                } else if ($item == 'mail') {
                    if ($cnt++ > 0) $sql .= ", ";
                    $sql .= str_replace('%{email}',$value,$this->cnf['UpdateEmail']);
                }
            }

            if ($err == 0) {
                if ($cnt > 0) {
                    $sql .= " ".str_replace('%{uid}', $uid, $this->cnf['UpdateTarget']);
                    if(get_class($this) == 'auth_minecraft') $sql .= " LIMIT 1"; //some PgSQL inheritance comp.
                    $this->_modifyDB($sql);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieves the group id of a given group name
     *
     * The database connection must already be established
     * for this function to work. Otherwise it will return
     * 'false'.
     *
     * @param  $group   group name which id is desired
     * @return group id
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _getGroupID($group) {
        if($this->dbcon) {
            $sql = str_replace('%{group}',$this->_escape($group),$this->cnf['getGroupID']);
            $result = $this->_queryDB($sql);
            return $result === false ? false : $result[0]['id'];
        }
        return false;
    }

    /**
     * Opens a connection to a database and saves the handle for further
     * usage in the object. The successful call to this functions is
     * essential for most functions in this object.
     *
     * @return bool
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _openDB() {
        if (!$this->dbcon) {
            $con = @mysql_connect ($this->cnf['server'], $this->cnf['user'], $this->cnf['password']);
            if ($con) {
                if ((mysql_select_db($this->cnf['database'], $con))) {
                    if ((preg_match("/^(\d+)\.(\d+)\.(\d+).*/", mysql_get_server_info ($con), $result)) == 1) {
                        $this->dbver = $result[1];
                        $this->dbrev = $result[2];
                        $this->dbsub = $result[3];
                    }
                    $this->dbcon = $con;
                    if(!empty($this->cnf['charset'])){
                        mysql_query('SET CHARACTER SET "' . $this->cnf['charset'] . '"', $con);
                    }
                    return true;   // connection and database successfully opened
                } else {
                    mysql_close ($con);
                    if ($this->cnf['debug'])
                        msg("MySQL err: No access to database {$this->cnf['database']}.",-1,__LINE__,__FILE__);
                }
            } else if ($this->cnf['debug'])
                msg ("MySQL err: Connection to {$this->cnf['user']}@{$this->cnf['server']} not possible.",
                    -1,__LINE__,__FILE__);

            return false;  // connection failed
        }
        return true;  // connection already open
    }

    /**
     * Closes a database connection.
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _closeDB() {
        if ($this->dbcon) {
            mysql_close ($this->dbcon);
            $this->dbcon = 0;
        }
    }

    /**
     * Sends a SQL query to the database and transforms the result into
     * an associative array.
     *
     * This function is only able to handle queries that returns a
     * table such as SELECT.
     *
     * @param $query  SQL string that contains the query
     * @return array with the result table
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _queryDB($query) {
        if($this->cnf['debug'] >= 2){
            msg('MySQL query: '.hsc($query),0,__LINE__,__FILE__);
        }

        $resultarray = array();
        if ($this->dbcon) {
            $result = @mysql_query($query,$this->dbcon);
            if ($result) {
                while (($t = mysql_fetch_assoc($result)) !== false)
                    $resultarray[]=$t;
                mysql_free_result ($result);
                return $resultarray;
            }
            if ($this->cnf['debug'])
                msg('MySQL err: '.mysql_error($this->dbcon),-1,__LINE__,__FILE__);
        }
        return false;
    }

    /**
     * Sends a SQL query to the database
     *
     * This function is only able to handle queries that returns
     * either nothing or an id value such as INPUT, DELETE, UPDATE, etc.
     *
     * @param $query  SQL string that contains the query
     * @return insert id or 0, false on error
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _modifyDB($query) {
        if ($this->dbcon) {
            $result = @mysql_query($query,$this->dbcon);
            if ($result) {
                $rc = mysql_insert_id($this->dbcon); //give back ID on insert
                if ($rc !== false) return $rc;
            }
            if ($this->cnf['debug'])
                msg('MySQL err: '.mysql_error($this->dbcon),-1,__LINE__,__FILE__);
        }
        return false;
    }

    /**
     * Locked a list of tables for exclusive access so that modifications
     * to the database can't be disturbed by other threads. The list
     * could be set with $conf['auth']['mysql']['TablesToLock'] = array()
     *
     * If aliases for tables are used in SQL statements, also this aliases
     * must be locked. For eg. you use a table 'user' and the alias 'u' in
     * some sql queries, the array must looks like this (order is important):
     *   array("user", "user AS u");
     *
     * MySQL V3 is not able to handle transactions with COMMIT/ROLLBACK
     * so that this functionality is simulated by this function. Nevertheless
     * it is not as powerful as transactions, it is a good compromise in safty.
     *
     * @param $mode  could be 'READ' or 'WRITE'
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _lockTables($mode) {
        if ($this->dbcon) {
            if (is_array($this->cnf['TablesToLock']) && !empty($this->cnf['TablesToLock'])) {
                if ($mode == "READ" || $mode == "WRITE") {
                    $sql = "LOCK TABLES ";
                    $cnt = 0;
                    foreach ($this->cnf['TablesToLock'] as $table) {
                        if ($cnt++ != 0) $sql .= ", ";
                        $sql .= "$table $mode";
                    }
                    $this->_modifyDB($sql);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Unlock locked tables. All existing locks of this thread will be
     * abrogated.
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _unlockTables() {
        if ($this->dbcon) {
            $this->_modifyDB("UNLOCK TABLES");
            return true;
        }
        return false;
    }

    /**
     * Transforms the filter settings in an filter string for a SQL database
     * The database connection must already be established, otherwise the
     * original SQL string without filter criteria will be returned.
     *
     * @param  $sql     SQL string to which the $filter criteria should be added
     * @param  $filter  array of filter criteria as pairs of item and pattern
     * @return SQL string with attached $filter criteria on success
     * @return the original SQL string on error.
     *
     * @author Matthias Grimm <matthiasgrimm@users.sourceforge.net>
     */
    function _createSQLFilter($sql, $filter) {
        $SQLfilter = "";
        $cnt = 0;

        if ($this->dbcon) {
            foreach ($filter as $item => $pattern) {
                $tmp = '%'.$this->_escape($pattern).'%';
                if ($item == 'user') {
                    if ($cnt++ > 0) $SQLfilter .= " AND ";
                    $SQLfilter .= str_replace('%{user}',$tmp,$this->cnf['FilterLogin']);
                } else if ($item == 'name') {
                    if ($cnt++ > 0) $SQLfilter .= " AND ";
                    $SQLfilter .= str_replace('%{name}',$tmp,$this->cnf['FilterName']);
                } else if ($item == 'mail') {
                    if ($cnt++ > 0) $SQLfilter .= " AND ";
                    $SQLfilter .= str_replace('%{email}',$tmp,$this->cnf['FilterEmail']);
                } else if ($item == 'grps') {
                    if ($cnt++ > 0) $SQLfilter .= " AND ";
                    $SQLfilter .= str_replace('%{group}',$tmp,$this->cnf['FilterGroup']);
                }
            }

            // we have to check SQLfilter here and must not use $cnt because if
            // any of cnf['Filter????'] is not defined, a malformed SQL string
            // would be generated.

            if (strlen($SQLfilter)) {
                $glue = strpos(strtolower($sql),"where") ? " AND " : " WHERE ";
                $sql = $sql.$glue.$SQLfilter;
            }
        }

        return $sql;
    }

    /**
     * Escape a string for insertion into the database
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     * @param  string  $string The string to escape
     * @param  boolean $like   Escape wildcard chars as well?
     */
    function _escape($string,$like=false){
        if($this->dbcon){
            $string = mysql_real_escape_string($string, $this->dbcon);
        }else{
            $string = addslashes($string);
        }
        if($like){
            $string = addcslashes($string,'%_');
        }
        return $string;
    }

}

//Setup VIM: ex: et ts=2 :
>>>>>>> 545b3facd206f670a2d26bd70c783dedf84438a9
